<?php

namespace App\Http\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions as BaseExceptions;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler
{
    protected int $jsonFlags = JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE;

    public function __invoke(BaseExceptions $exceptions): BaseExceptions
    {
        $this->renderUnauthorized($exceptions);
        $this->renderNotFound($exceptions);

        return $exceptions;
    }

    protected function renderUnauthorized(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(
            fn(AuthenticationException $e, ?Request $request = null) => $this->response(
                message: __('Unauthorized'),
                code: 401
            )
        );
    }

    protected function response(string $message, int $code): Response
    {
        return response()->json(['message' => $message], $code, options: $this->jsonFlags);
    }

    protected function renderNotFound(BaseExceptions $exceptions): void
    {
        $exceptions->renderable(
            fn(NotFoundHttpException $e, ?Request $request = null) => $this->response(
                message: __('Not Found'),
                code: 404
            )
        );
    }
}
