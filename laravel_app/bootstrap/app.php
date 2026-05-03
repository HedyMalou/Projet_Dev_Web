<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.check' => \App\Http\Middleware\CheckAuth::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\LogActivite::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })->create();
