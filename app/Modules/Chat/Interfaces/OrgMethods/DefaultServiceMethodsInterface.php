<?php

namespace App\Modules\Chat\Interfaces\OrgMethods;

use App\Modules\Chat\Models\Message;
use App\Modules\User\Models\Profile;
use Illuminate\Support\Collection;

interface DefaultServiceMethodsInterface
{
    public function getChats(Profile $profile): Collection;
    public function getMessages(Profile $profile, int $recipientId): Collection;
    public function sendMessage(Profile $profile, array $attributes, int $recipientId): Message;
    public function markMessagesAsRead(Profile $profile, array $attributes): bool;

    public function getMessageFromEsb(array $attributes): array;
}
