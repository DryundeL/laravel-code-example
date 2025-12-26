<?php

namespace App\Modules\Chat\Http\Resources;

use App\Resources\BaseResource;
use Illuminate\Http\Request;

class MessageResource extends BaseResource
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
            'recipient_id' => $this->recipient_id,
            'read_at' => $this->read_at?->format('d.m.Y H:i:s'),
        ]);
    }
}
