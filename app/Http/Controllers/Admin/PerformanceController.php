<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiRequest;
use App\Models\RequestLog;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('accessAdmin');

        $since = now()->subDay();

        $aiStats = [
            'total' => AiRequest::query()->where('created_at', '>=', $since)->count(),
            'errors' => AiRequest::query()->where('created_at', '>=', $since)->where('status', 'error')->count(),
            'avg_latency_ms' => (int) round((float) (AiRequest::query()->where('created_at', '>=', $since)->avg('latency_ms') ?? 0)),
        ];

        $recentAi = AiRequest::query()
            ->with('user')
            ->latest()
            ->take(30)
            ->get();

        $slowRequests = RequestLog::query()
            ->with('user')
            ->where('created_at', '>=', $since)
            ->orderByDesc('duration_ms')
            ->take(30)
            ->get();

        return view('admin.performance.index', [
            'aiStats' => $aiStats,
            'recentAi' => $recentAi,
            'slowRequests' => $slowRequests,
        ]);
    }
}
