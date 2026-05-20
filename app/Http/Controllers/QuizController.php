<?php

namespace App\Http\Controllers;

use App\Models\{Quiz, Question, QuizAttempt, QuizAnswer, Course, CourseEnrollment};
use App\Services\BadgeService;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function __construct(private BadgeService $badgeService) {}

    // =========================================================
    // GURU — WEB
    // =========================================================

    /**
     * Halaman form buat quiz baru.
     * GET /teacher/courses/{courseId}/quizzes/create
     */
    public function create(int $courseId)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($courseId);
        return view('teacher.quizzes.create', compact('course'));
    }

    /**
     * Simpan quiz baru via form web.
     * POST /teacher/courses/{courseId}/quizzes
     */
    public function storeWeb(Request $request, int $courseId)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($courseId);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'time_limit'    => 'required|integer|min:1|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
        ]);

        $validated['course_id'] = $courseId;
        $validated['type']      = 'multiple_choice';

        $quiz = Quiz::create($validated);

        return redirect()
            ->route('teacher.quizzes.show', $quiz->id)
            ->with('success', 'Quiz berhasil dibuat! Sekarang tambahkan soal-soalnya.');
    }

    /**
     * Halaman detail quiz untuk guru: lihat soal + tambah soal.
     * GET /teacher/quizzes/{quizId}
     */
    public function showTeacher(int $quizId)
    {
        $quiz = Quiz::with(['questions', 'course', 'attempts.user'])
            ->whereHas('course', fn($q) => $q->where('teacher_id', Auth::id()))
            ->findOrFail($quizId);

        return view('teacher.quizzes.show', compact('quiz'));
    }

    /**
     * Tambah soal via form web.
     * POST /teacher/quizzes/{quizId}/questions
     */
    public function addQuestionWeb(Request $request, int $quizId)
    {
        $quiz = Quiz::whereHas('course', fn($q) => $q->where('teacher_id', Auth::id()))
            ->findOrFail($quizId);

        $validated = $request->validate([
            'question_text'  => 'required|string',
            'options'        => 'required|array|min:2|max:6',
            'options.*'      => 'required|string|max:500',
            'correct_answer' => 'required|string',
        ]);

        // Pastikan correct_answer ada di dalam options
        if (!in_array($validated['correct_answer'], $validated['options'])) {
            return back()
                ->withErrors(['correct_answer' => 'Jawaban benar harus salah satu dari pilihan yang ada.'])
                ->withInput();
        }

        Question::create([
            'quiz_id'        => $quizId,
            'question_text'  => $validated['question_text'],
            'options'        => $validated['options'],
            'correct_answer' => $validated['correct_answer'],
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    /**
     * Hapus soal.
     * DELETE /teacher/quizzes/{quizId}/questions/{questionId}
     */
    public function deleteQuestion(int $quizId, int $questionId)
    {
        $quiz = Quiz::whereHas('course', fn($q) => $q->where('teacher_id', Auth::id()))
            ->findOrFail($quizId);

        Question::where('quiz_id', $quiz->id)->findOrFail($questionId)->delete();

        return back()->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Halaman hasil attempt semua siswa pada quiz ini.
     * GET /teacher/quizzes/{quizId}/results
     */
    public function results(int $quizId)
    {
        $quiz = Quiz::with([
            'course',
            'attempts' => fn($q) => $q->with('user')->latest(),
        ])
            ->whereHas('course', fn($q) => $q->where('teacher_id', Auth::id()))
            ->findOrFail($quizId);

        $stats = [
            'total'      => $quiz->attempts->count(),
            'completed'  => $quiz->attempts->whereNotNull('completed_at')->count(),
            'passed'     => $quiz->attempts->where('is_passed', true)->count(),
            'avg_score'  => round($quiz->attempts->whereNotNull('score')->avg('score') ?? 0),
        ];

        return view('teacher.quizzes.results', compact('quiz', 'stats'));
    }

    /**
     * Hapus quiz beserta soal-soalnya.
     * DELETE /teacher/quizzes/{quizId}
     */
    public function destroy(int $quizId)
    {
        $quiz = Quiz::whereHas('course', fn($q) => $q->where('teacher_id', Auth::id()))
            ->findOrFail($quizId);

        $courseId = $quiz->course_id;
        $quiz->delete();

        return redirect()
            ->route('teacher.courses.show', $courseId)
            ->with('success', 'Quiz berhasil dihapus.');
    }

    // =========================================================
    // SISWA — WEB
    // =========================================================

    /**
     * Halaman info quiz sebelum mulai.
     * GET /student/quizzes/{quizId}
     */
    public function showStudent(int $quizId)
    {
        $quiz = Quiz::with(['questions', 'course'])->findOrFail($quizId);

        // Pastikan student terdaftar di kursus ini
        abort_unless(
            CourseEnrollment::where('user_id', Auth::id())
                ->where('course_id', $quiz->course_id)
                ->exists(),
            403,
            'Anda belum terdaftar di kursus ini.'
        );

        $attempts = QuizAttempt::where('quiz_id', $quizId)
            ->where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->latest()
            ->get();

        $lastAttempt   = $attempts->first();
        $attemptCount  = $attempts->count();

        return view('student.quizzes.show', compact('quiz', 'lastAttempt', 'attemptCount'));
    }

    /**
     * Mulai attempt baru, redirect ke halaman kerjakan.
     * POST /student/quizzes/{quizId}/start
     */
    public function startWeb(int $quizId)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        abort_unless(
            CourseEnrollment::where('user_id', Auth::id())
                ->where('course_id', $quiz->course_id)
                ->exists(),
            403
        );

        abort_if($quiz->questions->isEmpty(), 422, 'Quiz ini belum memiliki soal.');

        $attempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('student.quizzes.work', $attempt->id);
    }

    /**
     * Halaman kerjakan soal.
     * GET /student/attempts/{attemptId}/work
     */
    public function workAttempt(int $attemptId)
    {
        $attempt = QuizAttempt::with('quiz.questions')->findOrFail($attemptId);

        abort_if($attempt->user_id !== Auth::id(), 403);

        // Kalau sudah selesai, redirect ke hasil
        if ($attempt->completed_at !== null) {
            return redirect()->route('student.quizzes.result', $attemptId);
        }

        $quiz      = $attempt->quiz;
        $questions = $quiz->questions->map(fn($q) => [
            'id'            => $q->id,
            'question_text' => $q->question_text,
            'options'       => $q->options,
        ]);

        return view('student.quizzes.work', compact('attempt', 'quiz', 'questions'));
    }

    /**
     * Submit semua jawaban via web.
     * POST /student/attempts/{attemptId}/submit
     */
    public function submitWeb(Request $request, int $attemptId)
    {
        $attempt = QuizAttempt::with('quiz.questions')->findOrFail($attemptId);

        abort_if($attempt->user_id !== Auth::id(), 403);
        abort_if($attempt->completed_at !== null, 422, 'Attempt sudah selesai.');

        $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'nullable|string',
        ]);

        $quiz      = $attempt->quiz;
        $questions = $quiz->questions->keyBy('id');
        $correct   = 0;

        foreach ($questions as $qId => $question) {
            $answerText = $request->input("answers.{$qId}", '');
            $isCorrect  = strtolower(trim($question->correct_answer)) === strtolower(trim($answerText));

            if ($isCorrect) $correct++;

            QuizAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'question_id'     => $qId,
                'answer_text'     => $answerText,
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

        $this->badgeService->checkAfterQuiz(Auth::user(), $score, $isPassed);

        return redirect()->route('student.quizzes.result', $attemptId);
    }

    /**
     * Halaman hasil setelah submit.
     * GET /student/attempts/{attemptId}/result
     */
    public function resultWeb(int $attemptId)
    {
        $attempt = QuizAttempt::with([
            'quiz.course',
            'answers.question',
        ])->findOrFail($attemptId);

        abort_if($attempt->user_id !== Auth::id(), 403);
        abort_if($attempt->completed_at === null, 422, 'Quiz belum selesai dikerjakan.');

        $totalQuestions = $attempt->answers->count();
        $correctCount   = $attempt->answers->where('is_correct', true)->count();

        return view('student.quizzes.result', compact('attempt', 'totalQuestions', 'correctCount'));
    }

    // =========================================================
    // GURU — API (tetap ada untuk keperluan API)
    // =========================================================

    /** POST /api/quizzes */
    public function storeQuiz(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id'     => 'required|integer|exists:courses,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'time_limit'    => 'required|integer|min:1',
            'passing_score' => 'sometimes|integer|min:0|max:100',
            'type'          => 'sometimes|in:multiple_choice,conversation',
        ]);

        return response()->json(Quiz::create($validated), 201);
    }

    /** POST /api/quizzes/{id}/questions */
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

    /** GET /api/courses/{courseId}/quizzes */
    public function indexByCourse(int $courseId): JsonResponse
    {
        $quizzes = Quiz::where('course_id', $courseId)
            ->withCount('questions')
            ->with('attempts')
            ->get();

        return response()->json($quizzes);
    }

    /** GET /api/quizzes/{id} */
    public function show(int $quizId): JsonResponse
    {
        return response()->json(Quiz::with('questions')->findOrFail($quizId));
    }

    // =========================================================
    // SISWA — API
    // =========================================================

    /** POST /api/quizzes/{id}/attempt */
    public function startAttempt(int $quizId): JsonResponse
    {
        $quiz    = Quiz::with('questions')->findOrFail($quizId);
        $attempt = QuizAttempt::create(['quiz_id' => $quizId, 'user_id' => Auth::id()]);

        $questions = $quiz->questions->map(fn($q) => [
            'id'            => $q->id,
            'question_text' => $q->question_text,
            'options'       => $q->options,
        ]);

        return response()->json([
            'attempt_id' => $attempt->id,
            'quiz'       => ['id' => $quiz->id, 'title' => $quiz->title, 'time_limit' => $quiz->time_limit, 'passing_score' => $quiz->passing_score],
            'questions'  => $questions,
        ], 201);
    }

    /** POST /api/quiz-attempts/{attemptId}/submit */
    public function submitAttempt(Request $request, int $attemptId): JsonResponse
    {
        $attempt = QuizAttempt::with('quiz.questions')->findOrFail($attemptId);

        abort_if($attempt->user_id !== Auth::id(), 403);
        abort_if($attempt->completed_at !== null, 422, 'Attempt sudah selesai.');

        $request->validate([
            'answers'               => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer_text' => 'required|string',
        ]);

        $questions = $attempt->quiz->questions->keyBy('id');
        $correct   = 0;

        foreach ($request->answers as $ans) {
            $question  = $questions->get($ans['question_id']);
            $isCorrect = $question && strtolower(trim($question->correct_answer)) === strtolower(trim($ans['answer_text']));
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
        $isPassed = $score >= $attempt->quiz->passing_score;

        $attempt->update(['score' => $score, 'is_passed' => $isPassed, 'completed_at' => Carbon::now()]);
        $this->badgeService->checkAfterQuiz(Auth::user(), $score, $isPassed);

        return response()->json(['score' => $score, 'is_passed' => $isPassed, 'correct' => $correct, 'total' => $total]);
    }

    /** @deprecated */
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