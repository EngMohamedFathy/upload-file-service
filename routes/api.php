<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FileUpload\FileUploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function (){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:api')->group(function (){
    Route::post('/uploads', [FileUploadController::class, 'createFileUploadToken'])->middleware('throttle:5,1');
    Route::post('/uploads/{token}', [FileUploadController::class, 'uploadFile']);
    Route::get('/uploads', [FileUploadController::class, 'listUserUploadedFiles']);
});

Route::get('/downloads/{uploadToken}', [FileUploadController::class, 'download'])->middleware('signed')->name('uploads.download');
