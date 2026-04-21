<?php

namespace App\Http\Controllers;

use App\Models\{ActivityLog, MaterialView,MaterialProgress};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function logActivity(Request $request) {
        return response()->json(ActivityLog::create(['user_id' => Auth::id(), 'activity' => $request->activity, 'metadata' => $request->metadata]));
    }

    public function trackProgress(Request $request, $materialId) {
        MaterialView::create(['user_id' => Auth::id(), 'material_id' => $materialId, 'seconds_spent' => $request->seconds]);
        $progress = MaterialProgress::updateOrCreate(['user_id' => Auth::id(), 'material_id' => $materialId], ['is_completed' => $request->completed ?? false]);
        return response()->json($progress);
    }

    public function getActivityLogs()
    {
        $logs = ActivityLog::where('user_id', Auth::id())->latest()->get();
        return response()->json($logs);
    }
}
