<?php

namespace App\Modules\Chat\Events;

use App\Modules\User\Models\Profile;
use App\Traits\KeysCaseConverter;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, KeysCaseConverter;

    public array $message;
    public Profile $profile;

    public function __construct(array $message, Profile $profile)
    {
        $this->message = $this->convertKeysToCamelCase($message);
        $this->profile = $profile;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.' . $this->profile->id . '.' . $this->message['fromExternalId']);
    }

    /**
     * Получаем название события для клиентской стороны.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message';
    }

    public function broadcastWith(): array
    {
        $message = $this->message;
        $message['answered'] = 0;
        $message['read'] = 0;
        $message['auto'] = 0;
        unset($message['partner']);

        return $message;
    }
}
