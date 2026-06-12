<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $query;
    private array $body;
    private array $files;
    private array $cookies;
    private array $server;
    private ?array $user = null;

    public function __construct()
    {
        $this->query = $_GET;
        $this->body = $_POST;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->server = $_SERVER;

        $contentType = $this->header('Content-Type');
        if ($contentType && str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->body = array_merge($this->body, $decoded);
            }
        }

        if (in_array($this->getMethod(), ['PUT', 'PATCH', 'DELETE'])) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->body = $decoded;
            } else {
                parse_str($raw, $parsed);
                $this->body = array_merge($this->body, $parsed);
            }
        }
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function getUri(): string
    {
        $uri = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return rtrim($uri, '/') ?: '/';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function only(array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = $this->input($key);
        }
        return $data;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    public function header(string $key, mixed $default = null): mixed
    {
        $headerName = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$headerName] ?? $default;
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function setUser(array $user): void
    {
        $this->user = $user;
    }

    public function user(): ?array
    {
        return $this->user;
    }

    public function userId(): ?int
    {
        return $this->user['id'] ?? null;
    }

    public function isAjax(): bool
    {
        return strtolower($this->header('X-Requested-With', '')) === 'xmlhttprequest';
    }

    public function expectsJson(): bool
    {
        $accept = $this->header('Accept', '');
        return str_contains($accept, 'application/json') || $this->isAjax();
    }
}
