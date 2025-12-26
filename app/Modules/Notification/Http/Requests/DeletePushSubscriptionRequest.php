<?php

namespace App\Modules\Notification\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class DeletePushSubscriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // WebPush отписка
            'endpoint' => 'required_without:player_id|url',
            // OneSignal отписка
            'player_id' => 'required_without:endpoint|string',
        ];
    }
}
