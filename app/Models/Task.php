<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Task extends Model
{
    protected static string $table = 'tasks';
    protected static string $primaryKey = 'id';
}
