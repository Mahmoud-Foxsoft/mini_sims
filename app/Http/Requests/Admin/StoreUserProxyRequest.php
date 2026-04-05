<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserProxyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proxy_username' => ['required', 'string'],
            'proxy_user_id' => ['required', 'string'],
            'proxy_user_password' => ['required', 'string'],
            'user_id' => ['nullable', 'integer'],
            'data_limit' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
