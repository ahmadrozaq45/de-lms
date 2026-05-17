<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function __construct(private BadgeService $badgeService) {}

    /**
     * Proses pendaftaran siswa ke kelas.
     * POST /api/enroll  |  POST /student/enroll
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
        ]);

        // Cek course ada
        $course = \App\Models\Course::find($request->course_id);
        if (!$course) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Course ID tidak ditemukan. Pastikan ID yang Anda masukkan benar.');
        }

        // Cek sudah terdaftar
        $alreadyEnrolled = CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $request->course_id)
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar di course "' . $course->title . '".');
        }

        CourseEnrollment::create([
            'user_id'   => Auth::id(),
            'course_id' => $request->course_id,
        ]);

        $this->badgeService->checkFirstLogin(Auth::user());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Berhasil mendaftar ke kelas.'], 201);
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'Selamat! Anda berhasil bergabung ke course "' . $course->title . '".');
    }
}