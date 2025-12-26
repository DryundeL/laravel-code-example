<?php

namespace App\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseResource
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
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'photo_url' => $this->photo_url,
            'lang' => $this->lang,
        ]);
    }
}
