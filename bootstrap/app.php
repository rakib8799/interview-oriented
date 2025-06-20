<?php

use App\Http\Middleware\AdminRole;
use App\Http\Middleware\CheckAge;
use App\Http\Middleware\EnsureJsonAcceptHeader;
use App\Http\Middleware\LogRequests;
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
        // Global middleware
        // $middleware->append(
        //     LogRequests::class
        // );

        // API-wise global middleware
        $middleware->api([
            EnsureJsonAcceptHeader::class
        ]);

        // Web-wise global middleware
        $middleware->web([
            LogRequests::class
        ]);

        // Group middleware
        $middleware->group('admin', [
            AdminRole::class
        ]);

        // Middleware alias
        $middleware->alias([
            'check.age' => CheckAge::class,
            'role.admin' => AdminRole::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
