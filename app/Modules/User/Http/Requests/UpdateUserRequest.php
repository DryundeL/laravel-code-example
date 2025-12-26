<?php

namespace App\Modules\User\Http\Requests;

use App\Requests\BaseFormRequest as FormRequest;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $userId = $this->getUserId();
        $uniqueRule = $userId ? "unique:users,email,{$userId}" : 'unique:users,email';

        return [
            'email' => 'required|string|max:255|' . $uniqueRule,
            'old_email' => 'required|string|max:255|exists:users,email',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'photo_url' => 'nullable|string',
        ];
    }

    private function getUserId()
    {
        $oldEmail = $this->input('old_email');

        if ($oldEmail) {
            $user = User::where('email', $oldEmail)->first();
            return $user ? $user->id : null;
        }

        return null;
    }

}
