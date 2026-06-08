<?php

namespace App\Services;

use App\Models\AiRecommendation;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * RecommendationService
 *
 * Generates personalised study recommendations by scoring a learner's
 * performance data across four signals:
 *   1. Nilai   – average grade per topic/course
 *   2. Quiz    – quiz attempt history and accuracy
 *   3. Tugas   – assignment submission rate & scores
 *   4. Progress – completion percentage per course/chapter
 *
 * Each signal contributes a weighted partial score; the combined score
 * determines which recommendations are surfaced and in what order.
 */
class RecommendationService
{
    // ── Signal weights (must sum to 1.0) ─────────────────────────────────────
    private const WEIGHT_NILAI    = 0.30;
    private const WEIGHT_QUIZ     = 0.25;
    private const WEIGHT_TUGAS    = 0.25;
    private const WEIGHT_PROGRESS = 0.20;

    // ── Thresholds ────────────────────────────────────────────────────────────
    private const WEAK_SCORE_THRESHOLD    = 65.0;  // below → remedial recommendation
    private const STRONG_SCORE_THRESHOLD  = 80.0;  // above → accelerate recommendation
    private const LOW_PROGRESS_THRESHOLD  = 40.0;  // below → resume recommendation
    private const HIGH_COMPLETION         = 80.0;  // above → next course recommendation

    /**
     * Generate (or refresh) all active recommendations for a user.
     * Old non-clicked recommendations are pruned before generation.
     */
    public function generateForUser(User $user): Collection
    {
        DB::beginTransaction();
        try {
            // Remove stale active recommendations
            AiRecommendation::forUser($user->id)
                ->active()
                ->where('is_clicked', false)
                ->delete();

            $profile = $this->buildLearnerProfile($user);
            $recommendations = collect();

            $recommendations = $recommendations->merge($this->recommendNextMaterial($user, $profile));
            $recommendations = $recommendations->merge($this->recommendTopics($user, $profile));
            $recommendations = $recommendations->merge($this->recommendCourses($user, $profile));
            $recommendations = $recommendations->merge($this->recommendPractice($user, $profile));

            DB::commit();
            return $recommendations->sortByDesc('score')->values();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('RecommendationService::generateForUser failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return collect();
        }
    }

    // ── Learner profile builder ───────────────────────────────────────────────

    /**
     * Aggregate all performance signals into a single associative array.
     * Adapt the DB queries below to match your actual table/column names.
     */
    private function buildLearnerProfile(User $user): array
    {
        $userId = $user->id;

        // ── Nilai (grades) ────────────────────────────────────────────────────
        // Expected tables: course_enrollments, grades (user_id, course_id, nilai)
        $nilaiStats = DB::table('grades')
            ->where('user_id', $userId)
            ->selectRaw('AVG(nilai) as avg_nilai, MIN(nilai) as min_nilai, COUNT(*) as total_nilai')
            ->first();

        // Per-course average so we can spot which courses are weak
        $nilaiPerCourse = DB::table('grades')
            ->where('user_id', $userId)
            ->selectRaw('course_id, AVG(nilai) as avg')
            ->groupBy('course_id')
            ->pluck('avg', 'course_id')
            ->toArray();

        // ── Quiz ──────────────────────────────────────────────────────────────
        // Expected tables: quiz_results (user_id, quiz_id, score, created_at)
        $quizStats = DB::table('quiz_results')
            ->where('user_id', $userId)
            ->selectRaw('AVG(score) as avg_score, COUNT(*) as total_attempts, MAX(created_at) as last_quiz')
            ->first();

        $quizPerCourse = DB::table('quiz_results as qr')
            ->join('quizzes as q', 'qr.quiz_id', '=', 'q.id')
            ->where('qr.user_id', $userId)
            ->selectRaw('q.course_id, AVG(qr.score) as avg')
            ->groupBy('q.course_id')
            ->pluck('avg', 'course_id')
            ->toArray();

        // ── Tugas (assignments) ───────────────────────────────────────────────
        // Expected tables: assignment_submissions (user_id, assignment_id, score, submitted_at)
        //                  assignments (id, course_id)
        $tugasStats = DB::table('assignment_submissions as sub')
            ->join('assignments as a', 'sub.assignment_id', '=', 'a.id')
            ->where('sub.user_id', $userId)
            ->selectRaw('AVG(sub.score) as avg_score, COUNT(*) as total_submitted')
            ->first();

        // Count due assignments user hasn't submitted
        $pendingTugas = DB::table('assignments as a')
            ->join('course_enrollments as e', 'a.course_id', '=', 'e.course_id')
            ->leftJoin('assignment_submissions as sub', function ($join) use ($userId) {
                $join->on('sub.assignment_id', '=', 'a.id')
                     ->where('sub.user_id', $userId);
            })
            ->where('e.user_id', $userId)
            ->whereNull('sub.id')
            ->count();

        // ── Progress ──────────────────────────────────────────────────────────
        // Expected tables: user_progress (user_id, course_id, progress_pct, last_activity_at)
        $progressStats = DB::table('user_progress')
            ->where('user_id', $userId)
            ->selectRaw('AVG(progress_pct) as avg_progress, MAX(last_activity_at) as last_activity')
            ->first();

        $progressPerCourse = DB::table('user_progress')
            ->where('user_id', $userId)
            ->pluck('progress_pct', 'course_id')
            ->toArray();

        // ── Enrolled courses ──────────────────────────────────────────────────
        $enrolledCourseIds = DB::table('course_enrollments')
            ->where('user_id', $userId)
            ->pluck('course_id')
            ->toArray();

        // ── Composite score ───────────────────────────────────────────────────
        $compositeScore = $this->computeCompositeScore(
            nilaiAvg:    (float) ($nilaiStats->avg_nilai ?? 0),
            quizAvg:     (float) ($quizStats->avg_score ?? 0),
            tugasAvg:    (float) ($tugasStats->avg_score ?? 0),
            progressAvg: (float) ($progressStats->avg_progress ?? 0),
        );

        return compact(
            'nilaiStats', 'nilaiPerCourse',
            'quizStats', 'quizPerCourse',
            'tugasStats', 'pendingTugas',
            'progressStats', 'progressPerCourse',
            'enrolledCourseIds', 'compositeScore'
        );
    }

    /**
     * Weighted composite score (0–100).
     */
    private function computeCompositeScore(
        float $nilaiAvg,
        float $quizAvg,
        float $tugasAvg,
        float $progressAvg
    ): float {
        return round(
            $nilaiAvg    * self::WEIGHT_NILAI    +
            $quizAvg     * self::WEIGHT_QUIZ     +
            $tugasAvg    * self::WEIGHT_TUGAS    +
            $progressAvg * self::WEIGHT_PROGRESS,
            2
        );
    }

    // ── Recommendation generators ─────────────────────────────────────────────

    /**
     * TYPE: next_material
     * Surface the next incomplete chapter/material in the user's most active course.
     */
    private function recommendNextMaterial(User $user, array $p): Collection
    {
        $recommendations = collect();

        foreach ($p['progressPerCourse'] as $courseId => $progress) {
            if ($progress >= self::HIGH_COMPLETION) {
                continue; // already finished
            }

            // Find next incomplete chapter
            $nextChapter = DB::table('chapters as c')
                ->join('courses as co', 'c.course_id', '=', 'co.id')
                ->leftJoin('user_chapter_progress as ucp', function ($j) use ($user) {
                    $j->on('ucp.chapter_id', '=', 'c.id')
                      ->where('ucp.user_id', $user->id);
                })
                ->where('c.course_id', $courseId)
                ->where(function ($q) {
                    $q->whereNull('ucp.completed_at')
                      ->orWhere('ucp.is_completed', false);
                })
                ->orderBy('c.order')
                ->select('c.id', 'c.title', 'co.title as course_title', 'c.course_id')
                ->first();

            if (!$nextChapter) {
                continue;
            }

            $score = $this->scoreNextMaterial($progress, $p['compositeScore']);

            $recommendations->push(AiRecommendation::create([
                'user_id'     => $user->id,
                'type'        => AiRecommendation::TYPE_NEXT_MATERIAL,
                'title'       => "Lanjutkan: {$nextChapter->title}",
                'description' => "Kamu sudah {$progress}% selesai di course \"{$nextChapter->course_title}\". Lanjutkan ke bab berikutnya!",
                'score'       => $score,
                'basis'       => [
                    'course_id'    => $courseId,
                    'progress_pct' => $progress,
                    'signal'       => 'progress',
                ],
                'target_type' => 'chapter',
                'target_id'   => $nextChapter->id,
                'expires_at'  => now()->addDays(3),
            ]));
        }

        return $recommendations;
    }

    /**
     * TYPE: topic
     * Recommend study topics where the learner is underperforming.
     */
    private function recommendTopics(User $user, array $p): Collection
    {
        $recommendations = collect();

        // Weak quiz performance per course → recommend review topic
        foreach ($p['quizPerCourse'] as $courseId => $avgScore) {
            if ($avgScore >= self::WEAK_SCORE_THRESHOLD) {
                continue;
            }

            $course = DB::table('courses')->find($courseId);
            if (!$course) {
                continue;
            }

            // Find which quiz categories/topics user struggled with
            $weakTopics = DB::table('quiz_results as qr')
                ->join('quizzes as q', 'qr.quiz_id', '=', 'q.id')
                ->where('qr.user_id', $user->id)
                ->where('q.course_id', $courseId)
                ->where('qr.score', '<', self::WEAK_SCORE_THRESHOLD)
                ->select('q.topic', DB::raw('AVG(qr.score) as avg'))
                ->groupBy('q.topic')
                ->orderBy('avg')
                ->limit(2)
                ->get();

            $topicList = $weakTopics->pluck('topic')->filter()->implode(', ') ?: $course->title;
            $score = $this->scoreWeakTopic($avgScore);

            $recommendations->push(AiRecommendation::create([
                'user_id'     => $user->id,
                'type'        => AiRecommendation::TYPE_TOPIC,
                'title'       => "Review Topik: {$topicList}",
                'description' => "Nilai quiz kamu di \"{$course->title}\" rata-rata {$avgScore}. Coba perkuat pemahaman topik ini sebelum lanjut.",
                'score'       => $score,
                'basis'       => [
                    'course_id'      => $courseId,
                    'avg_quiz_score' => $avgScore,
                    'weak_topics'    => $weakTopics->pluck('topic')->toArray(),
                    'signal'         => 'quiz',
                ],
                'target_type' => 'course',
                'target_id'   => $courseId,
                'expires_at'  => now()->addDays(5),
            ]));
        }

        // Weak nilai (grades) per course
        foreach ($p['nilaiPerCourse'] as $courseId => $avgNilai) {
            if ($avgNilai >= self::WEAK_SCORE_THRESHOLD) {
                continue;
            }

            $course = DB::table('courses')->find($courseId);
            if (!$course) {
                continue;
            }

            // Avoid duplicate if quiz already created a topic rec for same course
            if ($recommendations->where('target_id', $courseId)->where('type', AiRecommendation::TYPE_TOPIC)->isNotEmpty()) {
                continue;
            }

            $recommendations->push(AiRecommendation::create([
                'user_id'     => $user->id,
                'type'        => AiRecommendation::TYPE_TOPIC,
                'title'       => "Tingkatkan Nilai: {$course->title}",
                'description' => "Nilai kamu di course ini masih {$avgNilai}. Fokus pelajari materi inti untuk meningkatkan pemahaman.",
                'score'       => $this->scoreWeakTopic($avgNilai),
                'basis'       => [
                    'course_id' => $courseId,
                    'avg_nilai' => $avgNilai,
                    'signal'    => 'nilai',
                ],
                'target_type' => 'course',
                'target_id'   => $courseId,
                'expires_at'  => now()->addDays(5),
            ]));
        }

        return $recommendations;
    }

    /**
     * TYPE: course
     * Suggest relevant new courses based on completion + performance signals.
     */
    private function recommendCourses(User $user, array $p): Collection
    {
        $recommendations = collect();

        // High performers or users who completed most enrolled courses
        $completedCount = collect($p['progressPerCourse'])
            ->filter(fn($pct) => $pct >= self::HIGH_COMPLETION)
            ->count();

        if ($completedCount === 0 && $p['compositeScore'] < self::STRONG_SCORE_THRESHOLD) {
            return $recommendations;
        }

        // Find courses in same categories that user hasn't enrolled in
        $enrolledIds = $p['enrolledCourseIds'];

        $suggested = DB::table('courses as c')
            ->join('course_categories as cc', 'c.category_id', '=', 'cc.id')
            ->whereNotIn('c.id', $enrolledIds ?: [0])
            ->where('c.is_published', true)
            ->orderByDesc('c.rating')
            ->limit(3)
            ->select('c.id', 'c.title', 'c.description', 'cc.name as category')
            ->get();

        foreach ($suggested as $course) {
            $score = min(95, $p['compositeScore'] * 0.9 + 10);

            $recommendations->push(AiRecommendation::create([
                'user_id'     => $user->id,
                'type'        => AiRecommendation::TYPE_COURSE,
                'title'       => $course->title,
                'description' => "Course baru di kategori \"{$course->category}\" yang relevan dengan perjalanan belajarmu.",
                'score'       => $score,
                'basis'       => [
                    'completed_courses' => $completedCount,
                    'composite_score'   => $p['compositeScore'],
                    'category'          => $course->category,
                    'signal'            => 'progress+nilai',
                ],
                'target_type' => 'course',
                'target_id'   => $course->id,
                'expires_at'  => now()->addDays(7),
            ]));
        }

        return $recommendations;
    }

    /**
     * TYPE: practice
     * Suggest practice quizzes or pending assignments.
     */
    private function recommendPractice(User $user, array $p): Collection
    {
        $recommendations = collect();

        // Pending assignments
        if ($p['pendingTugas'] > 0) {
            $nextTugas = DB::table('assignments as a')
                ->join('course_enrollments as e', 'a.course_id', '=', 'e.course_id')
                ->join('courses as c', 'a.course_id', '=', 'c.id')
                ->leftJoin('assignment_submissions as sub', function ($j) use ($user) {
                    $j->on('sub.assignment_id', '=', 'a.id')
                      ->where('sub.user_id', $user->id);
                })
                ->where('e.user_id', $user->id)
                ->whereNull('sub.id')
                ->select('a.id', 'a.title', 'c.title as course_title', 'a.due_date')
                ->orderBy('a.due_date')
                ->first();

            if ($nextTugas) {
                $dueText = $nextTugas->due_date
                    ? 'Deadline: ' . \Carbon\Carbon::parse($nextTugas->due_date)->diffForHumans()
                    : 'Segera dikerjakan';

                $recommendations->push(AiRecommendation::create([
                    'user_id'     => $user->id,
                    'type'        => AiRecommendation::TYPE_PRACTICE,
                    'title'       => "Tugas: {$nextTugas->title}",
                    'description' => "Kamu punya {$p['pendingTugas']} tugas belum dikumpulkan di \"{$nextTugas->course_title}\". {$dueText}.",
                    'score'       => 90.0, // high priority
                    'basis'       => [
                        'pending_count' => $p['pendingTugas'],
                        'signal'        => 'tugas',
                    ],
                    'target_type' => 'assignment',
                    'target_id'   => $nextTugas->id,
                    'expires_at'  => now()->addDays(1),
                ]));
            }
        }

        // Low quiz score → recommend retake
        $avgQuiz = (float) ($p['quizStats']->avg_score ?? 0);
        if ($avgQuiz > 0 && $avgQuiz < self::WEAK_SCORE_THRESHOLD) {
            $practiceQuiz = DB::table('quizzes as q')
                ->join('quiz_results as qr', function ($j) use ($user) {
                    $j->on('qr.quiz_id', '=', 'q.id')
                      ->where('qr.user_id', $user->id);
                })
                ->where('q.allows_retake', true)
                ->orderBy('qr.score')
                ->select('q.id', 'q.title', DB::raw('MIN(qr.score) as worst_score'))
                ->groupBy('q.id', 'q.title')
                ->first();

            if ($practiceQuiz) {
                $recommendations->push(AiRecommendation::create([
                    'user_id'     => $user->id,
                    'type'        => AiRecommendation::TYPE_PRACTICE,
                    'title'       => "Ulangi Quiz: {$practiceQuiz->title}",
                    'description' => "Nilai quiz kamu sebelumnya {$practiceQuiz->worst_score}. Coba lagi untuk meningkatkan pemahaman!",
                    'score'       => $this->scoreWeakTopic($avgQuiz),
                    'basis'       => [
                        'avg_quiz' => $avgQuiz,
                        'signal'   => 'quiz',
                    ],
                    'target_type' => 'quiz',
                    'target_id'   => $practiceQuiz->id,
                    'expires_at'  => now()->addDays(2),
                ]));
            }
        }

        return $recommendations;
    }

    // ── Scoring helpers ───────────────────────────────────────────────────────

    private function scoreNextMaterial(float $progress, float $composite): float
    {
        // Higher score for users who are in the middle of a course (30–70%)
        $progressBonus = $progress >= 30 && $progress <= 70 ? 10 : 0;
        return min(95, $composite * 0.7 + $progressBonus + 15);
    }

    private function scoreWeakTopic(float $avgScore): float
    {
        // Weaker performance → higher priority recommendation
        return max(50, 100 - $avgScore);
    }

    // ── Feedback recording ────────────────────────────────────────────────────

    public function recordFeedback(int $recommendationId, int $userId, string $action, ?int $rating = null): void
    {
        $recommendation = AiRecommendation::find($recommendationId);
        if (!$recommendation || $recommendation->user_id !== $userId) {
            return;
        }

        \App\Models\RecommendationFeedback::create([
            'recommendation_id' => $recommendationId,
            'user_id'           => $userId,
            'action'            => $action,
            'rating'            => $rating,
        ]);

        match ($action) {
            'clicked'   => $recommendation->update(['is_clicked' => true]),
            'dismissed' => $recommendation->update(['is_dismissed' => true]),
            default     => null,
        };
    }
}
