<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Proses pendaftaran siswa ke kelas.
     * POST /api/enroll  |  POST /student/enroll
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string|max:7',
        ]);

        // Cek course ada
        $course = \App\Models\Course::where('course_code', $request->course_code)->first();
        if (!$course) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Kode course tidak ditemukan. Pastikan kode yang Anda masukkan benar.'
                ], 404);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kode course tidak ditemukan. Pastikan kode yang Anda masukkan benar.');
        }

        // Cek sudah terdaftar
        $alreadyEnrolled = CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->exists();

        if ($alreadyEnrolled) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Anda sudah terdaftar di course "' . $course->title . '".'
                ], 409);
            }
            return redirect()->back()->with('error', 'Anda sudah terdaftar di course "' . $course->title . '".');
        }

        CourseEnrollment::create([
            'user_id'   => Auth::id(),
            'course_id' => $course->id,
        ]);


        if ($request->expectsJson()) {
            return response()->json(['message' => 'Berhasil mendaftar ke kelas.'], 201);
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'Selamat! Anda berhasil bergabung ke course "' . $course->title . '".');
    }
}