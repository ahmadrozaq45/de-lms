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
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        $isNew = false;
        $enrollment = CourseEnrollment::firstOrCreate(
            ['user_id' => Auth::id(), 'course_id' => $request->course_id],
        );

        // first_login badge (award waktu pertama enroll = tanda pertama kali aktif)
        $this->badgeService->checkFirstLogin(Auth::user());

        if ($request->expectsJson()) {
            return response()->json([
                'message'    => 'Berhasil mendaftar ke kelas.',
                'enrollment' => $enrollment,
            ], 201);
        }

        return redirect()->back()->with('success', 'Selamat! Anda berhasil mendaftar ke kelas ini.');
    }
}
