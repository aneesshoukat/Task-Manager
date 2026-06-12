<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class LoginAttempt extends Model
{
    protected static string $table = 'login_attempts';
    protected static string $primaryKey = 'id';
}
