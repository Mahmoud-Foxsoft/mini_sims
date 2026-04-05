<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkerNodeRequest extends FormRequest
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
        $workerNodeId = (int) $this->route('worker_node');

        return [
            'node_id' => ['sometimes', 'string', 'max:255', Rule::unique('worker_nodes', 'node_id')->ignore($workerNodeId)],
            'status' => ['sometimes', 'string', Rule::in(['active', 'inactive', 'dead'])],
            'ip_address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'max_concurrent_syncs' => ['sometimes', 'integer', 'min:1'],
            'current_syncs' => ['sometimes', 'integer', 'min:0'],
            'api_key' => ['sometimes', 'string', 'max:255', Rule::unique('worker_nodes', 'api_key')->ignore($workerNodeId)],
            'last_heartbeat' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
