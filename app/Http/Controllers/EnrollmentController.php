<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Siswa meminta bergabung ke kelas via course_code.
     * Status awal: pending (menunggu persetujuan guru).
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string|max:7',
        ]);

        $course = \App\Models\Course::where('course_code', $request->course_code)->first();

        if (!$course) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Kode course tidak ditemukan.'], 404);
            }
            return redirect()->back()->withInput()
                ->with('error', 'Kode course tidak ditemukan. Pastikan kode yang Anda masukkan benar.');
        }

        $existing = CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            $msg = $existing->isPending()
                ? 'Permintaan bergabung ke "' . $course->title . '" sedang menunggu persetujuan guru.'
                : 'Anda sudah terdaftar di course "' . $course->title . '".';

            if ($request->expectsJson()) {
                return response()->json(['error' => $msg], 409);
            }
            return redirect()->back()->with('error', $msg);
        }

        CourseEnrollment::create([
            'user_id'   => Auth::id(),
            'course_id' => $course->id,
            'status'    => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Permintaan bergabung terkirim. Menunggu persetujuan guru.'], 201);
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'Permintaan bergabung ke "' . $course->title . '" berhasil dikirim. Menunggu persetujuan guru.');
    }
}