<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class TaskComment extends Model
{
    protected static string $table = 'task_comments';
    protected static string $primaryKey = 'id';
}
