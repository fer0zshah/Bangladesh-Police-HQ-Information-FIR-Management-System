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
    public function create(): View
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

        // Get the logged-in user's role
        $role = $request->user()->role;

        // Redirect based on role hierarchy
        if ($role === 'super_admin') {
            return redirect()->intended('/admin/dashboard');
        } elseif ($role === 'station_oc') {
            return redirect()->intended('/oc/dashboard');
        } elseif (in_array($role, ['metro_head', 'district_head'], true) && \Illuminate\Support\Facades\Route::has('command.dashboard')) {
            return redirect()->intended('/command/dashboard');
        } else {
            return redirect()->intended('/');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
