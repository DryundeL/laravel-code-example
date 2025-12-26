<?php

namespace App\Modules\Chat\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'message' => 'nullable|string',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,pdf,csv,xlsx,xls,docx,doc,pptx,ppt|max:10240',
        ];
    }

}
