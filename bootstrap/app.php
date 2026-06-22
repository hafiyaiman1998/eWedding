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
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Exclude RSVP routes from CSRF protection since they are public routes
        $middleware->validateCsrfTokens(except: [
            'wedding-card/*/rsvp',
            'wedding-card/*/rsvp/*',
            'wedding-card/*/rsvp/check-email',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
