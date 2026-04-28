<?php

namespace App\Http\Controllers;

use App\Models\AiAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAnalysisController extends Controller
{
    /**
     * Ambil analisis AI terbaru untuk user yang login pada kursus tertentu.
     * GET /api/ai/analysis/{courseId}
     */
    public function getAnalysis(int $courseId)
    {
        $analysis = AiAnalysis::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        return response()->json($analysis);
    }

    /**
     * Simpan analisis AI baru.
     * POST /api/ai/analysis
     */
    public function storeAnalysis(Request $request)
    {
        $validated = $request->validate([
            'course_id'          => 'required|integer|exists:courses,id',
            'status_prediction'  => 'required|string|max:255',
            'recommendation'     => 'nullable|string',
        ]);

        // user_id selalu diambil dari session, tidak dari input user
        $validated['user_id'] = Auth::id();

        return response()->json(AiAnalysis::create($validated), 201);
    }
}
