<?php

namespace App\Services;

use App\Models\{
    User, Course, CourseEnrollment, MaterialProgress,
    QuizAttempt, Submission, Grade, AiAnalysis, Material
};
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Laporan personal siswa (untuk dirinya sendiri).
     * Berisi: progress materi, hasil assessment, badge, rekomendasi AI.
     */
    public function studentPersonalReport(User $student): array
    {
        // Kursus yang diikuti
        $enrollments = CourseEnrollment::with('course.modules.materials')
            ->where('user_id', $student->id)
            ->get();

        $courses = [];
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;

            // Total materi di kursus
            $allMaterialIds = Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))
                ->pluck('id');
            $totalMaterials = $allMaterialIds->count();

            // Materi yang sudah selesai
            $completedMaterials = MaterialProgress::where('user_id', $student->id)
                ->whereIn('material_id', $allMaterialIds)
                ->where('is_completed', true)
                ->count();

            $progressPercent = $totalMaterials > 0
                ? round(($completedMaterials / $totalMaterials) * 100)
                : 0;

            // Quiz attempts di kursus ini
            $quizAttempts = QuizAttempt::with('quiz')
                ->where('user_id', $student->id)
                ->whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
                ->get();

            $quizSummary = $quizAttempts->map(fn($a) => [
                'quiz_title'   => $a->quiz->title,
                'quiz_type'    => $a->quiz->type,
                'score'        => $a->score,
                'is_passed'    => $a->is_passed,
                'passing_score'=> $a->quiz->passing_score,
                'completed_at' => $a->completed_at,
            ]);

            $failedQuizzes = $quizSummary->where('is_passed', false)->values();

            // Submission / Assignment
            $submissions = Submission::with('assignment')
                ->where('student_id', $student->id)
                ->where(function ($q) use ($course) {
                    $q->where('course_id', $course->id)
                      ->orWhereHas('assignment', fn($q2) => $q2->where('course_id', $course->id));
                })
                ->get();

            $assignmentSummary = $submissions->map(fn($s) => [
                'assignment_title'  => $s->assignment->title,
                'status'            => $s->status,
                'score'             => $s->score,
                'ai_feedback'       => $s->ai_feedback,
                'teacher_feedback'  => $s->teacher_feedback,
            ]);

            // AI rekomendasi terbaru
            $latestAi = AiAnalysis::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->latest()
                ->first();

            $courses[] = [
                'course_id'          => $course->id,
                'course_title'       => $course->title,
                'enrolled_at'        => $enrollment->created_at,
                'material_progress'  => [
                    'completed'  => $completedMaterials,
                    'total'      => $totalMaterials,
                    'percent'    => $progressPercent,
                ],
                'quiz_summary'       => $quizSummary,
                'failed_quizzes'     => $failedQuizzes,
                'assignment_summary' => $assignmentSummary,
                'ai_recommendation'  => $latestAi ? [
                    'status_prediction' => $latestAi->status_prediction,
                    'recommendation'    => $latestAi->recommendation,
                    'generated_at'      => $latestAi->created_at,
                ] : null,
            ];
        }

        // Badge
        $badges = $student->badges()->orderByPivot('earned_at', 'desc')->get()->map(fn($b) => [
            'name'       => $b->name,
            'icon'       => $b->icon,
            'description'=> $b->description,
            'earned_at'  => $b->pivot->earned_at,
        ]);

        return [
            'student' => [
                'id'    => $student->id,
                'name'  => $student->name,
                'email' => $student->email,
            ],
            'total_enrolled_courses' => count($courses),
            'badges'                 => $badges,
            'courses'                => $courses,
        ];
    }

    /**
     * Laporan guru: semua siswa di semua kursus yang diajar guru tersebut.
     */
    public function teacherReport(User $teacher): array
    {
        $courses = Course::with('enrollments.user')->where('teacher_id', $teacher->id)->get();

        $result = [];
        foreach ($courses as $course) {
            $students = [];
            foreach ($course->enrollments as $enrollment) {
                $student = $enrollment->user;
                $students[] = $this->buildStudentCourseSummary($student, $course);
            }

            $result[] = [
                'course_id'    => $course->id,
                'course_title' => $course->title,
                'total_students' => count($students),
                'students'     => $students,
            ];
        }

        return [
            'teacher' => [
                'id'   => $teacher->id,
                'name' => $teacher->name,
            ],
            'total_courses'  => count($result),
            'course_reports' => $result,
        ];
    }

    /**
     * Laporan admin: semua siswa, semua kelas.
     */
    public function adminReport(): array
    {
        $courses = Course::with(['teacher', 'enrollments.user'])->get();

        $result = [];
        foreach ($courses as $course) {
            $students = [];
            foreach ($course->enrollments as $enrollment) {
                $student = $enrollment->user;
                $students[] = $this->buildStudentCourseSummary($student, $course);
            }

            $result[] = [
                'course_id'    => $course->id,
                'course_title' => $course->title,
                'teacher_name' => $course->teacher->name ?? '-',
                'total_students' => count($students),
                'students'     => $students,
            ];
        }

        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalCourses  = Course::count();

        return [
            'summary' => [
                'total_students' => $totalStudents,
                'total_teachers' => $totalTeachers,
                'total_courses'  => $totalCourses,
            ],
            'course_reports' => $result,
        ];
    }

    /**
     * Ringkasan satu siswa di satu kursus (digunakan oleh teacher & admin report).
     */
    private function buildStudentCourseSummary(User $student, Course $course): array
    {
        $allMaterialIds = Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))
            ->pluck('id');
        $totalMaterials = $allMaterialIds->count();

        $completedMaterials = MaterialProgress::where('user_id', $student->id)
            ->whereIn('material_id', $allMaterialIds)
            ->where('is_completed', true)
            ->count();

        $progressPercent = $totalMaterials > 0
            ? round(($completedMaterials / $totalMaterials) * 100)
            : 0;

        // Rata-rata skor quiz
        $avgQuizScore = QuizAttempt::where('user_id', $student->id)
            ->whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
            ->whereNotNull('score')
            ->avg('score');

        // Jumlah quiz tidak lulus
        $failedQuizCount = QuizAttempt::where('user_id', $student->id)
            ->whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
            ->where('is_passed', false)
            ->whereNotNull('score')
            ->count();

        // AI rekomendasi terbaru
        $latestAi = AiAnalysis::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->latest()
            ->first();

        return [
            'student_id'         => $student->id,
            'student_name'       => $student->name,
            'student_email'      => $student->email,
            'material_progress'  => [
                'completed' => $completedMaterials,
                'total'     => $totalMaterials,
                'percent'   => $progressPercent,
            ],
            'avg_quiz_score'     => $avgQuizScore !== null ? round($avgQuizScore, 1) : null,
            'failed_quiz_count'  => $failedQuizCount,
            'ai_status'          => $latestAi?->status_prediction,
            'ai_recommendation'  => $latestAi?->recommendation,
        ];
    }
}
