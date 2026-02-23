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
        // Alias middleware untuk handle role
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
    // // Handle 403 Forbidden
    //     $exceptions->render(function ($request, \Symfony\Component\HttpFoundation\Response $response) {
    //         if ($response->getStatusCode() === 403) {
    //             return response()->view('errors.403', [], 403);
    //         }
    //         return $response;
    //     });
        
    //     // Handle 404 Not Found
    //     $exceptions->render(function ($request, \Symfony\Component\HttpFoundation\Response $response) {
    //         if ($response->getStatusCode() === 404) {
    //             return response()->view('errors.404', [], 404);
    //         }
    //         return $response;
    //     });

    })->create();
