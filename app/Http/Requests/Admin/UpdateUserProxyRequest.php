<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProxyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proxy_username' => ['sometimes', 'string'],
            'proxy_user_id' => ['sometimes', 'string'],
            'proxy_user_password' => ['sometimes', 'string'],
            'user_id' => ['sometimes', 'nullable', 'integer'],
            'data_limit' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
