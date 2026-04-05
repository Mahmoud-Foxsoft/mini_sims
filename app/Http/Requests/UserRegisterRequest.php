<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');
        if (is_string($email)) {
            $email = trim($email);
            if (strpos($email, '@') !== false) {
                [$local, $domain] = explode('@', $email, 2);
                $local = str_replace('.', '', $local);
                $email = $local.'@'.$domain;
            } else {
                $email = str_replace('.', '', $email);
            }
            $email = strtolower($email);
            $this->merge(['clean_email' => $email]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'clean_email' => 'required|email|unique:users,clean_email',
            'email' => 'required',
            'recaptcha_token' => 'required',
            'terms' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email is already taken',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'clean_email.unique' => 'Email is already taken',
            'clean_email.required' => 'Email is required',
            'clean_email.email' => 'Email must be a valid email address',
        ];
    }
}
