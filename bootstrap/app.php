<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $ex, $request) {
            if ($ex->getStatusCode() === 403) {
                // arahkan ke view custom 403.blade.php
                return response()->view('errors.403', [], 403);
            }

             if ($ex->getStatusCode() === 404) {
                return response()->view('errors.404', [], 404);
            }

        });

        //
    })->create();
