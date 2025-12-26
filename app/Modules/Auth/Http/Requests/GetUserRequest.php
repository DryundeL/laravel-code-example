<?php

namespace App\Modules\Auth\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class GetUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string',
            'state' => 'required|string|max:255',
        ];
    }

}
