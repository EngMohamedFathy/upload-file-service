<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{

    public function __construct(readonly private AuthService $authService){}

    public function login(UserLoginRequest $request): JsonResponse
    {
        try{
            $user = $this->authService->loginUser($request->validated('email'), $request->validated('password'));
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('auth.user_login_successfully',$user);

    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        try{
            $user = $this->authService->registerUser($request->validated());
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }

        return $this->responseSuccess('auth.user_created_successfully',$user);

    }


}
