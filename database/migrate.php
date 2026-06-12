<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = file_get_contents(__DIR__ . '/../.env');
if ($dotenv) {
    foreach (explode("\n", $dotenv) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

use App\Core\Database;

$db = Database::getInstance();
$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.php');
sort($files);

foreach ($files as $file) {
    $migration = require $file;
    $name = basename($file);
    echo "Running migration: {$name}\n";
    $db->query($migration['up']);
    echo "Done: {$name}\n";
}

echo "All migrations complete.\n";
