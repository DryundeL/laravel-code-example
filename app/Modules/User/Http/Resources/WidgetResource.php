<?php

namespace App\Modules\User\Http\Resources;

use App\Resources\BaseResource;
use Illuminate\Http\Request;

class WidgetResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'module';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
