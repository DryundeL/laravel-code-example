<?php

namespace App\Modules\Notification\Channels;

use Berkayk\OneSignal\OneSignalClient;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\OneSignal\Exceptions\CouldNotSendNotification;
use NotificationChannels\OneSignal\OneSignalPayloadFactory;
use Psr\Http\Message\ResponseInterface;

class OneSignalChannel
{
    /** @var OneSignalClient */
    protected $oneSignal;

    public function __construct(OneSignalClient $oneSignal)
    {
        $this->oneSignal = $oneSignal;
    }
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return \Psr\Http\Message\ResponseInterface|null
     *
     * @throws \NotificationChannels\OneSignal\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification): ?ResponseInterface
    {
        if (! $userIds = $notifiable->routeNotificationFor('OneSignal', $notification)) {
            return null;
        }

        $payload = $this->payload($notifiable, $notification, $userIds);
        $payload = $this->convertCollectionsToArrays($payload);

        /** @var ResponseInterface $response */
        $response = $this->oneSignal->sendNotificationCustom($payload);

        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();
        $response->getBody()->rewind();

        $responseData = json_decode($responseBody, true) ?: [];

        // Проверяем наличие ошибок в ответе (даже при статусе 200)
        if (isset($responseData['errors']) && !empty($responseData['errors'])) {
            $errors = is_array($responseData['errors']) ? $responseData['errors'] : [$responseData['errors']];

            if (in_array('All included players are not subscribed', $errors)) {
                Log::channel('notifications')->warning('OneSignal: all players are not subscribed', [
                    'profile_id' => $notifiable->id ?? null,
                    'player_ids' => $userIds,
                ]);
                return $response;
            }

            Log::channel('notifications')->error('OneSignal API errors in response', [
                'profile_id' => $notifiable->id ?? null,
                'status_code' => $statusCode,
                'errors' => $errors,
            ]);
        }

        if ($statusCode !== 200) {
            Log::channel('notifications')->error('OneSignal API error', [
                'profile_id' => $notifiable->id ?? null,
                'status_code' => $statusCode,
                'response' => $responseData
            ]);
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }

        return $response;
    }

    /**
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @param  mixed  $targeting
     * @return array
     */
    protected function payload($notifiable, Notification $notification, $targeting): array
    {
        return OneSignalPayloadFactory::make($notifiable, $notification, $targeting);
    }

    /**
     * Преобразует Collections в массивы для правильной сериализации JSON
     *
     * @param mixed $data
     * @return mixed
     */
    protected function convertCollectionsToArrays($data)
    {
        if ($data instanceof \Illuminate\Support\Collection) {
            return $data->toArray();
        }

        if (is_array($data)) {
            return array_map([$this, 'convertCollectionsToArrays'], $data);
        }

        return $data;
    }
}

