<?php

namespace App\Requests;

use App\Requests\BaseSortRequest as FormRequest;

/**
 * Query parameters
 */
class LimitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
        ]);
    }

    public function queryParameters(): array
    {
        return array_merge(parent::queryParameters(), [
            'limit' => [
                'example' => '2',
            ],
            'offset' => [
                'example' => '0',
            ],
        ]);
    }

}
