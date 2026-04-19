<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateSettingRequest extends FormRequest
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
        $setting = $this->route('setting');
        $valueRules = ['required'];

        if ($setting?->type === 'image') {
            $valueRules = ['required', 'file', 'image', 'max:5120'];
        } elseif ($setting?->type === 'faq_array' || $setting?->type === 'social_array') {
            $valueRules = ['required', 'string', 'json'];
        } elseif ($setting?->type === 'html') {
            $valueRules = ['required', 'string'];
        } else {
            $valueRules = ['required', 'string'];
        }

        return [
            'value' => $valueRules,
            'title' => 'sometimes|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'value.required' => 'The setting value is required.',
            'value.image' => 'The value must be a valid image file.',
            'value.max' => 'The image size must not exceed 5MB.',
            'value.json' => 'The setting value must be valid JSON.',
            'title.string' => 'The setting title must be a string.',
        ];
    }
}
