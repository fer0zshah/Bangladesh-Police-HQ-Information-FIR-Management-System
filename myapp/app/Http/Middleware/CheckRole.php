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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
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
            } elseif ($userRole === 'station_oc') {
                return redirect('/oc/dashboard');
            } else {
                return redirect('/citizen/my-complaints');
            }
        }

        return $next($request);
    }
}
