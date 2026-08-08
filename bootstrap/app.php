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
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'admin_access' => \App\Http\Middleware\EnsureAdminAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
            $statusCode = $response->getStatusCode();

            if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
                $statusCode = 403;
            }

            if (in_array($statusCode, [403, 404, 500, 503])) {
                return \Inertia\Inertia::render('Error', [
                    'status' => $statusCode,
                    'message' => $exception->getMessage() ?: null,
                ])
                ->toResponse($request)
                ->setStatusCode($statusCode);
            }

            return $response;
        });
    })->create();
