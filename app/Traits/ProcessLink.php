<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

trait ProcessLink
{
    private const string WEBINAR_API_BASE_URL = 'https://userapi.mts-link.ru/v3/';
    private const string WEBINAR_API_TOKEN = '06d78833fa3fcf3bbaca4770cd78499b';
    private const string VIDEO_BASE_URL = 'https://video.instudy.online/webinar/';
    private const string MTS_LINK_BASE_URL = 'https://my.mts-link.ru/';

    public function processRouteLink(array &$data, Carbon $date, bool $isUniversityEvents = false): ?string
    {
        $now = Carbon::now();
        $data['online'] = (int)$data['online'];
        $cancel = (bool)$data['cancel'];

        if ($cancel) {
            return $this->handleCancelledEvent($data, $date, $now);
        }

        if ($date >= $now) {
            return $this->handleFutureEvent($data, $isUniversityEvents);
        }

        return $this->handlePastEvent($data);
    }

    private function handleCancelledEvent(array &$data, Carbon $date, Carbon $now): ?string
    {
        $data['url'] = null;
        $data['is_passed'] = $date < $now;
        $data['auto_reg_link'] = null;
        return null;
    }

    private function handleFutureEvent(array &$data, bool $isUniversityEvents): ?string
    {
        $data['is_passed'] = false;
        $data['online'] = 1;

        if (empty($data['url'])) {
            $data['auto_reg_link'] = null;
            return null;
        }

        $data['auto_reg_link'] = !$isUniversityEvents;
        return $data['url'];
    }

    private function handlePastEvent(array &$data): ?string
    {
        $data['is_passed'] = true;
        $data['auto_reg_link'] = null;

        if (!empty($data['mp4'])) {
            return self::VIDEO_BASE_URL . "{$data['mp4']}.mp4";
        }

        if (!empty($data['webinar'])) {
            return $data['webinar'];
        }

        if (!isset($data['webinar'], $data['mp4'])) {
            return null;
        }

        return null;
    }

    private function autoRegLink(string $url): string
    {
        if (!str_contains($url, 'webinar.ru') && !str_contains($url, 'mts-link.ru')) {
            return $url;
        }

        // Заменяем домен webinar.ru на mts-link.ru если он присутствует
        if (str_contains($url, 'webinar.ru')) {
            $url = str_replace('events.webinar.ru', 'my.mts-link.ru/j', $url);
        }

        $eid = $this->extractEventId($url);
        if (!$eid) {
            return $url;
        }

        $event = $this->webinarRequest('GET', 'organization/events/' . $eid);
        $sid = $event['eventSessions'][0]['id'] ?? null;
        if (!$sid) {
            return $url;
        }

        $profile = Auth::user();
        if (!$profile) {
            return $url;
        }

        $registrationData = $this->buildRegistrationData($profile, $sid);
        $reg = $this->webinarRequest('POST', 'eventsessions/' . $sid . '/register', $registrationData);

        if ($this->isAlreadyRegistered($reg)) {
            return $this->handleExistingRegistration($sid, $profile->user->email, $profile, $url);
        }

        return $reg['link'] ?? $url;
    }

    private function extractEventId(string $url): ?string
    {
        $parts = explode('/', rtrim($url, '/'));
        $eid = end($parts);
        return is_numeric($eid) ? $eid : null;
    }

    private function buildRegistrationData(object $profile, string $sid): array
    {
        $user = $profile->user;
        $group = $profile->group ?? '';

        return [
            'eventSessionId' => $sid,
            'isAutoEnter' => 'true',
            'email' => $user->email,
            'role' => 'GUEST',
            'name' => $user->first_name ?? '',
            'secondName' => $user->last_name ?? '',
            'organization' => 'МИП',
            'position' => 'Тестировка. Группа ' . $group,
        ];
    }

    private function isAlreadyRegistered(array $reg): bool
    {
        return isset($reg['error']['code']) && $reg['error']['code'] === 409;
    }

    private function handleExistingRegistration(string $sid, string $userEmail, object $profile, string $fallbackUrl): string
    {
        $participations = $this->webinarRequest('GET', 'eventsessions/' . $sid . '/participations');
        foreach ($participations as $participation) {
            if (($participation['email'] ?? '') === $userEmail) {
                $this->updateContactInfo($userEmail, $profile);
                return $this->generateWebinarLink($participation) ?? $fallbackUrl;
            }
        }

        return $fallbackUrl;
    }

    private function updateContactInfo(string $userEmail, object $profile): void
    {
        $contactSearchData = ['contactsData' => ['email' => $userEmail]];
        $contacts = $this->webinarRequest('GET', 'contacts/search', $contactSearchData);

        foreach ($contacts as $contact) {
            if (($contact['email'] ?? '') === $userEmail) {
                $user = $profile->user;
                $contactUpdateData = [
                    'name' => $user->first_name ?? '',
                    'secondName' => $user->last_name ?? '',
                    'company' => $user->org,
                    'position' => 'Тестировка. Группа ' . ($profile->group ?? ''),
                ];
                $this->webinarRequest('PUT', 'contacts/' . $contact['id'], $contactUpdateData);
                break;
            }
        }
    }

    private function generateWebinarLink(array $participation): ?string
    {
        $wauthor = $this->webinarRequest('GET', 'eventsessions/' . $participation['eventSessionId']);

        if (!isset($wauthor['createUser']['id'], $participation['eventId'], $participation['url'])) {
            return null;
        }

        return self::MTS_LINK_BASE_URL
            . $wauthor['createUser']['id'] . '/'
            . $participation['eventId'] . '/'
            . $participation['url'];
    }

    /**
     * Возвращает список транскрибаций вебинара по идентификатору сессии события (eventSessionId).
     * Каждая запись массива — это результат ответа API по эндпоинту transcript/{id}.
     *
     * @param int|string $eventSessionId
     * @return array
     */
    public function getMTSLinkWebinarTranscripts(int|string $eventSessionId): array
    {
        $items = $this->getMTSLinkTranscriptionList($eventSessionId);
        if (empty($items) || !is_array($items)) {
            return [];
        }

        $transcripts = [];
        foreach ($items as $item) {
            $transcriptId = $item['id'] ?? null;
            if (!$transcriptId) {
                continue;
            }

            $transcript = $this->webinarRequest('GET', 'transcript/' . $transcriptId);
            if (!empty($transcript)) {
                $transcripts[] = $transcript;
            }
        }

        return $transcripts;
    }

    public function getMTSLinkTranscriptionList(int|string $eventSessionId): array
    {
        $listResponse = $this->webinarRequest('GET', 'eventsessions/' . $eventSessionId . '/transcript/list');

        $items = $listResponse['data']['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            return [];
        }

        return $items;
    }

    private function webinarRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = self::WEBINAR_API_BASE_URL . $endpoint;
        $headers = ['x-auth-token' => self::WEBINAR_API_TOKEN];

        try {
            $response = match (strtoupper($method)) {
                'GET' => Http::withHeaders($headers)->get($url, $data),
                'POST' => Http::withHeaders($headers)->post($url, $data),
                'PUT' => Http::withHeaders($headers)->put($url, $data),
                'DELETE' => Http::withHeaders($headers)->delete($url, $data),
                default => throw new RuntimeException("Unsupported HTTP method: $method"),
            };

            $statusCode = $response->status();
            $responseData = $response->json() ?? [];

            // Логгирование запроса к MTS Link API
            Log::channel('mts_link_requests')->info('MTS Link API Request', [
                'method' => $method,
                'url' => $url,
                'endpoint' => $endpoint,
                'status_code' => $statusCode,
                'request_data' => $data,
                'response_data' => $responseData,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()?->user->email,
                'timestamp' => now()->toISOString(),
            ]);

            return $responseData;
        } catch (RuntimeException|ConnectionException $e) {
            // Логгирование ошибки MTS Link API
            Log::channel('mts_link_requests')->error('MTS Link API Error', [
                'method' => $method,
                'url' => $url,
                'endpoint' => $endpoint,
                'error_message' => $e->getMessage(),
                'request_data' => $data,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()?->user->email,
                'timestamp' => now()->toISOString(),
            ]);

            return [];
        }
    }
}
