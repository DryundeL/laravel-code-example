<?php

namespace App\Modules\Notification\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class StorePushSubscriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // WebPush подписка (для веб-браузеров)
            'endpoint' => 'required_without:player_id|url',
            'keys.p256dh' => 'required_with:endpoint|string',
            'keys.auth' => 'required_with:endpoint|string',
            
            // OneSignal подписка (для мобильных приложений)
            // player_id должен быть в формате UUID (например: 123e4567-e89b-12d3-a456-426614174000)
            'player_id' => [
                'required_without:endpoint',
                'string',
                'regex:/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i'
            ],
            'device_type' => 'nullable|string|in:ios,android',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'player_id.regex' => 'player_id должен быть в формате UUID (например: 123e4567-e89b-12d3-a456-426614174000)',
        ];
    }
}
