<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== $role) {
            // Friendly redirect to the user's corresponding dashboard if they don't have this role
            $userRole = $request->user()->role;
            if ($userRole === 'super_admin') {
                return redirect('/admin/dashboard');
            } elseif ($userRole === 'officer') {
                return redirect('/oc/dashboard');
            } else {
                return redirect('/citizen/my-complaints');
            }
        }

        if ($role === 'officer' && ! $request->user()->officer?->is_oc) {
            abort(403, 'OC access has not been assigned to this officer account.');
        }

        return $next($request);
    }
}
