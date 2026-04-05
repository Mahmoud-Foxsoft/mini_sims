<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstimatePriceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => ['required', 'numeric', 'min:10'],
            'currency' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Please enter an amount.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Minimum amount is $10.',
            'currency.required' => 'Please select a currency.',
            'currency.string' => 'Currency must be a valid string.',
        ];
    }
}
