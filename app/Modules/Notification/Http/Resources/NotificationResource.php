<?php

namespace App\Modules\Notification\Http\Resources;

use App\Resources\BaseResource;
use Illuminate\Http\Request;

class NotificationResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'portfolio';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pivot->id,
            'title' => $this->title,
            'text' => $this->pivot->text,
            'is_read' => $this->pivot->is_read,
            'photo_url' => $this->getNotificationIcon(),
            'dark_photo_url' => $this->getNotificationIcon(true),
            'created_at' => $this->pivot->created_at->format('d.m.Y, H:i:s'),
            'updated_at' => $this->pivot->updated_at->format('d.m.Y, H:i:s'),
        ];
    }
}
