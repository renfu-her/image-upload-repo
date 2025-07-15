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
        // 配置 CSRF 驗證排除
        $middleware->validateCsrfTokens(except: [
            '/upload/image',
            '/test/upload'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
