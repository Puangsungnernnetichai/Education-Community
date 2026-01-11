<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $toast = [
            'type' => 'success',
            'message' => 'Login successfully.',
        ];

        $redirectTo = $request->string('redirect_to')->toString();
        if ($redirectTo !== '') {
            $host = parse_url($redirectTo, PHP_URL_HOST);
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);

            if (! $host || ($appHost && $host === $appHost)) {
                return redirect()->to($redirectTo)->with('toast', $toast);
            }
        }

        return redirect()->route('home')->with('toast', $toast);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('toast', [
            'type' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }
}
