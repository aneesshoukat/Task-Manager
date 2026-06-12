<?php

declare(strict_types=1);

return [
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => (bool) ($_ENV['APP_DEBUG'] ?? true),
    'url' => $_ENV['APP_URL'] ?? '',
    'name' => 'Task Manager',
];
