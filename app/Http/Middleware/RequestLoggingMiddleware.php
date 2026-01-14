<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestLoggingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('_start_time_ms', (int) round(microtime(true) * 1000));

        /** @var Response $response */
        $response = $next($request);

        return $response;
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            if ($this->shouldSkip($request)) {
                return;
            }

            $startMs = (int) ($request->attributes->get('_start_time_ms') ?? 0);
            $endMs = (int) round(microtime(true) * 1000);
            $durationMs = $startMs > 0 ? max(0, $endMs - $startMs) : null;

            $statusCode = method_exists($response, 'getStatusCode') ? (int) $response->getStatusCode() : null;

            $threshold = (int) config('services.monitoring.request_log_threshold_ms', 1500);
            $shouldLog = ($durationMs !== null && $durationMs >= $threshold) || ($statusCode !== null && $statusCode >= 500);

            if (! $shouldLog) {
                return;
            }

            RequestLog::create([
                'user_id' => $request->user()?->id,
                'method' => $request->getMethod(),
                'path' => '/' . ltrim($request->path(), '/'),
                'route_name' => optional($request->route())->getName(),
                'status_code' => $statusCode,
                'duration_ms' => $durationMs,
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            // Never break the request lifecycle for logging.
        }
    }

    private function shouldSkip(Request $request): bool
    {
        if ($request->isMethod('OPTIONS')) {
            return true;
        }

        if ($request->is('build/*') || $request->is('storage/*')) {
            return true;
        }

        $path = '/' . ltrim($request->path(), '/');
        if ($path === '/favicon.ico' || $path === '/robots.txt') {
            return true;
        }

        return false;
    }
}
