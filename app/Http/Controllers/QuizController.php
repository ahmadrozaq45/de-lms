<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Guru membuat quiz baru.
     * POST /api/quizzes
     */
    public function storeQuiz(Request $request)
    {
        $validated = $request->validate([
            'course_id'  => 'required|integer|exists:courses,id',
            'title'      => 'required|string|max:255',
            'time_limit' => 'required|integer|min:1',
        ]);

        return response()->json(Quiz::create($validated), 201);
    }

    /**
     * Guru menambahkan pertanyaan ke quiz.
     * POST /api/quizzes/{id}/questions
     */
    public function addQuestion(Request $request, int $quizId)
    {
        $validated = $request->validate([
            'question_text'  => 'required|string',
            'options'        => 'required|array|min:2',
            'options.*'      => 'required|string',
            'correct_answer' => 'required|string',
        ]);

        $validated['quiz_id'] = $quizId;

        return response()->json(Question::create($validated), 201);
    }

    /**
     * Siswa memulai attempt quiz.
     * POST /api/quizzes/{id}/attempt
     */
    public function startAttempt(int $quizId)
    {
        $attempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'user_id' => Auth::id(),
        ]);

        return response()->json($attempt, 201);
    }

    /**
     * Siswa menyimpan jawaban per soal.
     * POST /api/quizzes/save-answer
     */
    public function saveAnswer(Request $request)
    {
        $validated = $request->validate([
            'quiz_attempt_id' => 'required|integer|exists:quiz_attempts,id',
            'question_id'     => 'required|integer|exists:questions,id',
            'answer_text'     => 'required|string',
            'is_correct'      => 'required|boolean',
        ]);

        return response()->json(QuizAnswer::create($validated), 201);
    }
}
