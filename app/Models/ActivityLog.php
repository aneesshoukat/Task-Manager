<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ActivityLog extends Model
{
    protected static string $table = 'activity_logs';
    protected static string $primaryKey = 'id';
}
