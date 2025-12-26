<?php

use App\Console\Handler as ScheduleHandler;
use App\Http\Exceptions\Handler as ExceptionHandler;
use App\Http\Middleware\Handler as MiddlewareHandler;
use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(new MiddlewareHandler())
    ->withExceptions(new ExceptionHandler())
    ->withSchedule(new ScheduleHandler())
    ->withCommands([__DIR__ . '/../app/Console/Commands'])
    ->create();
