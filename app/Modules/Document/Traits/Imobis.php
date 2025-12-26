<?php

namespace App\Modules\Document\Traits;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Psr\SimpleCache\InvalidArgumentException;

trait Imobis
{
    private string $baseUrl;
    private string $apiToken;

    public function sendSMS(
        string $phone,
        string $email,
        string $messageTemplate = 'Ваш код для новой эл. подписи: %s'
    ): ?array
    {
        try {
            $code = sprintf('%06d', random_int(0, 999999));
            $cacheKey = 'profile_' . Auth::user()->id . '_confirmation_code:' . $code;

            $cleanPhone = preg_replace('/\D/', '', $phone);

            $isValidPhone = strlen($cleanPhone) === 11 &&
                str_starts_with($cleanPhone, '7') &&
                $cleanPhone[1] === '9';

            Cache::put($cacheKey, $code, 180);
            $message = sprintf($messageTemplate, $code);

            if ($isValidPhone) {
                $data = [
                    'sender' => 'INPSYCHO',
                    'phone' => $cleanPhone,
                    'text' => $message,
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Token ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ])->withBody(
                    json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
                )->post(
                    $this->baseUrl . 'message/sendSMS'
                );

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'device' => 'phone'
                    ];
                }

                return null;
            }

            Mail::raw($message, static function ($message) use ($email) {
                $message->to($email)
                    ->subject('Код подтверждения InStudy');
            });

            return [
                'success' => true,
                'device' => 'email'
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function confirmCode(string $code): bool
    {
        $cacheKey = 'profile_' . Auth::user()->id . '_confirmation_code:' . $code;
        $storedCode = Cache::get($cacheKey);

        if ($storedCode === null) {
            return false;
        }

        if ($storedCode === $code) {
            Cache::delete($cacheKey);
            Cache::delete('profile_' . Auth::user()->id . '_contacts');
            return true;
        }

        return false;
    }
}
