<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\CsrfService;
use App\Services\ValidationService;

class ProfileController extends Controller
{
    private ValidationService $validationService;
    private CsrfService $csrfService;

    public function __construct()
    {
        $this->validationService = new ValidationService();
        $this->csrfService = new CsrfService();
    }

    public function show(Request $request): void
    {
        $this->csrfService->generate();
        $this->render('profile/index', ['user' => $request->user()]);
    }

    public function update(Request $request): void
    {
        $data = $request->only(['name', 'email']);

        $errors = $this->validationService->validateProfile($data);
        if ($errors) {
            Session::flash('errors', $errors);
            Response::redirect('/profile');
            return;
        }

        $db = Database::getInstance();
        $db->update('users', [
            'name' => $data['name'],
            'email' => $data['email'],
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$request->userId()]);

        Session::flash('success', 'Profile updated.');
        Response::redirect('/profile');
    }

    public function changePassword(Request $request): void
    {
        $data = $request->only(['current_password', 'new_password', 'new_password_confirmation']);

        $errors = $this->validationService->validatePasswordChange($data);
        if ($errors) {
            Session::flash('errors', $errors);
            Response::redirect('/profile/change-password');
            return;
        }

        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$request->userId()]);

        if (!$user || !password_verify($data['current_password'], $user['password'])) {
            Session::flash('error', 'Current password is incorrect.');
            Response::redirect('/profile/change-password');
            return;
        }

        $db->update('users', [
            'password' => password_hash($data['new_password'], PASSWORD_BCRYPT),
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$request->userId()]);

        Session::flash('success', 'Password changed.');
        Response::redirect('/profile');
    }

    public function uploadAvatar(Request $request): void
    {
        if (!$request->hasFile('avatar')) {
            Session::flash('error', 'Please select an image.');
            Response::redirect('/profile');
            return;
        }

        $file = $request->file('avatar');
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file['type'], $allowedTypes)) {
            Session::flash('error', 'Invalid image type. Allowed: JPG, PNG, GIF, WebP.');
            Response::redirect('/profile');
            return;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            Session::flash('error', 'Image must be under 2MB.');
            Response::redirect('/profile');
            return;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $request->userId() . '_' . time() . '.' . $ext;
        $uploadPath = __DIR__ . '/../../public/uploads/' . $filename;

        move_uploaded_file($file['tmp_name'], $uploadPath);

        $db = Database::getInstance();
        $db->update('users', [
            'avatar' => $filename,
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$request->userId()]);

        Session::flash('success', 'Avatar updated.');
        Response::redirect('/profile');
    }
}
