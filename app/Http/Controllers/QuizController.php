<?php

namespace App\Http\Controllers;

use App\Models\{Quiz, Question, QuizAttempt, QuizAnswer};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function storeQuiz(Request $request) {
        $validated = $request->validate(['course_id' => 'required', 'title' => 'required', 'time_limit' => 'required|integer']);
        return response()->json(Quiz::create($validated), 201);
    }

    public function addQuestion(Request $request, $quizId) {
        $validated = $request->validate(['question_text' => 'required', 'options' => 'required|array', 'correct_answer' => 'required']);
        $validated['quiz_id'] = $quizId;
        return response()->json(Question::create($validated), 201);
    }

    public function startAttempt($quizId) {
        return response()->json(QuizAttempt::create(['quiz_id' => $quizId, 'user_id' => Auth::id()]), 201);
    }

    public function saveAnswer(Request $request) {
        $validated = $request->validate(['quiz_attempt_id' => 'required', 'question_id' => 'required', 'answer_text' => 'required', 'is_correct' => 'required|boolean']);
        return response()->json(QuizAnswer::create($validated), 201);
    }
}
