<?php

namespace App\Services;

use App\Models\{AiAnalysis, User, Course, Quiz, QuizAttempt, MaterialProgress, Submission, Material};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    private string $apiKey;
    private string $provider;
    private string $model;

    private const ENDPOINTS = [
        'anthropic' => 'https://api.anthropic.com/v1/messages',
        'gemini'    => 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent',
        'groq'      => 'https://api.groq.com/openai/v1/chat/completions',
        'openai'    => 'https://api.openai.com/v1/chat/completions',
    ];

    private const DEFAULT_MODELS = [
        'anthropic' => 'claude-sonnet-4-6',
        'gemini'    => 'gemini-1.5-flash',
        'groq'      => 'llama-3.1-8b-instant',
        'openai'    => 'gpt-4o-mini',
    ];

    public function __construct()
    {
        $this->provider = \App\Models\Setting::get('ai_provider', 'anthropic');

        // Baca API key dan model per provider
        $this->apiKey = \App\Models\Setting::get("ai_api_key_{$this->provider}", '')
            ?: config("services.{$this->provider}.key", '');

        $this->model = \App\Models\Setting::get("ai_model_{$this->provider}", '')
            ?: self::DEFAULT_MODELS[$this->provider] ?? 'gemini-1.5-flash';
    }

    // ── Public Methods ────────────────────────────────────────────────────────

    public function generateStudentRecommendation(User $student, Course $course): AiAnalysis
    {
        $allMaterialIds = Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))
            ->pluck('id');
        $totalMaterials     = $allMaterialIds->count();
        $completedMaterials = MaterialProgress::where('user_id', $student->id)
            ->whereIn('material_id', $allMaterialIds)
            ->where('is_completed', true)
            ->count();

        $quizAttempts = QuizAttempt::with('quiz')
            ->where('user_id', $student->id)
            ->whereHas('quiz', fn($q) => $q->where('course_id', $course->id))
            ->whereNotNull('score')
            ->get();

        $avgQuizScore  = $quizAttempts->avg('score') ?? 0;
        $failedQuizzes = $quizAttempts->where('is_passed', false)->count();
        $passedQuizzes = $quizAttempts->where('is_passed', true)->count();

        $submissions = Submission::where('student_id', $student->id)
            ->whereHas('assignment', fn($q) => $q->where('course_id', $course->id))
            ->whereNotNull('score')
            ->get();

        $avgAssignmentScore = $submissions->avg('score') ?? 0;
        $progressPercent    = $totalMaterials > 0
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

    public function conversationTurn(Quiz $quiz, array $history, string $userMessage): string
    {
        $systemPrompt = $quiz->ai_system_prompt
            ?? "Kamu adalah asisten guru yang menguji pemahaman siswa tentang topik: {$quiz->conversation_topic}. "
             . "Ajukan pertanyaan yang menggali pemahaman mereka secara mendalam. "
             . "Jangan langsung memberi jawaban—dorong siswa untuk berpikir.";

        $messages = [];
        foreach ($history as $turn) {
            $messages[] = ['role' => $turn['role'], 'content' => $turn['message']];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $this->callAiChat($systemPrompt, $messages);
    }

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
            $text = $this->callAiRaw($prompt);
            $data = json_decode($this->stripJsonFences($text), true);

            return [
                'score'      => $data['score']      ?? 0,
                'feedback'   => $data['feedback']   ?? '',
                'strengths'  => $data['strengths']  ?? '',
                'weaknesses' => $data['weaknesses'] ?? '',
            ];
        } catch (\Exception $e) {
            Log::error('AI evaluation error', ['error' => $e->getMessage()]);
            return ['score' => 0, 'feedback' => 'Evaluasi gagal.', 'strengths' => '', 'weaknesses' => ''];
        }
    }

    // ── Private: Dispatcher ───────────────────────────────────────────────────

    private function callApi(string $prompt): array
    {
        try {
            $text = $this->callAiRaw($prompt);
            $data = json_decode($this->stripJsonFences($text), true);

            return [
                $data['status_prediction'] ?? 'Unknown',
                $data['recommendation']    ?? 'Tidak ada rekomendasi.',
            ];
        } catch (\Exception $e) {
            Log::error('AI service error', ['error' => $e->getMessage()]);
            return ['Unknown', 'Gagal generate rekomendasi.'];
        }
    }

    private function callAiRaw(string $prompt): string
    {
        return match ($this->provider) {
            'gemini' => $this->callGemini($prompt),
            'groq'   => $this->callGroq([['role' => 'user', 'content' => $prompt]]),
            'openai' => $this->callOpenAi([['role' => 'user', 'content' => $prompt]]),
            default  => $this->callAnthropic([['role' => 'user', 'content' => $prompt]]),
        };
    }

    private function callAiChat(string $system, array $messages): string
    {
        return match ($this->provider) {
            'gemini' => $this->callGeminiChat($system, $messages),
            'groq'   => $this->callGroq($messages, $system),
            'openai' => $this->callOpenAi($messages, $system),
            default  => $this->callAnthropicChat($system, $messages),
        };
    }

    // ── Private: Anthropic ────────────────────────────────────────────────────

    private function callAnthropic(array $messages): string
    {
        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post(self::ENDPOINTS['anthropic'], [
            'model'      => $this->model,
            'max_tokens' => 512,
            'messages'   => $messages,
        ]);

        if ($response->failed()) {
            Log::error('Anthropic API error', ['body' => $response->body()]);
            throw new \RuntimeException('Anthropic API gagal: ' . $response->status());
        }

        return $response->json('content.0.text', '{}');
    }

    private function callAnthropicChat(string $system, array $messages): string
    {
        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post(self::ENDPOINTS['anthropic'], [
            'model'      => $this->model,
            'max_tokens' => 512,
            'system'     => $system,
            'messages'   => $messages,
        ]);

        if ($response->failed()) {
            Log::error('Anthropic chat error', ['body' => $response->body()]);
            return 'Maaf, terjadi kesalahan. Silakan coba lagi.';
        }

        return $response->json('content.0.text', 'Tidak ada respons.');
    }

    // ── Private: Gemini ───────────────────────────────────────────────────────

    private function callGemini(string $prompt): string
    {
        $url = str_replace('{model}', $this->model, self::ENDPOINTS['gemini'])
             . '?key=' . $this->apiKey;

        $response = Http::post($url, [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => ['maxOutputTokens' => 512, 'temperature' => 0.3],
        ]);

        if ($response->failed()) {
            Log::error('Gemini API error', ['body' => $response->body()]);
            throw new \RuntimeException('Gemini API gagal: ' . $response->status());
        }

        return $response->json('candidates.0.content.parts.0.text', '{}');
    }

    private function callGeminiChat(string $system, array $messages): string
    {
        $url = str_replace('{model}', $this->model, self::ENDPOINTS['gemini'])
             . '?key=' . $this->apiKey;

        $contents = [
            ['role' => 'user',  'parts' => [['text' => $system]]],
            ['role' => 'model', 'parts' => [['text' => 'Baik, saya siap.']]],
        ];

        foreach ($messages as $msg) {
            $geminiRole = $msg['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = ['role' => $geminiRole, 'parts' => [['text' => $msg['content']]]];
        }

        $response = Http::post($url, [
            'contents'         => $contents,
            'generationConfig' => ['maxOutputTokens' => 512, 'temperature' => 0.3],
        ]);

        if ($response->failed()) {
            Log::error('Gemini chat error', ['body' => $response->body()]);
            return 'Maaf, terjadi kesalahan. Silakan coba lagi.';
        }

        return $response->json('candidates.0.content.parts.0.text', 'Tidak ada respons.');
    }

    // ── Private: Groq (OpenAI-compatible) ────────────────────────────────────

    private function callGroq(array $messages, string $system = ''): string
    {
        $payload = ['model' => $this->model, 'max_tokens' => 512, 'temperature' => 0.3];

        if ($system) {
            array_unshift($messages, ['role' => 'system', 'content' => $system]);
        }
        $payload['messages'] = $messages;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post(self::ENDPOINTS['groq'], $payload);

        if ($response->failed()) {
            Log::error('Groq API error', ['body' => $response->body()]);
            return 'Maaf, terjadi kesalahan. Silakan coba lagi.';
        }

        return $response->json('choices.0.message.content', '{}');
    }

    // ── Private: OpenAI ───────────────────────────────────────────────────────

    private function callOpenAi(array $messages, string $system = ''): string
    {
        $payload = ['model' => $this->model, 'max_tokens' => 512, 'temperature' => 0.3];

        if ($system) {
            array_unshift($messages, ['role' => 'system', 'content' => $system]);
        }
        $payload['messages'] = $messages;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post(self::ENDPOINTS['openai'], $payload);

        if ($response->failed()) {
            Log::error('OpenAI API error', ['body' => $response->body()]);
            return 'Maaf, terjadi kesalahan. Silakan coba lagi.';
        }

        return $response->json('choices.0.message.content', '{}');
    }

    // ── Utility ───────────────────────────────────────────────────────────────

    private function stripJsonFences(string $text): string
    {
        return trim(preg_replace('/^```json\s*|^```\s*|```$/m', '', trim($text)));
    }
}