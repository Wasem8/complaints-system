<?php

use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [
            __DIR__ . '/../routes/api.php',


            __DIR__ . '/../routes/GCS/citizen.php',
            __DIR__ . '/../routes/GCS/employee.php',
            __DIR__ . '/../routes/GCS/admin.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' =>RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'verified' => EnsureEmailIsVerified::class,
            'check.status'=> \App\Http\Middleware\CheckUserStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions){
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e): string {
            return 'you dont have permissions';
        });
        $exceptions->render(function (
            ThrottleRequestsException $e,
            Request $request
        ) {
            return \App\Http\Responses\Response::Validation(false,'you have already reached the limit',429);

        });
    })->create();
