<?php

namespace App\Http\Requests;

use App\Models\DocumentChecklist;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDibpReceiptChecklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'clientid' => ['required', 'integer'],
            'checklist' => [
                'required',
                'string',
                'max:255',
                Rule::exists('portal_document_checklists', 'name')->where(function (Builder $query) {
                    $query->where('doc_type', DocumentChecklist::DOC_TYPE_DIBP_RECEIPT)
                        ->where('status', 1);
                }),
            ],
            'client_matter_id' => ['nullable', 'integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'checklist.exists' => 'Select a DIBP Receipt checklist.',
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
