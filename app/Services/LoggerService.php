<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoggerService
{
    /**
     * @throws \JsonException
     */
    public function log(array $body, ?array $response, bool $error, Request $request = null): void
    {
        $profile = Auth::user();

        $logData = [
            'profile_id' => $profile?->id,
            'user_email' => $profile?->user->email,
            'request_method' => $request?->method(),
            'request_url' => $request?->fullUrl(),
            'body' => $body,
            'response' => $response,
        ];

        $jsonLog = json_encode($logData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        Log::channel('esb_requests')->{$error ? 'error' : 'info'}($jsonLog);
    }
}
