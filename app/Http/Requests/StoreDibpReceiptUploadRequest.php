<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDibpReceiptUploadRequest extends FormRequest
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
            'document_upload' => ['required', 'file', 'max:20480'],
        ];
    }
}
