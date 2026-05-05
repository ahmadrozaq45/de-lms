<?php

namespace App\Http\Controllers;

use App\Models\{AiAnalysis, Course, CourseEnrollment};
use App\Services\AiService;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;

class AiAnalysisController extends Controller
{
    public function __construct(private AiService $aiService) {}

    /**
     * Ambil analisis AI terbaru untuk user yang login pada kursus tertentu.
     * GET /api/ai/analysis/{courseId}
     */
    public function getAnalysis(int $courseId): JsonResponse
    {
        $analysis = AiAnalysis::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        return response()->json($analysis);
    }

    /**
     * Simpan analisis AI secara manual (legacy).
     * POST /api/ai/analysis
     */
    public function storeAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id'         => 'required|integer|exists:courses,id',
            'status_prediction' => 'required|string|max:255',
            'recommendation'    => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        return response()->json(AiAnalysis::create($validated), 201);
    }

    /**
     * Generate rekomendasi AI otomatis untuk user yang login di suatu kursus.
     * POST /api/ai/generate/{courseId}
     */
    public function generateForMe(int $courseId): JsonResponse
    {
        $user   = Auth::user();
        $course = Course::findOrFail($courseId);

        // Hanya siswa yang enrolled yang bisa generate untuk dirinya sendiri
        if ($user->role === 'student') {
            $enrolled = CourseEnrollment::where('user_id', $user->id)
                ->where('course_id', $courseId)->exists();
            abort_if(!$enrolled, 403, 'Anda belum terdaftar di kursus ini.');
        }

        $analysis = $this->aiService->generateStudentRecommendation($user, $course);

        return response()->json($analysis, 201);
    }

    /**
     * Admin/Guru generate rekomendasi untuk siswa tertentu.
     * POST /api/ai/generate-for-student
     */
    public function generateForStudent(Request $request): JsonResponse
    {
        abort_if(!in_array(Auth::user()->role, ['admin', 'teacher']), 403);

        $request->validate([
            'student_id' => 'required|integer|exists:users,id',
            'course_id'  => 'required|integer|exists:courses,id',
        ]);

        $student = \App\Models\User::findOrFail($request->student_id);
        $course  = Course::findOrFail($request->course_id);

        $analysis = $this->aiService->generateStudentRecommendation($student, $course);

        return response()->json($analysis, 201);
    }
}
