<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\Stub\Exception;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * @throws AuthenticationException
     */
    protected function redirectTo(Request $request): ?Exception
    {
        if ($request->expectsJson()) {
            return null;
        }

        throw new AuthenticationException;
    }
}
