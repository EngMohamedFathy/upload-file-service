<?php

namespace App\Http\Requests\FileUpload;

use Illuminate\Foundation\Http\FormRequest;

class CreateFileUploadTokenRequest extends FormRequest
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
        $maxFileUploadSizeMB = config('filesystems.max_file_upload_size_MB');
        return [
            'expires_in_minutes' => 'required|integer|min:1',
            'max_size_in_MB' => "required|integer|min:1|max:$maxFileUploadSizeMB"
        ];
    }
}
