<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect ke dashboard sesuai role user yang login.
     * GET /dashboard
     */
    public function index()
    {
        $role        = Auth::user()->role;
        $validRoles  = ['admin', 'teacher', 'student'];

        if (!in_array($role, $validRoles)) {
            abort(403, 'Role tidak dikenali.');
        }

        return redirect()->route($role . '.dashboard');
    }

    /**
     * Dashboard Admin.
     * GET /admin/dashboard
     */
    public function admin()
    {
        return view('admin.dashboard');
    }

    /**
     * Dashboard Guru — statistik + pending review + daftar kursus.
     * GET /teacher/dashboard
     */
    public function teacher()
    {
        $courses   = Course::with('modules')->where('teacher_id', Auth::id())->get();
        $courseIds = $courses->pluck('id');

        $totalStudents    = CourseEnrollment::whereIn('course_id', $courseIds)
                                ->distinct('user_id')->count('user_id');
        $totalAssignments = Assignment::whereIn('course_id', $courseIds)->count();

        $pendingSubmissions = Submission::whereHas('assignment.course', function ($q) use ($courseIds) {
            $q->whereIn('id', $courseIds);
        })->where('status', 'pending')->with('student')->latest()->get();

        $pendingReviews = $pendingSubmissions->count();

        return view('teacher.dashboard', compact(
            'courses',
            'totalStudents',
            'totalAssignments',
            'pendingSubmissions',
            'pendingReviews'
        ));
    }

    /**
     * Dashboard Siswa — daftar semua kursus + info user.
     * GET /student/dashboard
     */
    public function student()
    {
        $courses      = Course::latest()->get();
        $totalCourses = $courses->count();
        $user         = Auth::user();

        return view('student.dashboard', compact('courses', 'totalCourses', 'user'));
    }
}
