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
    ->withMiddleware(function (Middleware $middleware) {
        // Confiar no Traefik/Nginx como proxy reverso
        $middleware->trustProxies(at: '*');
        $middleware->trustProxies(headers:
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR    |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST   |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT   |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );

        // DEVE ser o primeiro middleware — define o nome do cookie de sessão por subdomínio
        $middleware->prepend(\App\Http\Middleware\SetSessionCookieBySubdomain::class);

        // Locale baseado no usuário autenticado
        $middleware->append(\App\Http\Middleware\SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
