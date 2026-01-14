<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manageUsers');

        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('manageUsers');

        $data = $request->validate([
            'role' => ['required', 'in:user,admin'],
        ]);

        $user->update(['role' => $data['role']]);

        AdminAuditLog::record($request->user(), 'user.role_updated', User::class, $user->id, [
            'role' => $data['role'],
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Updated role.',
        ]);
    }

    public function ban(Request $request, User $user)
    {
        $this->authorize('manageUsers');

        if ($request->user()?->id === $user->id) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'You cannot ban your own account.',
            ]);
        }

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:200'],
            // permanent|1h|1d|3d|7d|30d|custom
            'duration' => ['nullable', 'in:permanent,1h,1d,3d,7d,30d,custom'],
            // Only used when duration=custom (HTML datetime-local or any parseable date)
            'banned_until' => ['nullable', 'required_if:duration,custom', 'date', 'after:now'],
        ]);

        $duration = (string) ($data['duration'] ?? 'permanent');
        $until = null;

        if ($duration === 'custom') {
            if (isset($data['banned_until']) && is_string($data['banned_until'])) {
                // HTML datetime-local comes without timezone. Interpret as app timezone.
                $until = Carbon::createFromFormat('Y-m-d\\TH:i', $data['banned_until'], config('app.timezone'));
            } elseif (isset($data['banned_until'])) {
                $until = Carbon::parse($data['banned_until'], config('app.timezone'));
            }
        } elseif ($duration !== '' && $duration !== 'permanent') {
            $n = (int) preg_replace('/\D+/', '', $duration);
            if ($n > 0) {
                if (str_ends_with($duration, 'h')) {
                    $until = now()->addHours($n);
                } elseif (str_ends_with($duration, 'd')) {
                    $until = now()->addDays($n);
                }
            }
        }

        $user->update([
            'banned_at' => now(),
            'banned_until' => $until,
            'ban_reason' => $data['reason'] ?? null,
        ]);

        AdminAuditLog::record($request->user(), 'user.banned', User::class, $user->id, [
            'reason' => $data['reason'] ?? null,
            'banned_until' => $until?->toIso8601String(),
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'User banned.',
        ]);
    }

    public function unban(Request $request, User $user)
    {
        $this->authorize('manageUsers');

        $user->update([
            'banned_at' => null,
            'banned_until' => null,
            'ban_reason' => null,
        ]);

        AdminAuditLog::record($request->user(), 'user.unbanned', User::class, $user->id);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'User unbanned.',
        ]);
    }

    public function adjustPoints(Request $request, User $user)
    {
        $this->authorize('manageUsers');

        $data = $request->validate([
            'delta' => ['required', 'integer', 'min:-100000', 'max:100000'],
            'reason' => ['nullable', 'string', 'max:200'],
        ]);

        $before = (int) ($user->points ?? 0);
        $user->increment('points', (int) $data['delta']);
        $user->refresh();

        AdminAuditLog::record($request->user(), 'user.points_adjusted', User::class, $user->id, [
            'before' => $before,
            'delta' => (int) $data['delta'],
            'after' => (int) ($user->points ?? 0),
            'reason' => $data['reason'] ?? null,
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Adjusted points.',
        ]);
    }
}
