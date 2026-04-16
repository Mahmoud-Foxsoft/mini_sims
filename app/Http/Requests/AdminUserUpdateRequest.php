<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // 'name' => 'sometimes|string|max:255',
            // 'password' => 'sometimes|string|min:8|confirmed',
            'balance_cents' => 'sometimes|numeric|min:0',
            'is_blocked' => 'sometimes|boolean',
            // 'email_verified_at' => 'sometimes|date|nullable',
            'max_pending_numbers' => 'sometimes|numeric|min:0',
        ];
    }
}
