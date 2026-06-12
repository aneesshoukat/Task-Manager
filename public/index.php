<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Session;
use App\Core\Logger;
use App\Services\CsrfService;

$dotenv = file_get_contents(__DIR__ . '/../.env');
if ($dotenv) {
    foreach (explode("\n", $dotenv) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

Session::start();

Logger::init(__DIR__ . '/../storage/logs');

set_error_handler(function ($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (\Throwable $e) {
    Logger::error($e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);

    $appConfig = require __DIR__ . '/../config/app.php';

    if ($appConfig['debug']) {
        echo '<h1>Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        require __DIR__ . '/../app/Views/errors/500.php';
    }
    exit;
});

$router = new Router();
$request = new Request();

$csrfService = new CsrfService();
if (!Session::has('csrf_token')) {
    $csrfService->generate();
}

$router->addGlobalMiddleware(\App\Middleware\CsrfMiddleware::class);

require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

$router->dispatch($request);
