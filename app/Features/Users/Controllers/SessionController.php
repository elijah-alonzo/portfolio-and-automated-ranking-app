<?php

namespace App\Features\Users\Controllers;

use App\Features\Landing\Actions\GetLandingStats;
use App\Features\Users\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The sign-in page at `/`, signing in and signing out.
 */
final class SessionController
{
    public function create(GetLandingStats $stats): Response
    {
        return Inertia::render('Login', ['stats' => $stats()]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->safe()->only(['email', 'password']), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'That email and password don\'t match an account.',
            ]);
        }

        $request->session()->regenerate();

        // Admins run the system from the admin panel; everyone else works in the app.
        return $request->user()->isAdmin()
            ? redirect()->intended('/admin')
            : redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
