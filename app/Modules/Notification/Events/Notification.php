<?php

namespace App\Modules\Notification\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Log;

class Notification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public string $type;
    public array $data;
    public int $profileId;

    public function __construct(int $profileId, string $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
        $this->profileId = $profileId;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->profileId);
    }

    /**
     * Получаем название события для клиентской стороны.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'notification';
    }

    public function broadcastWith(): array
    {
        Log::channel('notifications')
            ->info('Send notification for', ['profile_id' => $this->profileId, 'type' => $this->type, 'data' => $this->data]);
        return [
            $this->type => $this->data,
        ];
    }
}
