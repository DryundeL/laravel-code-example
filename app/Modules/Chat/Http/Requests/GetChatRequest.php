<?php

namespace App\Modules\Chat\Http\Requests;

use App\Requests\BaseSortRequest as SearchRequest;
use Illuminate\Validation\Rule;

class GetChatRequest extends SearchRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $groups = [
            'classmates',
            'teachers',
            'curators'
        ];
        return array_merge(parent::rules(), [
            'group' => [
                'nullable', 'string',
                Rule::in($groups)
            ],
            'query' => 'nullable|string',
        ]);
    }
}
