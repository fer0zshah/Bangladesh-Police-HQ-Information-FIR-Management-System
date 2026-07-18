<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();
            if ($user) {
                if ($user->role === 'super_admin') {
                    return '/admin/dashboard';
                } elseif (in_array($user->role, ['metro_head', 'district_head'], true)) {
                    return '/command/dashboard';
                } elseif ($user->role === 'station_oc') {
                    return '/oc/dashboard';
                }
            }
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if ($exception->getStatusCode() !== 419
                || ! $request->isMethod('post')
                || ! $request->is('logout')) {
                return null;
            }

            Auth::guard('web')->logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect('/');
        });
    })->create();
