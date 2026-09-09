<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDibpReceiptHubdocRequest extends FormRequest
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
            'fileid' => ['required', 'integer'],
        ];
    }
}
