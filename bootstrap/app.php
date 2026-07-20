<?php

use App\Services\ErrorHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptionService = app()->make(ErrorHandler::class);
        $exceptions->render(function (Throwable $e, Request $request) use ($exceptionService) {
            if ($request->is('api/*') || ($request->getHost() === config('app.api_domain'))) {
                if (app()->isDownForMaintenance()) {
                    return $exceptionService->maintenanceResponse();
                }
                return $exceptionService->handleException($e);
            }
        });
    })->create();
