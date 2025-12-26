<?php

namespace App\Modules\Notification\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Notification\Http\Requests\DeletePushSubscriptionRequest;
use App\Modules\Notification\Http\Requests\StorePushSubscriptionRequest;
use App\Modules\Notification\Http\Resources\NotificationResource;
use App\Modules\Notification\Notifications\ProfilePushNotification;
use App\Modules\Notification\Services\NotificationService;
use App\Requests\LimitRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws \Exception
     */
    public function count(): JsonResponse
    {
        $notifications = $this->service->getNotificationsCount();

        if (array_key_exists('errors', $notifications)) {
            return $this->sendErrorResponse($notifications);
        }

        return $this->sendResponse($notifications);
    }

    /**
     * @throws \Exception
     */
    public function index(LimitRequest $request)
    {
        $notifications = $this->service->getNotifications($request->validated());

        return (NotificationResource::collection($notifications['notifications']))->additional($notifications['meta']);
    }

    /**
     * @param StorePushSubscriptionRequest $request
     * @return JsonResponse
     */
    public function subscribe(StorePushSubscriptionRequest $request)
    {
        $this->service->subscribe($request->validated());
        return response()->json(['success' => true]);
    }

    /**
     * @param DeletePushSubscriptionRequest $request
     * @return JsonResponse
     */
    public function unsubscribe(DeletePushSubscriptionRequest $request)
    {
        $this->service->unsubscribe($request->validated());
        return response()->json(['success' => true]);
    }

    public function sendTestNotification()
    {
        $profile = Auth::user();
        $profile->notify(new ProfilePushNotification([
            'title' => 'Тестовое сообщение',
            'photo_url' => 'https://storage.yandexcloud.net/instudy-dev/icons/notifications/account_blocked.png',
            'text' => 'Привет, это тестовое сообщение для проверки браузерных уведомлений',
        ], 'notification'));
    }
}
