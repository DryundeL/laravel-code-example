<?php

namespace App\Modules\Chat\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class StoreMessageFromESBRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'message' => 'required|array',
            'message.from' => 'required|array',
            'message.from.id' => 'required|integer|gt:0',
            'message.from.full_name' => 'required|string',
            'message.from.photo_url' => 'nullable|string',
            'message.to' => 'required|integer|gt:0',
            'message.text' => 'required|string',
            'message.auto' => 'required|integer',
        ];
    }
}
