<?php

declare(strict_types=1);

namespace App\Controllers\Api;

class UserResource
{
    public function format(?array $user): ?array
    {
        if ($user === null) {
            return null;
        }

        return [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['avatar'],
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at'],
        ];
    }
}
