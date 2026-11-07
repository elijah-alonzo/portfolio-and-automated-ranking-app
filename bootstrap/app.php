<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        // Signed-out visitors land on the sign-in page; signed-in ones skip it.
        $middleware->redirectGuestsTo('/');
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->isAdmin() ? '/admin' : '/app');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
