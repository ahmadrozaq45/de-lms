<?php

namespace App\Http\Controllers;

use App\Models\{
    Course, CourseEnrollment, Assignment, Submission,
    QuizAttempt, MaterialProgress, Material, User,
    AiAnalysis, Quiz
};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect ke dashboard sesuai role.
     */
    public function index()
    {
        $role       = Auth::user()->role;
        $validRoles = ['admin', 'teacher', 'student'];

        if (!in_array($role, $validRoles)) {
            abort(403, 'Role tidak dikenali.');
        }

        return redirect()->route($role . '.dashboard');
    }

    // =========================================================
    // API DASHBOARD — JSON
    // =========================================================

    /**
     * Dashboard data API untuk Siswa.
     * GET /api/dashboard/student
     */
    public function apiStudent(): JsonResponse
    {
        $user = Auth::user();

        // Kursus yang diikuti
        $enrollments = CourseEnrollment::with('course.modules.materials')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $courseProgress = [];
        $totalCompletedMaterials = 0;
        $totalMaterials          = 0;

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;

            $allMaterialIds = Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))
                ->pluck('id');

            $total     = $allMaterialIds->count();
            $completed = MaterialProgress::where('user_id', $user->id)
                ->whereIn('material_id', $allMaterialIds)
                ->where('is_completed', true)
                ->count();

            $totalMaterials          += $total;
            $totalCompletedMaterials += $completed;

            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;

            $courseProgress[] = [
                'course_id'    => $course->id,
                'course_title' => $course->title,
                'progress'     => $percent,
                'completed'    => $completed,
                'total'        => $total,
            ];
        }

        // Assessment: quiz attempts terbaru
        $recentAttempts = QuizAttempt::with('quiz')
            ->where('user_id', $user->id)
            ->whereNotNull('score')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($a) => [
                'quiz_title'   => $a->quiz->title,
                'quiz_type'    => $a->quiz->type,
                'score'        => $a->score,
                'is_passed'    => $a->is_passed,
                'passing_score'=> $a->quiz->passing_score,
                'completed_at' => $a->completed_at,
            ]);

        // Quiz yang tidak lulus
        $failedQuizzes = QuizAttempt::with('quiz')
            ->where('user_id', $user->id)
            ->where('is_passed', false)
            ->whereNotNull('score')
            ->get()
            ->map(fn($a) => [
                'quiz_id'      => $a->quiz_id,
                'quiz_title'   => $a->quiz->title,
                'score'        => $a->score,
                'passing_score'=> $a->quiz->passing_score,
                'completed_at' => $a->completed_at,
            ]);
        // AI rekomendasi terbaru (semua kursus)
        $aiRecommendations = AiAnalysis::where('user_id', $user->id)
            ->with('course')
            ->latest()
            ->get()
            ->map(fn($a) => [
                'course_title'      => $a->course->title,
                'status_prediction' => $a->status_prediction,
                'recommendation'    => $a->recommendation,
                'generated_at'      => $a->created_at,
            ]);

        $overallProgress = $totalMaterials > 0
            ? round(($totalCompletedMaterials / $totalMaterials) * 100)
            : 0;

        return response()->json([
            'user'             => ['id' => $user->id, 'name' => $user->name],
            'stats'            => [
                'total_courses'   => count($courseProgress),
                'overall_progress'=> $overallProgress,
            ],
            'course_progress'  => $courseProgress,
            'recent_assessment'=> $recentAttempts,
            'failed_quizzes'   => $failedQuizzes,
            'ai_recommendations' => $aiRecommendations,
        ]);
    }

    /**
     * Dashboard data API untuk Guru.
     * GET /api/dashboard/teacher
     */
    public function apiTeacher(): JsonResponse
    {
        $user    = Auth::user();
        $courses = Course::where('teacher_id', $user->id)->get();
        $courseIds = $courses->pluck('id');

        $totalStudents = CourseEnrollment::whereIn('course_id', $courseIds)
            ->distinct('user_id')->count('user_id');

        $totalQuizzes = Quiz::whereIn('course_id', $courseIds)->count();

        $pendingSubmissions = Submission::whereHas('assignment', fn($q) => $q->whereIn('course_id', $courseIds))
            ->where('status', 'pending')
            ->with(['student', 'assignment'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($s) => [
                'id'              => $s->id,
                'student_name'    => $s->student->name,
                'assignment_title'=> $s->assignment->title,
                'submitted_at'    => $s->created_at,
            ]);

        // Progress kelas per kursus
        $classProgress = [];
        foreach ($courses as $course) {
            $enrolled = CourseEnrollment::where('course_id', $course->id)->count();

            $allMaterialIds = Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))
                ->pluck('id');

            $avgProgress = 0;
            if ($enrolled > 0 && $allMaterialIds->count() > 0) {
                $totalPossible = $enrolled * $allMaterialIds->count();
                $totalCompleted = MaterialProgress::whereIn('material_id', $allMaterialIds)
                    ->where('is_completed', true)
                    ->count();
                $avgProgress = round(($totalCompleted / $totalPossible) * 100);
            }

            $failedStudents = QuizAttempt::whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
                ->where('is_passed', false)
                ->whereNotNull('score')
                ->distinct('user_id')
                ->count('user_id');

            $classProgress[] = [
                'course_id'        => $course->id,
                'course_title'     => $course->title,
                'total_students'   => $enrolled,
                'avg_progress'     => $avgProgress,
                'failed_students'  => $failedStudents,
            ];
        }

        return response()->json([
            'stats' => [
                'total_courses'       => $courses->count(),
                'total_students'      => $totalStudents,
                'total_quizzes'       => $totalQuizzes,
                'pending_submissions' => $pendingSubmissions->count(),
            ],
            'class_progress'      => $classProgress,
            'pending_submissions' => $pendingSubmissions,
        ]);
    }

    /**
     * Dashboard data API untuk Admin.
     * GET /api/dashboard/admin
     */
    public function apiAdmin(): JsonResponse
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalCourses  = Course::count();
        $totalQuizzes  = Quiz::count();

        // Quiz tidak lulus (skor < passing_score dalam 30 hari)
        $recentFailures = QuizAttempt::with(['user', 'quiz'])
            ->where('is_passed', false)
            ->whereNotNull('score')
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($a) => [
                'student_name' => $a->user->name,
                'quiz_title'   => $a->quiz->title,
                'score'        => $a->score,
                'passing_score'=> $a->quiz->passing_score,
                'date'         => $a->completed_at,
            ]);

        // Kursus dengan siswa terbanyak
        $topCourses = Course::withCount('enrollments')
            ->with('teacher')
            ->orderByDesc('enrollments_count')
            ->take(5)
            ->get()
            ->map(fn($c) => [
                'course_title'   => $c->title,
                'teacher_name'   => $c->teacher->name ?? '-',
                'total_students' => $c->enrollments_count,
            ]);

        return response()->json([
            'stats' => [
                'total_students' => $totalStudents,
                'total_teachers' => $totalTeachers,
                'total_courses'  => $totalCourses,
                'total_quizzes'  => $totalQuizzes,
            ],
            'recent_failures' => $recentFailures,
            'top_courses'     => $topCourses,
        ]);
    }

    // =========================================================
    // WEB VIEWS (Blade)
    // =========================================================

        public function admin()
    {
        // Statistik utama
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::where('role', 'teacher')->count(),
            'total_courses'  => Course::count(),
            'total_quizzes'  => Quiz::count(),
        ];

        // Registrasi user per bulan (6 bulan terakhir)
        $userGrowth = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);
            return [
                'label'    => $month->translatedFormat('M Y'),
                'students' => User::where('role', 'student')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'teachers' => User::where('role', 'teacher')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        });

        // Top 5 kursus dengan siswa terbanyak
        $topCourses = Course::withCount(['enrollments' => fn($q) => $q->where('status', 'approved')])
            ->with('teacher:id,name')
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get();

        // Recent quiz failures (30 hari terakhir)
        $recentFailures = \App\Models\QuizAttempt::with(['user:id,name', 'quiz:id,title,passing_score'])
            ->where('is_passed', false)
            ->whereNotNull('score')
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->limit(8)
            ->get();

        // Distribusi enrollment per kursus (untuk chart bar)
        $enrollmentStats = Course::withCount(['enrollments' => fn($q) => $q->where('status', 'approved')])
            ->with('teacher:id,name')
            ->orderByDesc('enrollments_count')
            ->limit(7)
            ->get();

        // User terbaru
        $recentUsers = User::latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'userGrowth',
            'topCourses',
            'recentFailures',
            'enrollmentStats',
            'recentUsers',
        ));
    }


    public function teacher()
    {
        $courses   = Course::with('modules')->where('teacher_id', Auth::id())->get();
        $courseIds = $courses->pluck('id');

        $totalStudents = CourseEnrollment::whereIn('course_id', $courseIds)
            ->where('status', 'approved')
            ->distinct('user_id')->count('user_id');

        $pendingApprovals = CourseEnrollment::whereIn('course_id', $courseIds)
            ->where('status', 'pending')
            ->with(['user', 'course'])
            ->latest()
            ->get();

        $totalAssignments = Assignment::whereIn('course_id', $courseIds)->count();

        $pendingSubmissions = Submission::whereHas('assignment.course', fn($q) => $q->whereIn('id', $courseIds))
            ->where('status', 'pending')
            ->with('student')
            ->latest()
            ->get();

        return view('teacher.dashboard', compact(
            'courses',
            'totalStudents',
            'totalAssignments',
            'pendingSubmissions',
            'pendingApprovals',
        ));
    }

    public function student()
    {
        $user = Auth::user();

        // 1. Ambil Kursus yang diikuti (hanya yang sudah diapprove guru)
        $enrolledCourses = CourseEnrollment::with(['course.teacher', 'course.modules.materials'])
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->get();

        // Permintaan pending (menunggu persetujuan)
        $pendingEnrollments = CourseEnrollment::with('course.teacher')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        // 2. Hitung Progress Total & Rata-rata Nilai
        $totalMaterials = 0;
        $completedMaterials = 0;
        $totalScore = 0;
        $attemptCount = 0;

        foreach ($enrolledCourses as $enrollment) {
            $course = $enrollment->course;
            
            // Ambil semua ID materi di kursus ini
            $materialIds = Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))->pluck('id');
            
            $totalMaterials += $materialIds->count();
            $completedMaterials += MaterialProgress::where('user_id', $user->id)
                ->whereIn('material_id', $materialIds)
                ->where('is_completed', true)
                ->count();
        }

        // Hitung Rata-rata Nilai dari Quiz
        $avgGrade = QuizAttempt::where('user_id', $user->id)->avg('score') ?? 0;

        // Hitung Progress Keseluruhan (%)
        $overallProgress = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;

        return view('student.dashboard', compact(
            'user',
            'enrolledCourses',
            'pendingEnrollments',
            'overallProgress',
            'avgGrade',
        ));
    }
}