<?php

namespace App\Modules\User\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class SwapModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'order' => 'required|integer|gt:0',
            'accesses' => 'nullable|array',
            'accesses.*' => 'required|integer|exists:accesses,id',
        ];
    }

}
