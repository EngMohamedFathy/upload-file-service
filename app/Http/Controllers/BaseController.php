<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    public function responseSuccess(string $message = 'success_response', array|object $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => __($message),
            'data' => $data
        ], $status);
    }

    public function responseError(string $message = 'success_response', array|object $data = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => __($message),
            'data' => $data
        ], $status);
    }

}
