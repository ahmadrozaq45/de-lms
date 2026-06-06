<?php

namespace App\Http\Controllers;

use App\Models\{Course, CourseEnrollment, Submission};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Daftar siswa (approved + pending) di course milik guru.
     * GET /teacher/courses/{courseId}/students
     */
    public function index(int $courseId)
    {
        $course = Course::where('id', $courseId)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();

        // Siswa approved
        $students = CourseEnrollment::where('course_id', $courseId)
            ->where('status', 'approved')
            ->with(['user.submissions' => function ($q) use ($courseId) {
                $q->where('course_id', $courseId)->with('assignment');
            }])
            ->get()
            ->pluck('user')
            ->filter();

        // Siswa pending (menunggu persetujuan)
        $pendingEnrollments = CourseEnrollment::where('course_id', $courseId)
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();

        $pendingCount = Submission::where('course_id', $courseId)
            ->where('status', 'pending')
            ->count();

        $gradedCount = Submission::where('course_id', $courseId)
            ->where('status', 'graded')
            ->count();

        return view('teacher.students.index', compact(
            'course', 'students', 'pendingEnrollments', 'pendingCount', 'gradedCount'
        ));
    }

    /**
     * Setujui permintaan join siswa.
     * POST /teacher/courses/{courseId}/students/{enrollmentId}/approve
     */
    public function approve(int $courseId, int $enrollmentId)
    {
        $course = Course::where('id', $courseId)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();

        $enrollment = CourseEnrollment::where('id', $enrollmentId)
            ->where('course_id', $courseId)
            ->where('status', 'pending')
            ->firstOrFail();

        $enrollment->update(['status' => 'approved']);

        return redirect()->route('teacher.courses.students', $courseId)
            ->with('success', $enrollment->user->name . ' berhasil disetujui ke kelas ' . $course->title . '.');
    }

    /**
     * Tolak permintaan join atau hapus siswa dari kelas.
     * DELETE /teacher/courses/{courseId}/students/{enrollmentId}
     */
    public function destroy(int $courseId, int $enrollmentId)
    {
        $course = Course::where('id', $courseId)
            ->where('teacher_id', Auth::id())
            ->firstOrFail();

        $enrollment = CourseEnrollment::where('id', $enrollmentId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $name = $enrollment->user->name ?? 'Siswa';
        $enrollment->delete();

        return redirect()->route('teacher.courses.students', $courseId)
            ->with('success', $name . ' berhasil dihapus dari kelas.');
    }
}