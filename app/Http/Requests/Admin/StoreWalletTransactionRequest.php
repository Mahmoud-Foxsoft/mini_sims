<?php

namespace App\Http\Requests\Admin;

use App\Models\WalletTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWalletTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['required', Rule::in([
                WalletTransaction::TYPE_CREDIT,
                WalletTransaction::TYPE_DEBIT,
            ])],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string', 'max:255'],
            'reference_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
