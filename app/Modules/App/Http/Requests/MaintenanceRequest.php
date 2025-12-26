<?php

namespace App\Modules\App\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;

class MaintenanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'is_maintenance' => 'required|boolean',
            'start_date' => 'nullable|date|after:today',
            'end_date' => 'nullable|date|after:start_date',
        ];
    }

}
