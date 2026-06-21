<?php

namespace App\Http\Controllers;

use App\Models\{User, Course, CourseEnrollment, Submission, QuizAttempt, MaterialProgress, Material};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // ── ADMIN: Ringkasan seluruh platform ──
    public function admin(Request $request)
    {
        $totalUsers    = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalCourses  = Course::count();

        $totalEnrollments  = CourseEnrollment::where('status', 'approved')->count();
        $totalSubmissions  = Submission::count();
        $totalQuizAttempts = QuizAttempt::whereNotNull('score')->count();
        $avgQuizScore      = QuizAttempt::whereNotNull('score')->avg('score') ?? 0;

        // Filter dari request
        $filterSiswa   = trim($request->input('filter_siswa', ''));
        $filterGuru    = trim($request->input('filter_guru', ''));
        $filterDateFrom = trim($request->input('filter_date_from', ''));
        $filterDateTo   = trim($request->input('filter_date_to', ''));

        // Query kursus dengan filter guru (server-side)
        $coursesQuery = Course::withCount(['enrollments' => fn($q) => $q->where('status', 'approved')])
            ->with('teacher:id,name')
            ->orderByDesc('enrollments_count');

        if ($filterGuru !== '') {
            $coursesQuery->whereHas('teacher', fn($q) => $q->where('name', 'like', "%{$filterGuru}%"));
        }

        // Filter kursus berdasarkan tanggal dibuat
        if ($filterDateFrom !== '') {
            $coursesQuery->whereDate('created_at', '>=', $filterDateFrom);
        }
        if ($filterDateTo !== '') {
            $coursesQuery->whereDate('created_at', '<=', $filterDateTo);
        }

        $allCourses = $coursesQuery->get();

        // Top 5 untuk ringkasan (tidak terpengaruh filter)
        $topCourses = Course::withCount(['enrollments' => fn($q) => $q->where('status', 'approved')])
            ->with('teacher:id,name')
            ->orderByDesc('enrollments_count')
            ->limit(5)->get();

        $topTeachers = User::where('role', 'teacher')
            ->withCount('teacherCourses')
            ->orderByDesc('teacher_courses_count')
            ->limit(5)->get();

        $submissionStats = [
            'pending'  => Submission::where('status', 'pending')->count(),
            'graded'   => Submission::where('status', 'graded')->count(),
            'reviewed' => Submission::where('status', 'reviewed')->count(),
        ];

        return view('admin.report', compact(
            'totalUsers', 'totalStudents', 'totalTeachers', 'totalCourses',
            'totalEnrollments', 'totalSubmissions', 'totalQuizAttempts', 'avgQuizScore',
            'allCourses', 'topCourses', 'topTeachers', 'submissionStats',
            'filterSiswa', 'filterGuru', 'filterDateFrom', 'filterDateTo'
        ));
    }

    // ── TEACHER: Laporan kelas-kelas milik guru ──
    public function teacher()
    {
        $user    = Auth::user();
        $courses = Course::where('teacher_id', $user->id)
            ->withCount(['enrollments as student_count' => fn($q) => $q->where('status', 'approved')])
            ->with('modules')
            ->get();

        $courseIds = $courses->pluck('id');

        $courseStats = $courses->map(function ($course) {
            $submissions  = Submission::where('course_id', $course->id)->get();
            $quizAttempts = QuizAttempt::whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
                ->whereNotNull('score')->get();

            return [
                'course'      => $course,
                'enrolled'    => $course->student_count,
                'submissions' => $submissions->count(),
                'pending'     => $submissions->where('status', 'pending')->count(),
                'graded'      => $submissions->where('status', 'graded')->count(),
                'quiz_count'  => $quizAttempts->count(),
                'avg_score'   => $quizAttempts->count() ? round($quizAttempts->avg('score')) : null,
                'pass_count'  => $quizAttempts->where('is_passed', true)->count(),
            ];
        });

        $totals = [
            'courses'     => $courses->count(),
            'students'    => $courses->sum('student_count'),
            'submissions' => Submission::whereIn('course_id', $courseIds)->count(),
            'pending'     => Submission::whereIn('course_id', $courseIds)->where('status', 'pending')->count(),
        ];

        // ── Data per-siswa untuk panel AI Summarize ──
        $studentsByCourse = $courses->map(function ($course) {
            $enrolled = \App\Models\CourseEnrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->with('user')
                ->get();

            $students = $enrolled->map(function ($enrollment) use ($course) {
                $student = $enrollment->user;

                $matIds = Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))->pluck('id');
                $total  = $matIds->count();
                $done   = MaterialProgress::where('user_id', $student->id)
                    ->whereIn('material_id', $matIds)->where('is_completed', true)->count();
                $pct    = $total > 0 ? round($done / $total * 100) : 0;

                $avgQ = QuizAttempt::where('user_id', $student->id)
                    ->whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
                    ->whereNotNull('score')->avg('score');

                $ai = \App\Models\AiAnalysis::where('user_id', $student->id)
                    ->where('course_id', $course->id)->latest()->first();

                return [
                    'student' => $student,
                    'percent' => $pct,
                    'avg_quiz'=> $avgQ !== null ? round($avgQ, 1) : null,
                    'ai'      => $ai,
                ];
            });

            return [
                'course'   => $course,
                'students' => $students,
            ];
        })->filter(fn($c) => $c['students']->isNotEmpty())->values();

        return view('teacher.report', compact('courseStats', 'totals', 'studentsByCourse'));
    }

    // ── STUDENT: Laporan progress belajar siswa sendiri ──
    public function student()
    {
        $user = Auth::user();

        $enrollments = CourseEnrollment::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with('course.teacher')
            ->get();

        $courseReports = $enrollments->map(function ($enrollment) use ($user) {
            $course = $enrollment->course;

            $materialIds = Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))
                ->pluck('id');
            $total     = $materialIds->count();
            $completed = MaterialProgress::where('user_id', $user->id)
                ->whereIn('material_id', $materialIds)
                ->where('is_completed', true)->count();
            $progress  = $total > 0 ? round($completed / $total * 100) : 0;

            $submissions = Submission::where('student_id', $user->id)
                ->where('course_id', $course->id)->with('assignment')->get();

            $quizAttempts = QuizAttempt::where('user_id', $user->id)
                ->whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
                ->whereNotNull('score')
                ->with('quiz')->get();

            return [
                'course'        => $course,
                'progress'      => $progress,
                'completed'     => $completed,
                'total_mat'     => $total,
                'submissions'   => $submissions,
                'quiz_attempts' => $quizAttempts,
                'avg_quiz'      => $quizAttempts->count() ? round($quizAttempts->avg('score')) : null,
                'joined_at'     => $enrollment->created_at,
            ];
        });

        $overallProgress = $courseReports->count()
            ? round($courseReports->avg('progress')) : 0;
        $avgQuiz = $courseReports->filter(fn($r) => $r['avg_quiz'])->avg('avg_quiz');

        return view('student.report', compact('courseReports', 'overallProgress', 'avgQuiz'));
    }
}