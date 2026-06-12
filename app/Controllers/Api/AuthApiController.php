<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Services\ValidationService;

class AuthApiController
{
    private AuthService $authService;
    private ValidationService $validationService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->validationService = new ValidationService();
    }

    public function register(Request $request): void
    {
        $data = $request->only(['name', 'email', 'password', 'password_confirmation']);
        $errors = $this->validationService->validateRegister($data);

        if ($errors) {
            Response::json(['success' => false, 'message' => 'Validation failed.', 'errors' => $errors], 422);
            return;
        }

        $result = $this->authService->register($data);

        if (!$result['success']) {
            Response::json(['success' => false, 'message' => $result['message']], 409);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Registration successful.',
            'data' => [
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
            ],
        ], 201);
    }

    public function login(Request $request): void
    {
        $data = $request->only(['email', 'password']);
        $errors = $this->validationService->validateLogin($data);

        if ($errors) {
            Response::json(['success' => false, 'message' => 'Validation failed.', 'errors' => $errors], 422);
            return;
        }

        $result = $this->authService->login($data['email'], $data['password']);

        if (!$result['success']) {
            Response::json(['success' => false, 'message' => $result['message']], 401);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => (new UserResource())->format($result['user']),
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
            ],
        ]);
    }

    public function logout(Request $request): void
    {
        $userId = $request->userId();
        if ($userId) {
            $this->authService->logout($userId);
        }

        Response::json(['success' => true, 'message' => 'Logged out successfully.']);
    }

    public function refresh(Request $request): void
    {
        $refreshToken = $request->input('refresh_token', '');

        if (!$refreshToken) {
            Response::json(['success' => false, 'message' => 'Refresh token required.'], 400);
            return;
        }

        $result = $this->authService->refreshAccessToken($refreshToken);

        if (!$result['success']) {
            Response::json(['success' => false, 'message' => $result['message']], 401);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Token refreshed.',
            'data' => [
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
            ],
        ]);
    }
}
