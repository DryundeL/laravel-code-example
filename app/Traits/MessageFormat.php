<?php

namespace App\Traits;

use App\Modules\User\Interfaces\DataTransfer\ProfileServiceInterface;
use App\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait MessageFormat
{
    public ?ProfileServiceInterface $profileService = null;
    protected function getProfileService(): ProfileServiceInterface
    {
        if ($this->profileService === null) {
            $this->profileService = app(ProfileServiceInterface::class);
        }
        return $this->profileService;
    }

    public function formatMessage(array $message, ?int $recipientId): array
    {
        if (array_key_exists('datetime', $message)) {
            $message['date'] = Carbon::parse($message['datetime'])->format('d.m.Y H:i:s');
        } else {
            $message['date'] = Carbon::now()->format('d.m.Y H:i:s');
        }
        unset($message['datetime']);

        $message['text'] = strip_tags($message['text'], '<a>');
        $pattern = '/<a\s+href=["\'](.*?)["\']>(.*?)<\/a>/i';

        if (preg_match($pattern, $message['text'], $matches)) {
            $message['link'] = $matches[1];
            $message['fileName'] = $matches[2];
        }

        if ($recipientId === null || $recipientId != $message['from']) {
            $message['sendByCurrentUser'] = true;
        } else {
            $message['sendByCurrentUser'] = false;
        }

        if (array_key_exists('inc', $message)) {
            $message['id'] = $message['inc'];
            unset($message['inc']);
        }

        unset($message['to']);
        $message['from_external_id'] = $message['from'];

        $message['type'] = array_key_exists('link', $message) && !$message['auto']
            ? 'file' : 'text';

        return $message;
    }
}
