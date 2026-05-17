<?php

namespace App\Http\Controllers;

use App\Models\{Course, CourseEnrollment, Submission};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Tampilkan daftar siswa yang terdaftar di course tertentu.
     * GET /teacher/courses/{courseId}/students
     */
    public function index(int $courseId)
    {
        $course = Course::where('id', $courseId)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();

        // Ambil semua siswa yang enroll di course ini beserta submissions mereka
        $students = CourseEnrollment::where('course_id', $courseId)
            ->with(['user.submissions' => function ($q) use ($courseId) {
                $q->where('course_id', $courseId)->with('assignment');
            }])
            ->get()
            ->pluck('user')
            ->filter();

        $pendingCount = Submission::where('course_id', $courseId)
            ->where('status', 'pending')
            ->count();

        $gradedCount = Submission::where('course_id', $courseId)
            ->where('status', 'graded')
            ->count();

        return view('teacher.students.index', compact('course', 'students', 'pendingCount', 'gradedCount'));
    }
}