<?php

declare(strict_types=1);

namespace App\Core;

class Logger
{
    private static string $logDir;

    public static function init(string $logDir): void
    {
        self::$logDir = rtrim($logDir, '/\\');
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0775, true);
        }
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        $appConfig = require __DIR__ . '/../../config/app.php';
        if ($appConfig['debug']) {
            self::log('DEBUG', $message, $context);
        }
    }

    private static function log(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logLine = "[{$date}] {$level}: {$message}{$contextStr}" . PHP_EOL;

        $filename = self::$logDir . '/task-manager-' . date('Y-m-d') . '.log';
        file_put_contents($filename, $logLine, FILE_APPEND | LOCK_EX);
    }
}
