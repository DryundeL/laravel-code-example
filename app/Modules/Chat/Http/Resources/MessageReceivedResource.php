<?php

namespace App\Modules\Chat\Http\Resources;

use App\Resources\BaseResource;
use Illuminate\Http\Request;

class MessageReceivedResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'message';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'message' => $this->message,
            'attachments' => $this->attachments,
            'sender' => $this->profile->user->first_name . ' ' . $this->profile->user->last_name,
            'read_at' => $this->read_at?->format('d.m.Y H:i:s'),
        ]);
    }
}
