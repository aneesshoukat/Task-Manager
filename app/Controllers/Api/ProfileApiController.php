<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Services\ValidationService;

class ProfileApiController
{
    private ValidationService $validationService;

    public function __construct()
    {
        $this->validationService = new ValidationService();
    }

    public function show(Request $request): void
    {
        $user = $request->user();

        Response::json([
            'success' => true,
            'data' => (new UserResource())->format($user),
        ]);
    }

    public function update(Request $request): void
    {
        $data = $request->only(['name', 'email']);
        $errors = $this->validationService->validateProfile($data);

        if ($errors) {
            Response::json(['success' => false, 'message' => 'Validation failed.', 'errors' => $errors], 422);
            return;
        }

        $db = Database::getInstance();
        $db->update('users', [
            'name' => $data['name'],
            'email' => $data['email'],
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$request->userId()]);

        $user = \App\Models\User::find($request->userId());

        Response::json([
            'success' => true,
            'message' => 'Profile updated.',
            'data' => (new UserResource())->format($user),
        ]);
    }
}
