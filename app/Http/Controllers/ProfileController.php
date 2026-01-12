<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $stats = GameSession::query()
            ->where('user_id', $request->user()->id)
            ->selectRaw('COUNT(*) as total_played')
            ->selectRaw('COALESCE(MAX(score), 0) as highest_score')
            ->selectRaw('COALESCE(AVG(score), 0) as average_score')
            ->first();

        return view('profile.edit', [
            'user' => $request->user(),
            'gameStats' => [
                'total_played' => (int) ($stats->total_played ?? 0),
                'highest_score' => (int) ($stats->highest_score ?? 0),
                'average_score' => (float) ($stats->average_score ?? 0),
            ],
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
