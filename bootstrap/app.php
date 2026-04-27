<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware - runs on every request
        $middleware->use([
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\TriggerCleanupMiddleware::class,
        ]);

        $middleware->web(append: [
            \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        $middleware->alias([
            'no-cache' => \App\Http\Middleware\PreventBackHistory::class,
            'check-profile' => \App\Http\Middleware\EnsureProfileIsComplete::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'check-active' => \App\Http\Middleware\CheckAccountStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
