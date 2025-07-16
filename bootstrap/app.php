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
    ->withMiddleware(function (Middleware $middleware): void {
        // 配置 CSRF 驗證排除
        $middleware->validateCsrfTokens(except: [
            '/upload/image',
            '/test/upload'
        ]);
        
        // 配置 CORS
        $middleware->api(append: [
            \App\Http\Middleware\CorsMiddleware::class,
        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\CorsMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
