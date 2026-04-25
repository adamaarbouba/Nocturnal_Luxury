<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (in_array($request->user()->role->slug, $roles)) {
            return $next($request);
        }

        return redirect()->route($request->user()->dashboardRoute())->with('warning', 'You do not have access to that resource.');
    }
}
