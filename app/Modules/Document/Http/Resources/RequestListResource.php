<?php

namespace App\Modules\Document\Http\Resources;

use App\Resources\BaseResource;
use Illuminate\Http\Request;

class RequestListResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'name' => $this->name,
            'type' => $this->type,
        ]);
    }
}
