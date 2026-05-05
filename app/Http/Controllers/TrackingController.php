<?php

namespace App\Http\Controllers;

use App\Models\{ActivityLog, MaterialView, MaterialProgress, Material, Module};
use App\Services\BadgeService;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function __construct(private BadgeService $badgeService) {}

    /**
     * Log aktivitas user.
     * POST /api/tracking/log
     */
    public function logActivity(Request $request): JsonResponse
    {
        $log = ActivityLog::create([
            'user_id'  => Auth::id(),
            'activity' => $request->activity,
            'metadata' => $request->metadata,
        ]);
        return response()->json($log);
    }

    /**
     * Track progress materi (tandai selesai/tidak).
     * POST /api/tracking/progress/{materialId}
     */
    public function trackProgress(Request $request, int $materialId): JsonResponse
    {
        $completed = $request->boolean('completed', false);

        // Catat durasi belajar
        if ($request->has('seconds')) {
            MaterialView::create([
                'user_id'      => Auth::id(),
                'material_id'  => $materialId,
                'seconds_spent'=> $request->seconds,
            ]);
        }

        $progress = MaterialProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'material_id' => $materialId],
            ['is_completed' => $completed]
        );

        // Cek badge jika materi baru saja diselesaikan
        if ($completed) {
            $this->checkModuleCompletionBadge($materialId);
        }

        return response()->json($progress);
    }

    /**
     * Ambil log aktivitas user yang login.
     * GET /api/tracking/my-logs
     */
    public function getActivityLogs(): JsonResponse
    {
        $logs = ActivityLog::where('user_id', Auth::id())->latest()->get();
        return response()->json($logs);
    }

    /**
     * Cek apakah semua materi dalam modul sudah selesai → award badge.
     */
    private function checkModuleCompletionBadge(int $materialId): void
    {
        $material = Material::find($materialId);
        if (!$material) return;

        $module = Module::with('materials')->find($material->module_id);
        if (!$module) return;

        $totalMaterials     = $module->materials->count();
        $completedMaterials = MaterialProgress::where('user_id', Auth::id())
            ->whereIn('material_id', $module->materials->pluck('id'))
            ->where('is_completed', true)
            ->count();

        if ($totalMaterials > 0 && $completedMaterials === $totalMaterials) {
            $this->badgeService->checkAfterMaterialComplete(Auth::user());
        }
    }
}
