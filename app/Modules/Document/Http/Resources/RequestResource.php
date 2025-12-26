<?php

namespace App\Modules\Document\Http\Resources;

use App\Resources\BaseResource;
use Illuminate\Http\Request;

class RequestResource extends BaseResource
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
        $data = array_merge(parent::toArray($request), [
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'alert' => $this->alert,
            'period' => $this->period,
        ]);

        if ($this->activeDiscounts !== null) {
            $data['activeDiscounts'] = $this->activeDiscounts;
        }

        if ($this->template_url !== null) {
            $data['template_url'] = $this->template_url;
        }

        return $data;
    }
}
