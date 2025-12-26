<?php

namespace App\Traits;

use App\Services\LoggerService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use JsonException;

trait iESBRequest
{
    use KeysCaseConverter;

    abstract protected function getLogger(): LoggerService;

    abstract protected function getEsbUrl(): string;

    abstract protected function getEsbAdapter(): string;

    private function sendESBRequest(string $bus, array $payloadData): ?array
    {
        $token = $this->getTokenFromRedis();

        if (!$token) {
            $token = $this->authenticateESB();
        }

        $payload = $this->preparePayload($payloadData);

        if (!$token || !is_string($token) || empty(trim($token))) {
            $this->logError($bus, $payloadData['payload'] ?? [], 'Token отсутствует или невалиден');
            return null;
        }

        try {
            $requestBody = [
                'token' => $token,
                'adapter' => $this->getEsbAdapter(),
                'bus' => $bus,
                'payload' => $payload,
            ];

            $response = Http::timeout(60)->post($this->getEsbUrl(), $requestBody);
            $responseData = $response->json();
            $isError = !$response->successful() || ($responseData['error'] ?? false);

            $this->getLogger()->log(
                $requestBody,
                $responseData,
                $isError,
                Request::instance()
            );

            if ($isError) {
                $text = $responseData['text'] ?? '';
                if (!preg_match('/[a-zA-Z]/', $text)) {
                    return $this->convertKeysToCamelCase($responseData);
                }
                return null;
            }

            return $this->convertKeysToCamelCase($responseData);

        } catch (ConnectionException $e) {
            $this->logError($bus, $payload, $e->getMessage());
            return null;
        }
    }

    private function logError(string $bus, array $payload, string $errorMessage = null): void
    {
        $logData = [
            'bus' => $bus,
            'payload' => $payload,
            'error_message' => $errorMessage,
        ];

        $this->getLogger()->log(
            $logData,
            null,
            true,
            Request::instance()
        );

        if ($errorMessage) {
            Log::error("ESB Connection Error: {$errorMessage}", [
                'bus' => $bus,
                'payload' => $payload,
            ]);
        }
    }

    private function preparePayload(array $payloadData): array
    {
        $profile = $payloadData['profile'] ?? Auth::user();
        $includeIdent = $payloadData['includeIdent'] ?? false;

        if (!$payloadData['ESBId'] && $includeIdent) {
            $payloadData['ESBId'] = $profile->external_id;
        }

        if (!$payloadData['ESBId'] || !$includeIdent) {
            return $payloadData['payload'];
        }

        $identKeys = $payloadData['identKeys'];
        $cachedData = Cache::get('external_profile_' . $payloadData['ESBId']);

        if (!empty($cachedData)) {
            try {
                $addInfo = json_decode($cachedData, true, 512, JSON_THROW_ON_ERROR);

                if (is_array($addInfo) && $identKeys) {
                    $addInfo = array_intersect_key(
                        $addInfo,
                        array_flip($identKeys)
                    );
                }
            } catch (JsonException $e) {
                Log::error("Ошибка декодирования JSON для ключа external_profile_{$payloadData['ESBId']}: " . $e->getMessage());
                $addInfo = null;
            }
        } else {
            $addInfo = null;
        }

        if (in_array('gid', $identKeys)) {
            if ($profile?->gid) {
                if ($addInfo === null) {
                    $addInfo = [];
                }
                $addInfo['gid'] = $profile->gid;
            } else {
                Log::warning('GID не найден в профиле пользователя', [
                    'profile_id' => $profile?->id,
                    'identKeys' => $identKeys
                ]);
            }
        }

        return array_merge($payloadData['payload'], ['ident' => $addInfo]);
    }

    private function getTokenFromRedis(): ?string
    {
        $token = Cache::get('esb_token');

        if (!$token || !is_string($token) || empty(trim($token))) {
            return null;
        }

        return $token;
    }

    private function authenticateESB(): bool|string
    {
        $url = $this->getEsbUrl();

        $credentials = [
            'login' => 'instudy2',
            'password' => 'TH8!HLp3Ujk#0',
        ];

        try {
            $response = Http::post($url, $credentials);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['payload']['token']) && $data['error'] === false) {
                    $token = $data['payload']['token'];

                    if (empty($token) || !is_string($token)) {
                        Log::error('ESB Authentication: получен пустой или невалидный токен.', [
                            'response' => $response->body(),
                        ]);
                        return false;
                    }

                    $ttl = (int)config('inStudy.ESB_token_ttl');
                    Cache::set('esb_token', $token, $ttl);
                    Log::info('ESB Authentication succeeded. Token saved to Redis.');

                    return $token;
                }

                Log::error('ESB Authentication response indicates failure.', [
                    'response' => $response->body(),
                ]);
            } else {
                Log::error('ESB Authentication HTTP request failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception during ESB Authentication.', [
                'message' => $e->getMessage(),
            ]);
        }

        return false;
    }
}
