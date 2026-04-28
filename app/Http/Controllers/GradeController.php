<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Guru memberikan nilai (polymorphic: Assignment atau QuizAttempt).
     * POST /api/grades
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'required|integer|exists:users,id',
            'gradeable_id'   => 'required|integer',
            'gradeable_type' => 'required|string|in:App\Models\Assignment,App\Models\QuizAttempt',
            'score'          => 'required|integer|min:0|max:100',
        ]);

        return response()->json(Grade::create($validated), 201);
    }
}
