<?php

declare(strict_types=1);

use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\TaskApiController;
use App\Controllers\Api\ProfileApiController;
use App\Controllers\Api\DashboardApiController;
use App\Middleware\JwtMiddleware;

$router->post('/api/v1/auth/register', [AuthApiController::class, 'register']);
$router->post('/api/v1/auth/login', [AuthApiController::class, 'login']);
$router->post('/api/v1/auth/logout', [AuthApiController::class, 'logout'], [JwtMiddleware::class]);
$router->post('/api/v1/auth/refresh', [AuthApiController::class, 'refresh']);

$router->get('/api/v1/profile', [ProfileApiController::class, 'show'], [JwtMiddleware::class]);
$router->put('/api/v1/profile', [ProfileApiController::class, 'update'], [JwtMiddleware::class]);

$router->get('/api/v1/tasks', [TaskApiController::class, 'index'], [JwtMiddleware::class]);
$router->get('/api/v1/tasks/{id}', [TaskApiController::class, 'show'], [JwtMiddleware::class]);
$router->post('/api/v1/tasks', [TaskApiController::class, 'store'], [JwtMiddleware::class]);
$router->put('/api/v1/tasks/{id}', [TaskApiController::class, 'update'], [JwtMiddleware::class]);
$router->patch('/api/v1/tasks/{id}/complete', [TaskApiController::class, 'complete'], [JwtMiddleware::class]);
$router->delete('/api/v1/tasks/{id}', [TaskApiController::class, 'destroy'], [JwtMiddleware::class]);

$router->get('/api/v1/dashboard/stats', [DashboardApiController::class, 'stats'], [JwtMiddleware::class]);
