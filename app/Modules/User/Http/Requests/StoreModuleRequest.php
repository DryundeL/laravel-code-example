<?php

namespace App\Modules\User\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;
use Illuminate\Validation\Rule;

class StoreModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'desc' => 'required|string|max:255',
            'icon' => 'required|boolean',
            'show' => 'nullable|array',
            'show.*' => [
                'nullable', 'string',
                Rule::in(['mobile', 'desktop']),
            ],
            'inuse' => 'required|boolean',
            'order' => 'nullable|integer|gt:0',
            'accesses' => 'nullable|array',
            'accesses.*' => 'integer|exists:accesses,id',
        ];
    }

}
