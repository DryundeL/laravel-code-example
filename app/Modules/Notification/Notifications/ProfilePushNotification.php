<?php

namespace App\Modules\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Modules\Notification\Channels\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ProfilePushNotification extends Notification
{
    use Queueable;

    private array $data;
    private string $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data, string $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        $channels = [WebPushChannel::class];

        // Проверяем наличие конфигурации OneSignal
        $oneSignalAppId = config('services.onesignal.app_id');
        $oneSignalApiKey = config('services.onesignal.rest_api_key');

        if ($oneSignalAppId && $oneSignalApiKey) {
            $channels[] = OneSignalChannel::class;
        }

        return $channels;
    }

    public function toWebPush($notifiable)
    {
        switch ($this->type) {
            case 'message':
                $title = $this->data['from']['fullName'];
                $icon = $this->data['from']['photoUrl'] ?? config('inStudy.yandex_s3_url') . 'icons/avatar.png';
                $fromExternalId = $this->data['fromExternalId'] ?? $this->data['from']['externalId'] ?? $this->data['from_external_id'] ?? null;
                $url = $fromExternalId
                    ? config('inStudy.landing_url') . '/chats/' . $fromExternalId
                    : config('inStudy.landing_url') . '/chats';
                break;
            case 'notification':
                $title = $this->data['title'];
                $icon = $this->data['photoUrl'];
                $url = config('inStudy.landing_url');
                break;
        }

        return (new WebPushMessage)
            ->title($title)
            ->icon($icon)
            ->body($this->data['text'])
            ->action('Перейти', 'open_url')
            ->data(['url' => $url]);
    }

    /**
     * Получить представление уведомления для OneSignal
     *
     * @param mixed $notifiable
     * @return OneSignalMessage
     */
    public function toOneSignal($notifiable): OneSignalMessage
    {
        try {
            switch ($this->type) {
            case 'message':
                // Используем fullName, если отдельные поля недоступны
                if (isset($this->data['from']['lastName'], $this->data['from']['firstName'])) {
                    $title = trim(
                        ($this->data['from']['lastName'] ?? '') . ' ' .
                        ($this->data['from']['firstName'] ?? '') . ' ' .
                        ($this->data['from']['middleName'] ?? '')
                    );
                } else {
                    $title = $this->data['from']['fullName'] ?? 'Новое сообщение';
                }
                $icon = $this->data['from']['photoUrl'] ?? config('inStudy.yandex_s3_url') . 'icons/avatar.png';
                $fromExternalId = $this->data['fromExternalId'] ?? $this->data['from']['externalId'] ?? $this->data['from_external_id'] ?? null;
                $url = $fromExternalId
                    ? config('inStudy.landing_url') . '/chats/' . $fromExternalId
                    : config('inStudy.landing_url') . '/chats';
                break;
            case 'notification':
                $title = $this->data['title'] ?? 'Уведомление';
                $icon = $this->data['photoUrl'] ?? null;
                $url = config('inStudy.landing_url');
                break;
            default:
                $title = 'Уведомление';
                $icon = null;
                $url = config('inStudy.landing_url');
        }

        $message = OneSignalMessage::create()
            ->setSubject($title)
            ->setBody($this->data['text'])
            ->setData('type', $this->type)
            ->setData('url', $url);

        if ($icon) {
            $message->setParameter('large_icon', $icon);
        }

        if ($url) {
            $message->setParameter('url', $url);
        }

        return $message;
        } catch (\Throwable $e) {
            Log::channel('notifications')->error('OneSignal notification error', [
                'profile_id' => $notifiable->id ?? null,
                'type' => $this->type,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
