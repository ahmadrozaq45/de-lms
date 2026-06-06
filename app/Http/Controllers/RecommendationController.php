<?php

namespace App\Http\Controllers;

use App\Models\AiRecommendation;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(private RecommendationService $service) {}

    /**
     * Main recommendations page / panel.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Refresh recommendations if none exist or forced
        $existing = AiRecommendation::forUser($user->id)->active()->count();
        if ($existing === 0 || $request->boolean('refresh')) {
            $this->service->generateForUser($user);
        }

        $recommendations = AiRecommendation::forUser($user->id)
            ->active()
            ->orderByDesc('score')
            ->get()
            ->groupBy('type');

        $summary = $this->buildSummary($user->id);

        return view('recommendations.index', compact('recommendations', 'summary'));
    }

    /**
     * AJAX: get recommendations as JSON (for dashboard widget).
     */
    public function widget(Request $request)
    {
        $user = $request->user();

        $items = AiRecommendation::forUser($user->id)
            ->active()
            ->orderByDesc('score')
            ->limit(5)
            ->get();

        return response()->json($items->map(fn($r) => [
            'id'          => $r->id,
            'type'        => $r->type,
            'type_label'  => $r->getTypeLabel(),
            'type_icon'   => $r->getTypeIcon(),
            'type_color'  => $r->getTypeColor(),
            'title'       => $r->title,
            'description' => $r->description,
            'score'       => $r->score,
            'basis'       => $r->basis,
        ]));
    }

    /**
     * Record user interaction with a recommendation.
     */
    public function feedback(Request $request, int $id)
    {
        $request->validate([
            'action' => 'required|in:clicked,dismissed,completed,skipped',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $this->service->recordFeedback(
            recommendationId: $id,
            userId:           $request->user()->id,
            action:           $request->input('action'),
            rating:           $request->input('rating'),
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Force-regenerate recommendations for the authenticated user.
     */
    public function refresh(Request $request)
    {
        $recs = $this->service->generateForUser($request->user());
        return redirect()->route('recommendations.index')
                         ->with('success', "Rekomendasi diperbarui ({$recs->count()} item).");
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function buildSummary(int $userId): array
    {
        return [
            'total'      => AiRecommendation::forUser($userId)->active()->count(),
            'by_type'    => AiRecommendation::forUser($userId)->active()
                               ->selectRaw('type, COUNT(*) as cnt')
                               ->groupBy('type')
                               ->pluck('cnt', 'type'),
            'top_score'  => AiRecommendation::forUser($userId)->active()->max('score') ?? 0,
        ];
    }

    public function goto(Request $request, int $id)
    {
        $rec = AiRecommendation::findOrFail($id);

        if ($rec->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->service->recordFeedback($id, $request->user()->id, 'clicked');

        // Build redirect URL based on target_type
        $url = match ($rec->target_type) {
            'chapter'    => route('courses.chapters.show', ['chapter' => $rec->target_id]),
            'course'     => route('courses.show',          ['course'  => $rec->target_id]),
            'quiz'       => route('quizzes.show',          ['quiz'    => $rec->target_id]),
            'assignment' => route('assignments.show',      ['assignment' => $rec->target_id]),
            default      => route('dashboard'),
        };

        return redirect($url);
    }
}
