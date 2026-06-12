<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class RateLimitMiddleware
{
    private int $maxAttempts;
    private int $decayMinutes;

    public function __construct(int $maxAttempts = 60, int $decayMinutes = 1)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    public function handle(Request $request, callable $next): void
    {
        $ip = $request->ip();
        $key = 'rate_limit:' . $ip . ':' . $request->getMethod() . $request->getUri();

        $db = Database::getInstance();
        $now = time();

        $db->query(
            "DELETE FROM login_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$this->decayMinutes]
        );

        $attempts = $db->fetch(
            "SELECT COUNT(*) as count FROM login_attempts WHERE email = ?",
            [$key]
        );

        if (($attempts['count'] ?? 0) >= $this->maxAttempts) {
            $request->expectsJson()
                ? Response::json(['success' => false, 'message' => 'Too many requests.'], 429)
                : Response::redirectBack();
            return;
        }

        $next($request);
    }
}
