<?php

declare(strict_types=1);

if (!function_exists('escape')) {
    function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): string
    {
        return escape((string) ($_POST[$key] ?? $default));
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return \App\Core\Session::get('csrf_token', '');
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . escape($token) . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . escape($method) . '">';
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $appUrl = config('app.url', '');
        return rtrim($appUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $appUrl = config('app.url', '');
        return rtrim($appUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $value = null): mixed
    {
        return \App\Core\Session::flash($key, $value);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);
        $path = __DIR__ . '/../../config/' . $file . '.php';

        if (!file_exists($path)) {
            return $default;
        }

        $config = require $path;

        foreach ($parts as $part) {
            if (!is_array($config) || !array_key_exists($part, $config)) {
                return $default;
            }
            $config = $config[$part];
        }

        return $config;
    }
}
