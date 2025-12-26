<?php

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Http\Resources\MessageUserResource;
use App\Modules\Chat\Models\Message;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Traits\MessageFormat;
use App\Modules\Chat\Traits\ProcessAvatar;
use App\Modules\Chat\Events\MessageSent;
use App\Modules\User\Models\Profile;

class ChatService extends BaseService
{
    use MessageFormat, ProcessAvatar;

    public function getChats(array $attributes): array
    {
        $attributes['contact_group'] = $attributes['group'] ?? null;

        if (array_key_exists('query', $attributes)) {
            $attributes['search'] = $attributes['query'];
            unset($attributes['query']);
        }

        $identKeys = ['gid'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys, $attributes);
        $chats = $this->handleRequestToESB('getContacts', $payloadData, 'contacts');

        if (isset($chats['errors'])) {
            return $this->getErrorMessage('chats', $chats['errors']['error'][0]);
        }

        array_walk($chats, function (&$chat) {
            $chat['role'] = $chat['title'];
            $chat['have_unread_messages'] = $chat['unread'];

            $this->processAvatar($chat);

            unset($chat['title'], $chat['unread']);
        });

        if (!isset($attributes['group'])) {
            if (!empty($chats)) {
                $curator = array_shift($chats);
                if ($curator['role'] !== 'Куратор') {
                    array_unshift($chats, $curator);
                    $curator = null;
                }
            } else {
                $curator = null;
            }

            return [
                'curator' => $curator,
                'chats' => $chats,
            ];
        }

        return [
            'chats' => $chats,
        ];
    }

    /**
     * @throws Exception
     */
    public function sendMessages(array $attributes, int $recipientId): Message|array
    {
        if (array_key_exists('attachments', $attributes)) {
            $attachments = $attributes['attachments'];
            $attachLink = 'https://dist.inpsycho.ru/uploads/message/';
            $files = [];

            foreach ($attachments as $attachment) {
                if (!file_exists($attachment)) {
                    return $this->getErrorMessage('message', 'Файл не найден.');
                }

                $zipName = md5(uniqid('', true));
                $zipExt = '.zip';

                if (!Storage::disk('message')->exists('/')) {
                    try {
                        Storage::disk('message')->makeDirectory('/');
                    } catch (Exception $e) {
                        return $this->getErrorMessage('storage', 'Не удалось создать директорию для архива.');
                    }
                }

                $zipPath = Storage::disk('message')->path($zipName . $zipExt);
                $zip = new ZipArchive();
                if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                    $zip->addFile($attachment, $attachment->getClientOriginalName());
                    $zip->close();
                } else {
                    return $this->getErrorMessage('message', 'Не удалось создать ZIP-архив.');
                }

                $href = $attachLink . $zipName . $zipExt;
                $files[] = "<a href=\"$href\"><i style='font-size: 13px;' class='fa fa-file-archive-o' aria-hidden='true'></i>{$attachment->getClientOriginalName()}</a>";
            }

            unset($attributes['attachments']);

            return $this->prepareMessageToESB($recipientId, $attributes, $files);
        }

        return $this->prepareMessageToESB($recipientId, $attributes);
    }

    /**
     * @param int $recipientId
     * @param array $attributes
     * @param array $files
     * @return array
     */
    private function prepareMessageToESB(int $recipientId, array $attributes, array $files = []): array
    {
        $attributes['partner_id'] = $recipientId;
        $receiver = $this->profileService->findProfileByExternalId($recipientId);

        $messages = [];

        if (!empty($files)) {
            foreach ($files as $file) {
                $fileAttributes = $attributes;
                $fileAttributes['message'] = $file;

                $message = $this->sendMessageToESB($fileAttributes, $receiver);

                if (isset($message['errors'])) {
                    return $message;
                }

                $messages[] = $message;
            }
        }

        if (array_key_exists('message', $attributes)) {
            $message = $this->sendMessageToESB($attributes, $receiver);

            if (isset($message['errors'])) {
                return $message;
            }

            $messages[] = $message;
        }

        return $messages;
    }

    /**
     * @param array $attributes
     * @param Profile|null $receiver
     * @return mixed
     */
    private function sendMessageToESB(array $attributes, ?Profile $receiver): mixed
    {
        $payloadData = $this->prepareDataToESB(true, attributes: $attributes);
        $fileMessage = $this->handleRequestToESB('sendMessage', $payloadData);

        if (isset($fileMessage['errors'])) {
            return $fileMessage;
        }

        $formatedMessage = $this->formatMessage($fileMessage, $receiver?->id);

        if ($receiver) {
            // Сохраняем from_external_id перед заменой from на объект пользователя
            $fromExternalId = $formatedMessage['from_external_id'] ?? $formatedMessage['from'];

            $profile = $this->profileService->findProfileByExternalId($formatedMessage['from']);
            $formatedMessage['from'] = (new MessageUserResource($profile->user))->toArray(request());

            // Убеждаемся, что fromExternalId присутствует в данных
            $formatedMessage['from_external_id'] = $fromExternalId;

            event(new MessageSent($formatedMessage, $receiver));
        }

        unset($formatedMessage['from']);
        return $formatedMessage;
    }

    /**
     * @throws Exception
     */
    public function getMessages(int $recipientId, array $attributes): Collection|array
    {
        $attributes['partner_id'] = $recipientId;
        $attributes['limit'] = 100000;
        $attributes['offset'] = 0;
        $identKeys = ['gid'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys, $attributes);
        $data = $this->handleRequestToESB('getChat', $payloadData);

        if (isset($data['errors'])) {
            return $this->getErrorMessage('chat', $data['errors']['error'][0]);
        }

        $messages = $data['messages'];
        $partner = $data['partner'];

        array_walk($messages, function (&$message) use ($recipientId) {
            $message = $this->formatMessage($message, $recipientId);
        });

        $partner['full_name'] = $partner['name'];
        $partner['role'] = $partner['title'];
        $this->processAvatar($partner);

        unset($partner['name'], $partner['title']);

        return [
            'partner' => $partner,
            'messages' => array_reverse($messages),
        ];
    }

    /**
     * @throws Exception
     */
    public function markAsRead(array $attributes): bool|array
    {
        $payloadData = $this->prepareDataToESB(true, attributes: $attributes);
        $messages = $this->handleRequestToESB('setMessagesRead', $payloadData);

        if (isset($message['errors'])) {
            return $this->getErrorMessage('chats', $messages['errors']['error'][0]);
        }

        if ($messages) {
            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function getMessageFromEsb(array $attributes): array
    {
        $message = $attributes['message'];
        $profile = $this->profileService->findProfileByExternalId($message['to']);

        if ($profile) {
            $formatedMessage = $this->formatMessage($message, $profile->id);

            // Сохраняем from_external_id перед заменой from на объект пользователя
            $fromExternalId = $formatedMessage['from_external_id'] ?? $formatedMessage['from'];

            $senderProfile = $this->profileService->findProfileByExternalId($formatedMessage['from']);
            if ($senderProfile) {
                $formatedMessage['from'] = (new MessageUserResource($senderProfile->user))->toArray(request());
            }

            // Убеждаемся, что fromExternalId присутствует в данных
            $formatedMessage['from_external_id'] = $fromExternalId;

            event(new MessageSent($formatedMessage, $profile));
        }

        return [];
    }
}
