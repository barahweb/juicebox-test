<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends ApiController
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return $this->sendResponse([
            'access_token' => $result['access_token'],
            'token_type' => 'Bearer',
        ], 'Registrasi berhasil.', Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return $this->sendError('Email atau password salah.', null, Response::HTTP_UNAUTHORIZED);
        }

        return $this->sendResponse([
            'access_token' => $result['access_token'],
            'token_type' => 'Bearer',
        ], 'Login berhasil.');
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return $this->sendResponse(null, 'Berhasil logout.');
    }

    public function me(Request $request)
    {
        return $this->sendResponse(new UserResource($request->user()), 'Data user berhasil diambil.');
    }
}
