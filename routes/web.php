<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\TaskController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$router->get('/', function () {
    \App\Core\Response::redirect('/login');
});

$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
$router->get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [GuestMiddleware::class]);

$router->get('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
$router->post('/refresh', [AuthController::class, 'refresh']);

$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);

$router->get('/tasks', [TaskController::class, 'index'], [AuthMiddleware::class]);
$router->get('/tasks/create', [TaskController::class, 'create'], [AuthMiddleware::class]);
$router->post('/tasks', [TaskController::class, 'store'], [AuthMiddleware::class]);
$router->get('/tasks/export', [TaskController::class, 'exportCsv'], [AuthMiddleware::class]);
$router->post('/tasks/import', [TaskController::class, 'importCsv'], [AuthMiddleware::class]);
$router->get('/tasks/trashed', [TaskController::class, 'trashed'], [AuthMiddleware::class]);
$router->get('/tasks/{id}', [TaskController::class, 'show'], [AuthMiddleware::class]);
$router->get('/tasks/{id}/edit', [TaskController::class, 'edit'], [AuthMiddleware::class]);
$router->put('/tasks/{id}', [TaskController::class, 'update'], [AuthMiddleware::class]);
$router->get('/tasks/{id}/delete', [TaskController::class, 'destroy'], [AuthMiddleware::class]);
$router->get('/tasks/{id}/complete', [TaskController::class, 'complete'], [AuthMiddleware::class]);
$router->get('/tasks/{id}/restore', [TaskController::class, 'restore'], [AuthMiddleware::class]);

$router->get('/profile', [ProfileController::class, 'show'], [AuthMiddleware::class]);
$router->put('/profile', [ProfileController::class, 'update'], [AuthMiddleware::class]);
$router->get('/profile/change-password', [ProfileController::class, 'changePassword'], [AuthMiddleware::class]);
$router->put('/profile/change-password', [ProfileController::class, 'changePassword'], [AuthMiddleware::class]);
$router->post('/profile/avatar', [ProfileController::class, 'uploadAvatar'], [AuthMiddleware::class]);
