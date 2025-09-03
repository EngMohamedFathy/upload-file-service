<?php

namespace App\Http\Controllers\FileUpload;

use App\Http\Controllers\BaseController;
use App\Http\Requests\FileUpload\CreateFileUploadTokenRequest;
use App\Http\Requests\FileUpload\UploadFileRequest;
use App\Models\UploadFileToken;
use App\Services\FileUpload\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends BaseController
{
    public function __construct(readonly private FileUploadService $fileUploadService){}

    public function createFileUploadToken(CreateFileUploadTokenRequest $request): JsonResponse
    {
        $uploadToken = $this->fileUploadService->createUploadFileToken(Auth::user(),$request->validated());

        return $this->responseSuccess('messages.success_response',$uploadToken);
    }

    public function uploadFile(UploadFileRequest $request, $token): JsonResponse
    {
        $file = $request->hasFile('file') ?  $request->file('file') : null;

        try {
            $uploadedFile = $this->fileUploadService->uploadFile(Auth::user(),$token,$file, $request->validated('base64'),$request->validated('sha256'));
        }catch (\Exception $exception){
            return $this->responseError($exception->getMessage());
        }

        return $this->responseSuccess('file_upload.file_uploaded_successfully',$uploadedFile);
    }

    public function listUserUploadedFiles(Request $request): JsonResponse
    {
        $uploadedFiles = $this->fileUploadService->listUserUploads(Auth::user());

        return $this->responseSuccess('messages.success_response',$uploadedFiles);
    }

    // Handle temp download link
    public function download(Request $request, UploadFileToken $uploadToken)
    {
        // Optional: prevent direct guessing
        if (!$request->hasValidSignature()) {
            return $this->responseError("Invalid or expired link");
        }

        // Ensure only owner can access
        /*if ($uploadToken->user_id !== Auth::id()) {
            return $this->errorResponse("Unauthorized");
        }*/

        return Storage::download($uploadToken->path);
    }


}
