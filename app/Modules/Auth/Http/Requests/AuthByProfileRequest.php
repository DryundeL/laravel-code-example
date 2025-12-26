<?php

namespace App\Modules\Auth\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class AuthByProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|max:255',
        ];
    }

}
