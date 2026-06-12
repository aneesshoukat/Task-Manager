<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Validator;

class ValidationService
{
    public function validateRegister(array $data): array
    {
        $v = new Validator($data);
        $v->required('name', 'Name')
          ->required('email', 'Email')
          ->email('email', 'Email')
          ->required('password', 'Password')
          ->min('password', 8, 'Password')
          ->confirmed('password', 'Password');

        return $v->passes() ? [] : $v->errors();
    }

    public function validateLogin(array $data): array
    {
        $v = new Validator($data);
        $v->required('email', 'Email')
          ->email('email', 'Email')
          ->required('password', 'Password');

        return $v->passes() ? [] : $v->errors();
    }

    public function validateTask(array $data): array
    {
        $v = new Validator($data);
        $v->required('title', 'Title')
          ->max('title', 255, 'Title')
          ->max('description', 5000, 'Description')
          ->in('priority', ['low', 'medium', 'high'], 'Priority')
          ->in('status', ['pending', 'completed'], 'Status');

        if (!empty($data['due_date'])) {
            $v->date('due_date', 'Due date');
        }

        return $v->passes() ? [] : $v->errors();
    }

    public function validateProfile(array $data): array
    {
        $v = new Validator($data);
        $v->required('name', 'Name')
          ->max('name', 255, 'Name')
          ->required('email', 'Email')
          ->email('email', 'Email');

        return $v->passes() ? [] : $v->errors();
    }

    public function validatePasswordChange(array $data): array
    {
        $v = new Validator($data);
        $v->required('current_password', 'Current password')
          ->required('new_password', 'New password')
          ->min('new_password', 8, 'New password')
          ->confirmed('new_password', 'New password');

        return $v->passes() ? [] : $v->errors();
    }
}
