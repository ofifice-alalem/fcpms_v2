<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Display the spatial glassmorphic login view.
     */
    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle authentication login attempt.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $this->authService->authenticate(
            login: $request->validated('login'),
            password: $request->validated('password'),
            remember: (bool) $request->validated('remember', false)
        );

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy authenticated session and logout user.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
