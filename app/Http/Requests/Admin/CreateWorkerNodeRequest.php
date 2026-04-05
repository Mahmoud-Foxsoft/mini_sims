<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateWorkerNodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('node_id') && is_string($this->input('node_id'))) {
            $this->merge(['node_id' => trim($this->input('node_id'))]);
        }

        if ($this->has('ip_address') && is_string($this->input('ip_address'))) {
            $this->merge(['ip_address' => trim($this->input('ip_address'))]);
        }
    }

    public function rules(): array
    {
        return [
            'node_id' => ['required', 'string', 'max:255', Rule::unique('worker_nodes', 'node_id')],
            'status' => ['required', 'string', Rule::in(['active', 'inactive', 'dead'])],
            'ip_address' => ['nullable', 'string', 'max:255'],
            'max_concurrent_syncs' => ['required', 'integer', 'min:1'],
            'current_syncs' => ['sometimes', 'integer', 'min:0'],
            'api_key' => ['required', 'string', 'max:255', Rule::unique('worker_nodes', 'api_key')],
            'last_heartbeat' => ['nullable', 'date'],
        ];
    }
}
