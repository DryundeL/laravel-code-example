<?php

namespace App\Modules\Document\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SendDocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $profile = Auth::user();
        $requestType = $this->route()?->parameter('requestType')->type;
        $currentSemester = Cache::get('discount_' . $profile->external_id)['currentSemester'] ?? 0;

        return match ($requestType) {
            'transfer' => [
                'education_level' => 'required|string|regex:/^[0-9]+$/|max:20',
                'faculty' => 'required|string|regex:/^[0-9]+$/|max:20',
                'speciality' => 'required|string|regex:/^[0-9]+$/|max:20',
                'profile' => 'required|string|regex:/^[0-9]+$/|max:20',
                'education_form' => 'required|string|regex:/^[0-9]+$/|max:20',
                'education_format' => 'nullable|string',
            ],
            'discount_social' => [
                'discount_id' => 'required|string|regex:/^[0-9]+$/|max:20',
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png,giff,bmp,tiff|max:3072',
            ],
            'discount' => [
                'semester_begin' => 'required|integer|gte:' . $currentSemester,
                'semester_end' => 'required|integer|gte:semester_begin',
            ],
            'vypiska' => [
                'place_of_demand' => 'required|string|max:255',
            ],
            'personal' => [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'required|string|max:255',
                'doc_series' => 'required|string|regex:/^[0-9]+$/',
                'doc_number' => 'required|string|regex:/^[0-9]+$/',
                'doc_date' => 'required|date',
                'doc_ovd' => 'required|string',
                'doc_ovd_code' => 'required|string|regex:/^[0-9-]+$/',
                'reason' => 'required|string|regex:/^[0-9]+$/|max:20',
                'personal_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:3072',
                'change_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
            ],
            'iup' => [
                'semester' => 'required|integer',
                'accelerated' => 'nullable|boolean',
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:3072',
            ],
            'academ' => [
                'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
            ],
            'expel' => [
                'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
            ],
            default => [],
        };
    }
}

