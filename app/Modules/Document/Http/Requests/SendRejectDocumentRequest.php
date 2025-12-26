<?php

namespace App\Modules\Document\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class SendRejectDocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'comment' => 'nullable|string',
        ];
    }
}
