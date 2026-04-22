<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Map user roles to their dashboard routes
     */
    private const ROLE_DASHBOARDS = [
        'admin' => 'admin.dashboard',
        'owner' => 'owner.dashboard',
        'receptionist' => 'receptionist.dashboard',
        'staff' => 'staff.dashboard',
        'cleaner' => 'cleaner.dashboard',
        'inspector' => 'inspector.dashboard',
        'guest' => 'guest.dashboard',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role->slug;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Redirect to user's dashboard instead of showing error
        $dashboardRoute = self::ROLE_DASHBOARDS[$userRole] ?? 'dashboard';
        return redirect()->route($dashboardRoute)->with('warning', 'You do not have access to that resource.');
    }
}
