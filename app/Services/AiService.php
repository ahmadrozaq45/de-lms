<?php

namespace App\Services;

use App\Models\{AiAnalysis, User, Course, Quiz, QuizAttempt, MaterialProgress, Submission, Material};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    private string $apiUrl   = 'https://api.anthropic.com/v1/messages';
    private string $model    = 'claude-opus-4-6';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key', '');
    }

    /**
     * Generate AI recommendation (analisis performa siswa di suatu kursus).
     * Simpan ke ai_analyses.
     */
    public function generateStudentRecommendation(User $student, Course $course): AiAnalysis
    {
        // Kumpulkan data performa siswa
        $allMaterialIds = Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))
            ->pluck('id');
        $totalMaterials    = $allMaterialIds->count();
        $completedMaterials = MaterialProgress::where('user_id', $student->id)
            ->whereIn('material_id', $allMaterialIds)
            ->where('is_completed', true)
            ->count();

        $quizAttempts = QuizAttempt::with('quiz')
            ->where('user_id', $student->id)
            ->whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
            ->whereNotNull('score')
            ->get();

        $avgQuizScore   = $quizAttempts->avg('score') ?? 0;
        $failedQuizzes  = $quizAttempts->where('is_passed', false)->count();
        $passedQuizzes  = $quizAttempts->where('is_passed', true)->count();

        $submissions = Submission::where('student_id', $student->id)
            ->whereHas('assignment', fn($q) => $q->where('course_id', $course->id))
            ->whereNotNull('score')
            ->get();

        $avgAssignmentScore = $submissions->avg('score') ?? 0;

        $progressPercent = $totalMaterials > 0
            ? round(($completedMaterials / $totalMaterials) * 100)
            : 0;

        $prompt = <<<PROMPT
Kamu adalah AI tutor yang membantu guru menganalisis performa siswa.

Data siswa:
- Nama: {$student->name}
- Kursus: {$course->title}
- Progress materi: {$completedMaterials}/{$totalMaterials} ({$progressPercent}%)
- Rata-rata skor quiz: {$avgQuizScore}/100
- Quiz tidak lulus: {$failedQuizzes}, Quiz lulus: {$passedQuizzes}
- Rata-rata skor tugas: {$avgAssignmentScore}/100

Berikan:
1. Status prediksi singkat (misal: "At Risk", "Excellent", "Needs Improvement", "On Track")
2. Rekomendasi konkret dalam 2-3 kalimat

Format response JSON:
{
  "status_prediction": "...",
  "recommendation": "..."
}

Jawab hanya dengan JSON, tanpa teks lain.
PROMPT;

        [$statusPrediction, $recommendation] = $this->callApi($prompt);

        return AiAnalysis::updateOrCreate(
            ['user_id' => $student->id, 'course_id' => $course->id],
            [
                'status_prediction' => $statusPrediction,
                'recommendation'    => $recommendation,
            ]
        );
    }

    /**
     * Proses satu giliran conversation quiz:
     * Kirim history + pesan baru ke AI, kembalikan balasan AI.
     */
    public function conversationTurn(Quiz $quiz, array $history, string $userMessage): string
    {
        $systemPrompt = $quiz->ai_system_prompt
            ?? "Kamu adalah asisten guru yang menguji pemahaman siswa tentang topik: {$quiz->conversation_topic}. "
             . "Ajukan pertanyaan yang menggali pemahaman mereka secara mendalam. "
             . "Jangan langsung memberi jawaban—dorong siswa untuk berpikir.";

        // Build messages array untuk Anthropic API
        $messages = [];
        foreach ($history as $turn) {
            $messages[] = [
                'role'    => $turn['role'], // 'user' atau 'assistant'
                'content' => $turn['message'],
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post($this->apiUrl, [
            'model'      => $this->model,
            'max_tokens' => 512,
            'system'     => $systemPrompt,
            'messages'   => $messages,
        ]);

        if ($response->failed()) {
            Log::error('Anthropic API error', ['response' => $response->body()]);
            return 'Maaf, terjadi kesalahan. Silakan coba lagi.';
        }

        return $response->json('content.0.text', 'Tidak ada respons.');
    }

    /**
     * Evaluasi percakapan conversation quiz, beri skor 0-100.
     */
    public function evaluateConversation(Quiz $quiz, array $conversationHistory): array
    {
        $topic = $quiz->conversation_topic ?? $quiz->title;

        $historyText = collect($conversationHistory)->map(function ($turn) {
            $role = $turn['role'] === 'user' ? 'Siswa' : 'AI';
            return "{$role}: {$turn['message']}";
        })->implode("\n");

        $prompt = <<<PROMPT
Kamu adalah penilai ujian lisan berbasis AI.

Topik yang diuji: {$topic}

Transkrip percakapan:
{$historyText}

Nilai pemahaman siswa dari 0-100 berdasarkan:
- Kedalaman pemahaman konsep (40%)
- Kemampuan menjelaskan dengan kata sendiri (30%)
- Ketepatan jawaban (30%)

Format response JSON:
{
  "score": 85,
  "feedback": "Penjelasan yang jelas...",
  "strengths": "...",
  "weaknesses": "..."
}

Jawab hanya dengan JSON, tanpa teks lain.
PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post($this->apiUrl, [
                'model'      => $this->model,
                'max_tokens' => 512,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $text = $response->json('content.0.text', '{}');
            $data = json_decode($text, true);

            return [
                'score'     => $data['score'] ?? 0,
                'feedback'  => $data['feedback'] ?? '',
                'strengths' => $data['strengths'] ?? '',
                'weaknesses'=> $data['weaknesses'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::error('AI evaluation error', ['error' => $e->getMessage()]);
            return ['score' => 0, 'feedback' => 'Evaluasi gagal.', 'strengths' => '', 'weaknesses' => ''];
        }
    }

    /**
     * Panggil Anthropic API dengan prompt sederhana, harapkan JSON response.
     * Return [status_prediction, recommendation].
     */
    private function callApi(string $prompt): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post($this->apiUrl, [
                'model'      => $this->model,
                'max_tokens' => 512,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $text = $response->json('content.0.text', '{}');
            $data = json_decode($text, true);

            return [
                $data['status_prediction'] ?? 'Unknown',
                $data['recommendation']    ?? 'Tidak ada rekomendasi.',
            ];
        } catch (\Exception $e) {
            Log::error('AI service error', ['error' => $e->getMessage()]);
            return ['Unknown', 'Gagal generate rekomendasi.'];
        }
    }
}
