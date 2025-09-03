<?php

namespace App\Services\FileUpload;

use App\Models\UploadFileToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class FileUploadService
{

    public function createUploadFileToken(User $user, array $tokenParams): UploadFileToken
    {
        $expiresAt = Carbon::now()->addMinutes(intval($tokenParams['expires_in_minutes']));

        return UploadFileToken::create([
            'user_id' => $user->id,
            'status' => UploadFileToken::STATUS_PENDING,
            'token' => Str::random(40),
            'max_size' => $tokenParams['max_size_in_MB'],
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function uploadFile(User $user, string $token, $file=null, string $base64=null, string $sha256 = null): UploadFileToken
    {
        // 1- get token
        $uploadToken = UploadFileToken::where('token', $token)
            ->where('user_id', $user->id)
            ->first();

        // 2- check if token is found or not
        if (!$uploadToken) {
            throw new \Exception('Token not found');
        }

        // 3- check token status
        if ($uploadToken->status === UploadFileToken::STATUS_UPLOADED) {
            throw new \Exception('Already uploaded');
        }

        // 4- check token expiration
        if ($uploadToken->expires_at <= Carbon::now()) {
            $uploadToken->update(['status' => 'expired']);
            throw new \Exception('Token expired');
        }

        // File upload
        if ($file) {
            $uploadedFile = $this->processAndUploadStaticFile($uploadToken, $file);
        }
        // JSON base64 + sha256
        elseif($base64 && $sha256) {
             $uploadedFile = $this->processAndUploadBase64File($uploadToken,$base64,$sha256);
        }else{
            throw new \Exception('No file or base64 provided');
        }

        $uploadToken->update([
            'status' => UploadFileToken::STATUS_UPLOADED,
            'path' => $uploadedFile['path'],
            'checksum_sha256' => $uploadedFile['checksum'],
            'uploaded_at' => Carbon::now(),
        ]);

        return $uploadToken;
    }

    /**
     * @throws \Exception
     */
    private function processAndUploadStaticFile(UploadFileToken $fileUploadToken, $file): array
    {
        // 1- check file size
        $fileSizeInMB = round($file->getSize()/(1024*1024),2);
        if($fileSizeInMB > $fileUploadToken->max_size){
            throw new \Exception("File exceeds maximum allowed size of {$fileUploadToken->max_size} MB");
        }

        // 2- store file
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName()); // safe file name
        $path = $file->storeAs("uploads/{$fileUploadToken->user_id}", "{$fileUploadToken->token}-{$filename}");
        $checksum = hash_file('sha256', Storage::path($path));

        return ['path' => $path, "checksum" => $checksum];

    }

    /**
     * @throws \Exception
     */
    private function processAndUploadBase64File(UploadFileToken $fileUploadToken, string $base64, string $sha256): array
    {
        // 1- Remove data url scheme if present
        if(str_contains($base64, 'base64,')){
            $base64 = explode('base64,', $base64)[1];
        }

        // 2- convert base64 to binary data
        $binary = base64_decode(trim($base64), true);
        if($binary === false){
            throw new \Exception("Invalid base64 data");
        }

        // 3- check file size from binary data byte by byte
        $fileSizeInBytes = strlen($binary);
        $fileSizeInMB = round($fileSizeInBytes/(1024*1024),2);
        if($fileSizeInMB > $fileUploadToken->max_size){
            throw new \Exception("File exceeds maximum allowed size of {$fileUploadToken->max_size} MB");
        }

        // 4- verify sha256 checksum
        $computedValueSha256 = hash('sha256', $binary);
        if($computedValueSha256 !== $sha256){
            throw new \Exception("Checksum mismatch");
        }

        // 5- store file
        $fileName = Str::random(20);
        $path = "uploads/{$fileUploadToken->user_id}/{$fileUploadToken->token}-{$fileName}";
        Storage::put($path, $binary);

        return ['path' => $path, "checksum" => $computedValueSha256];
    }

    public function listUserUploads(User $user): Collection
    {
        return UploadFileToken::select('id')
            ->where('user_id', $user->id)
            ->where('status', UploadFileToken::STATUS_UPLOADED)
            ->orderBy('created_at', 'desc')
            ->get()
            // map to add temp link valid for 5 minutes
            ->map(function ($upload) {
                $upload->temp_url = URL::temporarySignedRoute(
                    'uploads.download',
                    now()->addMinutes(5), // expire after 5 minutes
                    ['uploadToken' => $upload->id]
                );
                return $upload;
            });
    }

}
