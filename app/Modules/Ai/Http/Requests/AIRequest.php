<?php

namespace App\Modules\Ai\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class AIRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'is_mobile' => 'nullable|boolean',
            'score' => 'nullable|integer|min:1|max:100',
            'query' => 'required|string',
        ];
    }

}
