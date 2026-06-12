<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        Response::view($view, $data);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }

    protected function redirectBack(): void
    {
        Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}
