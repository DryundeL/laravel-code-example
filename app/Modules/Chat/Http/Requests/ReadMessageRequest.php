<?php

namespace App\Modules\Chat\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class ReadMessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'messages_id' => 'required|array',
            'messages_id.*' => 'integer|gt:0',
        ];
    }

}
