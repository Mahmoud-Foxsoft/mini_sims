<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code') && is_string($this->input('code'))) {
            $this->merge(['code' => strtoupper(trim($this->input('code')))]);
        }
    }

    public function rules(): array
    {
        $couponId = (int) $this->route('coupon');

        return [
            'code' => ['sometimes', 'string', 'max:255', Rule::unique('promo_codes', 'code')->ignore($couponId)],
            'description' => ['sometimes', 'nullable', 'string'],
            'amount_cents' => ['sometimes', 'integer', 'min:1'],
            'max_uses' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'times_used' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'expires_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
