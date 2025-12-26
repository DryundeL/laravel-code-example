<?php

namespace App\Modules\Document\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class SendSignDocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx |max:3072',
        ];
    }
}
