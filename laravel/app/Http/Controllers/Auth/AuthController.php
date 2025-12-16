<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\Actions\LoginUserAction;
use App\Domain\Auth\Actions\LogoutUserAction;
use App\Domain\Auth\Actions\RegisterUserAction;
use App\Domain\Auth\DTOs\LoginData;
use App\Domain\Auth\DTOs\RegisterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private LoginUserAction $loginAction,
        private RegisterUserAction $registerAction,
        private LogoutUserAction $logoutAction,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $loginData = LoginData::fromArray($request->validated());

        $result = $this->loginAction->execute($loginData);

        if (! $result) {
            return response()->json([
                'message' => 'Credenciais inválidas',
            ], 401);
        }

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $registerData = RegisterData::fromArray($request->validated());

        $result = $this->registerAction->execute($registerData);

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logoutAction->execute($request->user());

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
}
