<?php

namespace App\Modules\Document\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class CodeVerifyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'code' => 'nullable|integer|max_digits:6',
        ];
    }
}
