<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\Target;
use App\Jobs\CheckSslAndDomainJob;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✳️ Add your custom global middleware (runs for all routes)

        // ✳️ Add Sanctum for API (needed for React auth with cookies)
        // $middleware->api(prepend: [
        //     EnsureFrontendRequestsAreStateful::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // You can register custom exception handlers here
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('monitor:ping')->everyFiveMinutes();
        $schedule->command('monitor:check-expiry')->daily();
    })
    ->create();
