<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function status(int $code): void
    {
        http_response_code($code);
    }

    public static function header(string $key, string $value): void
    {
        header("{$key}: {$value}");
    }

    public static function cookie(string $name, string $value = '', int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true, string $sameSite = 'Lax'): void
    {
        setcookie($name, $value, [
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ]);
    }

    public static function json(mixed $data, int $status = 200): void
    {
        self::status($status);
        self::header('Content-Type', 'application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function view(string $view, array $data = []): void
    {
        $viewPath = __DIR__ . "/../Views/{$view}.php";

        if (!file_exists($viewPath)) {
            self::status(500);
            echo "View not found: {$view}";
            exit;
        }

        extract($data);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        echo $content;
        exit;
    }

    public static function redirect(string $url, int $status = 302): void
    {
        self::status($status);
        self::header('Location', $url);
        exit;
    }

    public static function back(): void
    {
        self::redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }

    public static function redirectBack(): void
    {
        self::back();
    }
}
