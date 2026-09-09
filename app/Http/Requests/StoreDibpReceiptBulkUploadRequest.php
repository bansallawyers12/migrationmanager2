<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDibpReceiptBulkUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'clientid' => ['required', 'integer'],
            'client_matter_id' => ['nullable', 'integer'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'max:20480'],
            'mappings' => ['required', 'array'],
            'mappings.*' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $matterId = $this->input('client_matter_id');
        if ($matterId === '' || $matterId === '0') {
            $this->merge(['client_matter_id' => null]);
        }
    }
}
