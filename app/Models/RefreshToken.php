<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class RefreshToken extends Model
{
    protected static string $table = 'refresh_tokens';
    protected static string $primaryKey = 'id';
}
