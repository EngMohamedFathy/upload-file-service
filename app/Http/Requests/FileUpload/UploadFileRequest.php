<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxFileUploadSizeMB = config('filesystems.max_file_upload_size_MB')*1024; // in KB
        return [
            'file' => "required_without:base64|file|max:$maxFileUploadSizeMB",
            'base64' => 'required_without:file|string',
            'sha256' => 'required_with:base64|string|size:64' // sha-256 is 64 chars hex
        ];
    }
}
