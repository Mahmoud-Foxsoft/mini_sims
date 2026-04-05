<?php

namespace App\Http\Requests\Admin;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare incoming data before validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('currency') || $this->input('currency') === null) {
            $this->merge(['currency' => 'USD']);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['required', Rule::in([Transaction::TYPE_CREDIT, Transaction::TYPE_DEBIT])],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'max:7'],
            'provider_event_id' => ['nullable', 'string', 'max:255'],
            'source' => ['required', Rule::in([
                Transaction::SOURCE_STRIPE,
                Transaction::SOURCE_CRYPTO,
                Transaction::SOURCE_PAYPAL,
                Transaction::SOURCE_PROMO,
                Transaction::SOURCE_MANUAL,
            ])],
            'reference' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
