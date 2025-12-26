<?php

namespace App\Modules\User\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class DeleteModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'accesses' => 'nullable|array',
            'accesses.*' => 'integer|exists:accesses,id',
        ];
    }

}
