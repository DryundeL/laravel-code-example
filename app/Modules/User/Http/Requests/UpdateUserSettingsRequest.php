<?php

namespace App\Modules\User\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class UpdateUserSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'lang' => 'required|string|max:255',
        ];
    }

}
