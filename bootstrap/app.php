<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Регистрация route middleware
        $middleware->alias([
            'staff.guest' => \App\Http\Middleware\RedirectIfStaff::class,
            'staff.auth' => \App\Http\Middleware\StaffAuthenticated::class,
            'concurrent.logins' => \App\Http\Middleware\PreventConcurrentLogins::class,
            // Другие route middleware...
        ]);
        
        // Можно также добавить middleware в группы, если нужно
        $middleware->appendToGroup('web', [
            // \App\Http\Middleware\ExampleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();