<?php

namespace App\Http\Controllers;

use App\Models\AiAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAnalysisController extends Controller
{
    /**
     * Ambil analisis AI untuk user yang sedang login pada kursus tertentu.
     * Route: GET /api/ai/analysis/{courseId}
     */
    public function getAnalysis($courseId)
    {
        $analysis = AiAnalysis::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();
 
        return response()->json($analysis);
    }

    public function storeAnalysis(Request $request) 
    {
        return response()->json(AiAnalysis::create($request->all()), 201);
    }
}
