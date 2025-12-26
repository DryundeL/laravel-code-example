<?php

namespace App\Modules\Notification\Services;

use App\Modules\Chat\Models\Message;
use App\Services\BaseService;
use Exception;
use App\Modules\Notification\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Modules\User\Interfaces\DataTransfer\ProfileServiceInterface;
use App\Modules\Notification\Traits\NotificationHelper;
use App\Traits\LazyServiceLoader;
use App\Traits\Dates;
use App\Traits\FinanceProcessing;

class NotificationService extends BaseService
{
    use NotificationHelper, LazyServiceLoader, Dates, FinanceProcessing;
    /**
     * @throws Exception
     */
    public function getNotificationsCount(): Message|array
    {
        $payloadData = $this->prepareDataToESB(true);
        $countMessages = $this->handleRequestToESB('getUnreadMessageAmount', $payloadData, 'amount');

        if (isset($countMessages['errors'])) {
            return $this->getErrorMessage('chat', $countMessages['errors']['error'][0]);
        }

        $countNotifications = Notification::getAllNotificationsInfo();

        return [
            'chat' => [
                'unread_messages' => $countMessages,
            ],
            'notifications' => $countNotifications
        ];
    }

    /**
     * @throws Exception
     */
    public function getNotifications(array $filters)
    {
        return Auth::user()->getNotificationsList($filters);
    }

    public function sendPaymentNotification(int $profileId)
    {
        $profileService = $this->getService(ProfileServiceInterface::class);
        $profile = $profileService->findProfileById($profileId);

        $payloadData = $this->prepareDataToESB(false, true, null, null, $profile);
        $finances = Cache::remember('profile_' . $profile->id . '_finances', 1800, function () use ($payloadData) {
            return $this->handleRequestToESB('getFinance', $payloadData);
        });

        if (is_array($finances) && (array_key_exists('errors', $finances) || empty($finances['finance']))) {
            Log::channel('notifications')
                ->error('Payment Notification Error', ['profile_id' => $profile->id, $finances]);
            return;
        }

        $data = $this->processPaymentData($finances['finance']);

        if ($data['next_payment']['date'] != null) {
            $this->checkNotificationDate('payment_reminder', $data['next_payment']['date'], $profile);
        }
    }

    public function sendSessionNotification(int $profileId)
    {
        $profileService = $this->getService(ProfileServiceInterface::class);
        $profile = $profileService->findProfileById($profileId);
        $identKeys = ['gid', 'semester'];
        $payloadData = $this->prepareDataToESB(false, true, $identKeys, null, $profile);

        $dates = $this->handleRequestToESB('getSession', $payloadData);

        if (array_key_exists('errors', $dates)) {
            Log::channel('notifications')
                ->error('Session Notification Error', ['profile_id' => $profile->id, $dates]);
            return;
        }

        $data = $this->formatSessionDates($dates);

        if ($data['exam'] != null) {
            $this->checkNotificationDate('session_reminder', $data['exam']['start'], $profile);
        }
    }

    public function subscribe(array $attributes)
    {
        $profile = Auth::user();

        // Подписка для WebPush (веб-браузеры)
        if (isset($attributes['endpoint']) && isset($attributes['keys'])) {
            $profile->updatePushSubscription(
                $attributes['endpoint'],
                $attributes['keys']['p256dh'],
                $attributes['keys']['auth'],
            );
            Log::channel('notifications')->info('WebPush subscription', ['profile_id' => $profile->id]);
        }

        // Подписка для OneSignal (мобильные приложения)
        if (isset($attributes['player_id'])) {
            $playerId = trim($attributes['player_id']);

            // Дополнительная проверка формата UUID
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $playerId)) {
                Log::channel('notifications')->warning('Invalid OneSignal player_id format', [
                    'profile_id' => $profile->id,
                    'player_id' => $playerId,
                    'device_type' => $attributes['device_type'] ?? null
                ]);
                throw new \InvalidArgumentException('player_id должен быть в формате UUID');
            }

            $profile->updateOneSignalSubscription(
                $playerId,
                $attributes['device_type'] ?? null
            );
            Log::channel('notifications')->info('OneSignal subscription', [
                'profile_id' => $profile->id,
                'player_id' => $playerId,
                'device_type' => $attributes['device_type'] ?? null
            ]);
        }
    }

    public function unsubscribe(array $attributes)
    {
        $profile = Auth::user();

        // Отписка от WebPush
        if (isset($attributes['endpoint'])) {
            $profile->deletePushSubscription($attributes['endpoint']);
            Log::channel('notifications')->info('WebPush unsubscription', ['profile_id' => $profile->id]);
        }

        // Отписка от OneSignal
        if (isset($attributes['player_id'])) {
            $profile->deleteOneSignalSubscription($attributes['player_id']);
            Log::channel('notifications')->info('OneSignal unsubscription', ['profile_id' => $profile->id]);
        }
    }
}
