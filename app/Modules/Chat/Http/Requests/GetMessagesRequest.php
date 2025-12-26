<?php

namespace App\Modules\Chat\Http\Requests;

use App\Requests\BaseSortRequest as SearchRequest;

class GetMessagesRequest extends SearchRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'limit' => 'nullable|integer|min:0',
            'offset' => 'nullable|integer|min:0',
        ]);
    }
}
