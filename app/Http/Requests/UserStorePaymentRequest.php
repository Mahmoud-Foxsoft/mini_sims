<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStorePaymentRequest extends FormRequest
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
            'amount' => 'required|numeric|min:10|max:10000', // Set reasonable limits for users
            'currency' => 'required|string|max:10', // Limit to supported currencies
            'paid_amount' => 'required|numeric'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'amount.min' => 'The minimum payment amount is $0.01.',
            'amount.max' => 'The maximum payment amount is $10,000.',
        ];
    }
}
