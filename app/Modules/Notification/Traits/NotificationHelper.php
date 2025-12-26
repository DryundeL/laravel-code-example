<?php

namespace App\Modules\Notification\Traits;

use App\Jobs\MailSendJob;
use App\Modules\Notification\Events\Notification;
use App\Modules\Notification\Mail\MessageNotificationMail;
use App\Modules\Notification\Mail\NotificationMail;
use App\Modules\Notification\Models\Notification as NotificationModel;
use App\Modules\User\Models\Profile;
use App\Traits\KeysCaseConverter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

trait NotificationHelper
{
    use KeysCaseConverter;

    /**
     * @param Profile $profile
     * @return void
     */
    private function updateNotificationCount(Profile $profile): void
    {
        $countNotifications = NotificationModel::getAllNotificationsInfo($profile);
        event(new Notification($profile->id, 'unreadNotificationCount', $this->convertKeysToCamelCase($countNotifications)));
    }

    /**
     * @param Profile $profile
     * @param string $notificationType
     * @param array $sendData
     * @param string|null $notificationTime
     * @return void
     */
    public function sendNotifications(Profile $profile, string $notificationType,
                                      array   $sendData = [], string $notificationTime = null): void
    {
        $notificationBase = NotificationModel::where('name', $notificationType)->first();
        $settings = $profile->settings->notification_settings;

        if (!$notificationBase) {
            Log::channel('notifications')->error('Notification Type Error:', [$notificationType]);
            return;
        }

        if ($notificationType != 'message') {

            if ($notificationTime) {
                $data = $notificationBase->data[$notificationTime];
            } else {
                $data = $notificationBase->data;
            }

            if ($notificationBase->need_more_data) {
                $data['text'] = $data['text'] . $sendData['text'];
            }

            $data['title'] = $notificationBase->title;
            $data['photoUrl'] = $notificationBase->getNotificationIcon();
            $data['darkPhotoUrl'] = $notificationBase->getNotificationIcon(true);

            $profile->notifications()->attach($notificationBase->id, [
                'text' => $data['text'],
            ]);
            $this->updateNotificationCount($profile);

            $data['mailTitle'] = 'У вас новое уведомление';
            $notificationType = 'notification';

            $mail = new NotificationMail($data);
        } else {
            $data = $sendData;
            $data['mailTitle'] = 'У вас новое личное сообщение';

            if (isset($data['link'])) {
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                $filePatch = pathinfo($data['fileName'], PATHINFO_EXTENSION);

                $data['text'] = match (in_array($filePatch, $imageExtensions)) {
                    true => 'Фотография',
                    false => 'Файл',
                };
            }

            $mail = new MessageNotificationMail($data);

            if (!$settings['show_message_text']) {
                $data['text'] = 'Отправил вам личное сообщение';
            }
        }

        $settingName = $notificationBase->setting_name;

        switch ($settings[$settingName] ?? 'all') {
            case 'all':
                event(new Notification($profile->id, $notificationType, $data));
                MailSendJob::dispatch($profile->user->email, $mail);
                break;
            case 'system':
                event(new Notification($profile->id, $notificationType, $data));
                break;
            default:
                break;
        }
    }

    /**
     * @param string $notificationType
     * @param string $notificationDate
     * @param Profile $profile
     * @return void
     */
    private function checkNotificationDate(string $notificationType, string $notificationDate, Profile $profile): void
    {
        $now = Carbon::now();
        $notificationDate = Carbon::createFromFormat('d.m.Y', $notificationDate);

        switch (true) {
            case $now->copy()->addDay()->isSameDay($notificationDate):
                $this->sendNotifications($profile, $notificationType, [], 'day');
                break;
            case $now->copy()->addWeek()->isSameDay($notificationDate):
                $this->sendNotifications($profile, $notificationType, [], 'week');
                break;
            case $now->copy()->addMonth()->isSameDay($notificationDate):
                $this->sendNotifications($profile, $notificationType, [], 'month');
                break;
            default:
                break;
        }
    }
}
