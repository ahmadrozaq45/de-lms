<?php

namespace App\Http\Controllers;

use App\Models\AiAnalysis;
use Illuminate\Http\Request;

class AiAnalysisController extends Controller
{
    public function getAnalysis($courseId, $userId) {
        return response()->json(AiAnalysis::where('course_id', $courseId)->where('user_id', $userId)->latest()->first());
    }

    public function storeAnalysis(Request $request) {
        return response()->json(AiAnalysis::create($request->all()), 201);
    }
}
