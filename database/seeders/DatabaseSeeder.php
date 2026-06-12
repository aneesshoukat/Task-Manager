<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = file_get_contents(__DIR__ . '/../../.env');
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

echo "Seeding database...\n";

$password = password_hash('password123', PASSWORD_BCRYPT);
$now = date('Y-m-d H:i:s');

$db->query("DELETE FROM activity_logs");
$db->query("DELETE FROM refresh_tokens");
$db->query("DELETE FROM login_attempts");
$db->query("DELETE FROM tasks");
$db->query("DELETE FROM users");

$userId = $db->insert('users', [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => $password,
    'created_at' => $now,
    'updated_at' => $now,
]);

echo "Created user: {$userId} (john@example.com / password123)\n";

$titles = [
    'Complete project documentation',
    'Fix login page styling',
    'Implement dark mode',
    'Write unit tests for TaskService',
    'Set up CI/CD pipeline',
    'Review pull requests',
    'Update dependencies',
    'Refactor AuthController',
    'Add pagination to task list',
    'Create database migration script',
];

$priorities = ['low', 'medium', 'high'];

for ($i = 0; $i < 10; $i++) {
    $days = rand(-5, 30);
    $dueDate = $days > 0 ? date('Y-m-d', strtotime("+{$days} days")) : null;

    $db->insert('tasks', [
        'user_id' => $userId,
        'title' => $titles[$i],
        'description' => "This is a sample task: {$titles[$i]}",
        'priority' => $priorities[array_rand($priorities)],
        'status' => $i < 3 ? 'completed' : 'pending',
        'due_date' => $dueDate,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}

echo "Created 10 sample tasks.\n";
echo "Seeding complete.\n";
