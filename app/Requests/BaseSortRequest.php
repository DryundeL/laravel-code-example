<?php

namespace App\Requests;

use Illuminate\Validation\Rule;

class BaseSortRequest extends BaseFormRequest
{
    /**
     * The sortable fields.
     *
     * @var array
     */
    protected array $sortableFields = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'sort_by' => [
                'nullable', 'required_with:sort_dir',
                Rule::in($this->sortableFields),
            ],
            'sort_dir' => 'nullable|in:asc,desc',
            'date_range' => 'nullable|in:month,week,day',
        ];
    }
}
