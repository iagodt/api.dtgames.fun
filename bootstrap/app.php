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
        $middleware->validateCsrfTokens(except: [
            '/web-api/auth/session/v2/verifyOperatorPlayerSession',
            '/web-api/auth/session/v2/verifySession',
            '/web-api/game-proxy/v2/GameName/Get',
            '/web-api/game-proxy/v2/*',
            '/game-api/*/v2/GameInfo/Get',
            '/game-api/*/v2/Spin',
            '/testealgoritmos'
        ]
        
    
    );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
