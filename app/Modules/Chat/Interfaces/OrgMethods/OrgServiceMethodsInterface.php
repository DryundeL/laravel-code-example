<?php

namespace App\Modules\Chat\Interfaces\OrgMethods;

interface OrgServiceMethodsInterface
{
    public function getChats(array $attributes): array;

    public function getMessages(int $recipientId, array $attributes): array;

    public function sendMessages(int $recipientId, array $attributes): array;

    public function markMessagesAsRead(array $attributes): bool|array;
}
