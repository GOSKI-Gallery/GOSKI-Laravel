<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->append(SecurityHeadersMiddleware::class);

        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'guest' => RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e) {
            return redirect('/')->with('error', 'Sua sessão expirou. Faça login novamente.');
        });
    })->create();
