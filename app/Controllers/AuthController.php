<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\CsrfService;
use App\Services\ValidationService;

class AuthController extends Controller
{
    private AuthService $authService;
    private ValidationService $validationService;
    private CsrfService $csrfService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->validationService = new ValidationService();
        $this->csrfService = new CsrfService();
    }

    public function showLogin(Request $request): void
    {
        $this->csrfService->generate();
        $this->render('auth/login');
    }

    public function login(Request $request): void
    {
        $data = $request->only(['email', 'password']);

        $errors = $this->validationService->validateLogin($data);
        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            Response::redirect('/login');
            return;
        }

        $result = $this->authService->login($data['email'], $data['password']);

        if (!$result['success']) {
            Session::flash('error', $result['message']);
            Session::flash('old', $data);
            Response::redirect('/login');
            return;
        }

        Response::cookie('access_token', $result['access_token'], time() + 3600, '/', '', false, true, 'Lax');
        Response::cookie('refresh_token', $result['refresh_token'], time() + 604800, '/', '', false, true, 'Lax');

        Response::redirect('/dashboard');
    }

    public function showRegister(Request $request): void
    {
        $this->csrfService->generate();
        $this->render('auth/register');
    }

    public function register(Request $request): void
    {
        $data = $request->only(['name', 'email', 'password', 'password_confirmation']);

        $errors = $this->validationService->validateRegister($data);
        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            Response::redirect('/register');
            return;
        }

        $result = $this->authService->register($data);

        if (!$result['success']) {
            Session::flash('error', $result['message']);
            Session::flash('old', $data);
            Response::redirect('/register');
            return;
        }

        Response::cookie('access_token', $result['access_token'], time() + 3600, '/', '', false, true, 'Lax');
        Response::cookie('refresh_token', $result['refresh_token'], time() + 604800, '/', '', false, true, 'Lax');

        Response::redirect('/dashboard');
    }

    public function logout(Request $request): void
    {
        $userId = $request->userId();
        if ($userId) {
            $this->authService->logout($userId);
        }

        Response::cookie('access_token', '', time() - 3600, '/', '', false, true, 'Lax');
        Response::cookie('refresh_token', '', time() - 3600, '/', '', false, true, 'Lax');

        Response::redirect('/login');
    }

    public function refresh(Request $request): void
    {
        $refreshToken = $request->cookie('refresh_token', '');

        if (!$refreshToken) {
            Response::redirect('/login');
            return;
        }

        $result = $this->authService->refreshAccessToken($refreshToken);

        if (!$result['success']) {
            Response::redirect('/login');
            return;
        }

        Response::cookie('access_token', $result['access_token'], time() + 3600, '/', '', false, true, 'Lax');
        Response::cookie('refresh_token', $result['refresh_token'], time() + 604800, '/', '', false, true, 'Lax');

        Response::redirect('/dashboard');
    }
}
