<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::prefix('api')
                ->middleware(['api', 'RequestAcceptJson'])
                ->group(base_path('routes/api.php'));

            Route::prefix('api')
                ->middleware(['api', 'RequestAcceptJson'])
                ->group(base_path('routes/user.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'RequestAcceptJson' => App\Http\Middleware\RequestAcceptJson::class,
            'scopesAny' => Laravel\Passport\Http\Middleware\CheckTokenForAnyScope::class,
            'scopes' => Laravel\Passport\Http\Middleware\CheckToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
