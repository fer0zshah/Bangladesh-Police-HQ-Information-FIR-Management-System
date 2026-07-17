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
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! in_array($request->user()->role, $roles, true)) {
            // Friendly redirect to the user's corresponding dashboard if they don't have this role
            $userRole = $request->user()->role;
            return match ($userRole) {
                'super_admin' => redirect('/admin/dashboard'),
                'station_oc' => redirect('/oc/dashboard'),
                'citizen' => redirect('/citizen/my-complaints'),
                'metro_head', 'district_head' => redirect('/stations'),
                default => redirect('/'),
            };
        }

        if (in_array('station_oc', $roles, true) && ! $request->user()->officer?->is_oc) {
            abort(403, 'OC access has not been assigned to this officer account.');
        }

        return $next($request);
    }
}
