<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            if (auth()->check()) {
                $role = auth()->user()->role;
                return match ($role) {
                    'superadmin' => '/superadmin/dashboard',
                    'kaur' => '/kaur/dashboard',
                    'kabag' => '/kabag/dashboard',
                    'pengelola' => '/pengelola/dashboard',
                    'tenant' => '/tenant/dashboard',
                    'pelanggan' => '/pelanggan/dashboard',
                    default => '/login',
                };
            }
            return '/login';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
