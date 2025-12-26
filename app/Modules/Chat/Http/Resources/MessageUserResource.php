<?php

namespace App\Modules\Chat\Http\Resources;

use App\Resources\BaseResource;
use Illuminate\Http\Request;

class MessageUserResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'user';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'full_name' => trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name),
            'photo_url' => $this->photo_url,
        ]);
    }
}
