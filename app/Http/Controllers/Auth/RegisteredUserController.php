<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER,
        ]);

        event(new Registered($user));

        // Log in using a fresh instance from the database to ensure persistence.
        Auth::login($user->fresh());

        $toast = [
            'type' => 'success',
            'message' => 'Registered successfully.',
            'action' => 'reload',
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
}
