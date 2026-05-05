<?php

namespace App\Http\Controllers;

use App\Models\{Quiz, Question, QuizAttempt, QuizAnswer, AiConversation};
use App\Services\{BadgeService, AiService};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function __construct(
        private BadgeService $badgeService,
        private AiService    $aiService,
    ) {}

    // =========================================================
    // GURU
    // =========================================================

    /**
     * Guru membuat quiz baru.
     * POST /api/quizzes
     */
    public function storeQuiz(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id'          => 'required|integer|exists:courses,id',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'time_limit'         => 'required|integer|min:1',
            'passing_score'      => 'sometimes|integer|min:0|max:100',
            'type'               => 'sometimes|in:multiple_choice,conversation',
            'ai_system_prompt'   => 'nullable|string',
            'conversation_topic' => 'nullable|string',
            'min_turns'          => 'sometimes|integer|min:1',
        ]);

        return response()->json(Quiz::create($validated), 201);
    }

    /**
     * Guru menambahkan pertanyaan pilihan ganda ke quiz.
     * POST /api/quizzes/{id}/questions
     */
    public function addQuestion(Request $request, int $quizId): JsonResponse
    {
        $quiz = Quiz::findOrFail($quizId);
        abort_if($quiz->type !== 'multiple_choice', 422, 'Quiz ini bukan tipe pilihan ganda.');

        $validated = $request->validate([
            'question_text'  => 'required|string',
            'options'        => 'required|array|min:2|max:6',
            'options.*'      => 'required|string',
            'correct_answer' => 'required|string',
        ]);

        $validated['quiz_id'] = $quizId;

        return response()->json(Question::create($validated), 201);
    }

    /**
     * Guru melihat semua quiz di kursusnya.
     * GET /api/courses/{courseId}/quizzes
     */
    public function indexByCourse(int $courseId): JsonResponse
    {
        $quizzes = Quiz::where('course_id', $courseId)
            ->withCount('questions')
            ->with('attempts')
            ->get();

        return response()->json($quizzes);
    }

    /**
     * Lihat detail quiz (termasuk soal).
     * GET /api/quizzes/{id}
     */
    public function show(int $quizId): JsonResponse
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        return response()->json($quiz);
    }

    // =========================================================
    // SISWA — MULTIPLE CHOICE
    // =========================================================

    /**
     * Siswa memulai attempt quiz pilihan ganda.
     * POST /api/quizzes/{id}/attempt
     */
    public function startAttempt(int $quizId): JsonResponse
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'user_id' => Auth::id(),
        ]);

        // Kembalikan soal (tanpa jawaban benar)
        $questions = $quiz->questions->map(fn($q) => [
            'id'            => $q->id,
            'question_text' => $q->question_text,
            'options'       => $q->options,
        ]);

        return response()->json([
            'attempt_id' => $attempt->id,
            'quiz'       => [
                'id'           => $quiz->id,
                'title'        => $quiz->title,
                'time_limit'   => $quiz->time_limit,
                'passing_score'=> $quiz->passing_score,
            ],
            'questions'  => $questions,
        ], 201);
    }

    /**
     * Siswa submit semua jawaban sekaligus + auto-score.
     * POST /api/quiz-attempts/{attemptId}/submit
     */
    public function submitAttempt(Request $request, int $attemptId): JsonResponse
    {
        $attempt = QuizAttempt::with('quiz.questions')->findOrFail($attemptId);

        abort_if($attempt->user_id !== Auth::id(), 403, 'Bukan attempt Anda.');
        abort_if($attempt->completed_at !== null, 422, 'Attempt sudah selesai.');

        $request->validate([
            'answers'              => 'required|array',
            'answers.*.question_id'=> 'required|integer|exists:questions,id',
            'answers.*.answer_text'=> 'required|string',
        ]);

        $quiz      = $attempt->quiz;
        $questions = $quiz->questions->keyBy('id');
        $correct   = 0;

        foreach ($request->answers as $ans) {
            $question  = $questions->get($ans['question_id']);
            $isCorrect = $question && (strtolower(trim($question->correct_answer)) === strtolower(trim($ans['answer_text'])));

            if ($isCorrect) $correct++;

            QuizAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'question_id'     => $ans['question_id'],
                'answer_text'     => $ans['answer_text'],
                'is_correct'      => $isCorrect,
            ]);
        }

        $total    = $questions->count();
        $score    = $total > 0 ? round(($correct / $total) * 100) : 0;
        $isPassed = $score >= $quiz->passing_score;

        $attempt->update([
            'score'        => $score,
            'is_passed'    => $isPassed,
            'completed_at' => Carbon::now(),
        ]);

        // Award badges
        $this->badgeService->checkAfterQuiz(Auth::user(), $score, $isPassed);

        return response()->json([
            'score'         => $score,
            'is_passed'     => $isPassed,
            'correct'       => $correct,
            'total'         => $total,
            'passing_score' => $quiz->passing_score,
        ]);
    }

    // =========================================================
    // SISWA — CONVERSATION QUIZ
    // =========================================================

    /**
     * Siswa memulai conversation quiz.
     * POST /api/quizzes/{id}/conversation/start
     */
    public function startConversation(int $quizId): JsonResponse
    {
        $quiz = Quiz::findOrFail($quizId);
        abort_if($quiz->type !== 'conversation', 422, 'Quiz ini bukan tipe conversation.');

        $attempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'user_id' => Auth::id(),
        ]);

        // AI membuka percakapan dengan pertanyaan pertama
        $firstMessage = $this->aiService->conversationTurn($quiz, [], '');

        // Simpan pesan pembuka AI
        AiConversation::create([
            'quiz_attempt_id' => $attempt->id,
            'user_id'         => Auth::id(),
            'quiz_id'         => $quizId,
            'role'            => 'assistant',
            'message'         => $firstMessage,
        ]);

        return response()->json([
            'attempt_id'    => $attempt->id,
            'quiz_title'    => $quiz->title,
            'topic'         => $quiz->conversation_topic,
            'min_turns'     => $quiz->min_turns,
            'first_message' => $firstMessage,
        ], 201);
    }

    /**
     * Siswa mengirim pesan dalam conversation quiz.
     * POST /api/quiz-attempts/{attemptId}/conversation/chat
     */
    public function conversationChat(Request $request, int $attemptId): JsonResponse
    {
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);

        abort_if($attempt->user_id !== Auth::id(), 403, 'Bukan attempt Anda.');
        abort_if($attempt->completed_at !== null, 422, 'Sesi sudah selesai.');

        $request->validate([
            'message'   => 'required|string|max:2000',
            'audio_url' => 'nullable|string|url', // opsional untuk input audio
        ]);

        // Simpan pesan siswa
        AiConversation::create([
            'quiz_attempt_id' => $attempt->id,
            'user_id'         => Auth::id(),
            'quiz_id'         => $attempt->quiz_id,
            'role'            => 'user',
            'message'         => $request->message,
            'audio_url'       => $request->audio_url,
        ]);

        // Ambil history percakapan
        $history = AiConversation::where('quiz_attempt_id', $attemptId)
            ->orderBy('id')
            ->get(['role', 'message'])
            ->toArray();

        // Dapatkan respons AI
        $aiReply = $this->aiService->conversationTurn($attempt->quiz, $history, $request->message);

        // Simpan balasan AI
        AiConversation::create([
            'quiz_attempt_id' => $attempt->id,
            'user_id'         => Auth::id(),
            'quiz_id'         => $attempt->quiz_id,
            'role'            => 'assistant',
            'message'         => $aiReply,
        ]);

        // Hitung jumlah turn siswa
        $userTurns = AiConversation::where('quiz_attempt_id', $attemptId)
            ->where('role', 'user')
            ->count();

        return response()->json([
            'ai_reply'   => $aiReply,
            'user_turns' => $userTurns,
            'min_turns'  => $attempt->quiz->min_turns,
            'can_finish' => $userTurns >= $attempt->quiz->min_turns,
        ]);
    }

    /**
     * Siswa mengakhiri conversation quiz dan mendapat skor.
     * POST /api/quiz-attempts/{attemptId}/conversation/finish
     */
    public function finishConversation(int $attemptId): JsonResponse
    {
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);

        abort_if($attempt->user_id !== Auth::id(), 403, 'Bukan attempt Anda.');
        abort_if($attempt->completed_at !== null, 422, 'Sesi sudah selesai.');

        $userTurns = AiConversation::where('quiz_attempt_id', $attemptId)
            ->where('role', 'user')
            ->count();

        if ($userTurns < $attempt->quiz->min_turns) {
            return response()->json([
                'message' => "Minimal {$attempt->quiz->min_turns} giliran sebelum selesai. Anda baru {$userTurns}.",
            ], 422);
        }

        // Ambil history untuk evaluasi
        $history = AiConversation::where('quiz_attempt_id', $attemptId)
            ->orderBy('id')
            ->get(['role', 'message'])
            ->toArray();

        $evaluation = $this->aiService->evaluateConversation($attempt->quiz, $history);
        $score      = $evaluation['score'];
        $isPassed   = $score >= $attempt->quiz->passing_score;

        $attempt->update([
            'score'        => $score,
            'is_passed'    => $isPassed,
            'completed_at' => Carbon::now(),
        ]);

        // Award badges
        $this->badgeService->checkAfterQuiz(Auth::user(), $score, $isPassed);

        return response()->json([
            'score'         => $score,
            'is_passed'     => $isPassed,
            'passing_score' => $attempt->quiz->passing_score,
            'feedback'      => $evaluation['feedback'],
            'strengths'     => $evaluation['strengths'],
            'weaknesses'    => $evaluation['weaknesses'],
        ]);
    }

    /**
     * Ambil history percakapan suatu attempt.
     * GET /api/quiz-attempts/{attemptId}/conversation
     */
    public function getConversation(int $attemptId): JsonResponse
    {
        $attempt = QuizAttempt::findOrFail($attemptId);
        abort_if($attempt->user_id !== Auth::id() && !in_array(Auth::user()->role, ['teacher', 'admin']), 403);

        $history = AiConversation::where('quiz_attempt_id', $attemptId)
            ->orderBy('id')
            ->get();

        return response()->json($history);
    }

    // =========================================================
    // LEGACY (backward compat)
    // =========================================================

    /** @deprecated Gunakan submitAttempt */
    public function saveAnswer(Request $request): JsonResponse
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
