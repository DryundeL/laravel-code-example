<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @param mixed $result
     * @param int $code
     * @return JsonResponse
     */
    protected function sendResponse(mixed $result = [], int $code = 200): JsonResponse
    {
        return response()->json($result, $code);
    }

    /**
     * Error response method.
     *
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponse
     */
    protected function sendErrorResponse(array $errorMessages = [], int $code = 404): JsonResponse
    {
        return response()->json($errorMessages, $code);
    }
}
