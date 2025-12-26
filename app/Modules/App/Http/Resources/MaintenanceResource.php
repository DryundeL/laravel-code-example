<?php

namespace App\Modules\App\Http\Resources;

use App\Resources\BaseResource;
use Illuminate\Http\Request;

class MaintenanceResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'app_setting';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'is_maintenance' => $this->is_maintenance,
            'start_date' => $this->start_date?->format('d.m.Y H:i:s'),
            'end_date' => $this->end_date?->format('d.m.Y H:i:s'),
        ]);
    }
}
