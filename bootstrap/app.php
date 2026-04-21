<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirect 404s to user dashboard when authenticated
        $exceptions->render(function (\Illuminate\Http\Exceptions\HttpResponseException|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->user() && $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                $roleMap = [
                    'admin' => 'admin.dashboard',
                    'owner' => 'owner.dashboard',
                    'receptionist' => 'receptionist.dashboard',
                    'cleaner' => 'staff.dashboard',
                    'inspector' => 'inspector.dashboard',
                    'guest' => 'guest.dashboard',
                ];

                $userRole = $request->user()->role->slug;
                $dashboardRoute = $roleMap[$userRole] ?? 'dashboard';

                return redirect()->route($dashboardRoute);
            }
        });
    })->create();
