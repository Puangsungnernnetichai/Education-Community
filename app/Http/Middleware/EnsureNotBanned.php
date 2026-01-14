<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User && $user->banned_at) {
            // Auto-expire timed bans.
            if ($user->banned_until && $user->banned_until->isPast()) {
                $user->update([
                    'banned_at' => null,
                    'banned_until' => null,
                    'ban_reason' => null,
                ]);

                return $next($request);
            }

            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $data = [
                'reason' => (is_string($user->ban_reason) && trim($user->ban_reason) !== '') ? trim($user->ban_reason) : null,
                'banned_until' => $user->banned_until?->toIso8601String(),
                'banned_until_human' => $user->banned_until?->diffForHumans(),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'error' => 'banned',
                    'message' => 'บัญชีนี้ถูกระงับการใช้งาน',
                    'data' => $data,
                ], 403);
            }

            return redirect()->route('banned.notice')->with('banned', $data);
        }

        return $next($request);
    }
}
