<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;

class CsrfService
{
    public function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    public function validate(string $token): bool
    {
        $stored = Session::get('csrf_token', '');
        if (empty($stored) || empty($token)) {
            return false;
        }
        return hash_equals($stored, $token);
    }

    public function regenerate(): void
    {
        $this->generate();
    }
}
