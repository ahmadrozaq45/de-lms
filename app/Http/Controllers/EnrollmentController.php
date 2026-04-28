<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Proses pendaftaran siswa ke kelas.
     * Digunakan oleh web route maupun API route (role: student).
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        CourseEnrollment::firstOrCreate([
            'user_id'   => Auth::id(),
            'course_id' => $request->course_id,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Berhasil mendaftar ke kelas.'], 201);
        }

        return redirect()->back()->with('success', 'Selamat! Anda berhasil mendaftar ke kelas ini.');
    }
}
