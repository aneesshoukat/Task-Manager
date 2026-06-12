<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\JwtService;

class AuthMiddleware
{
    public function handle(Request $request, callable $next): void
    {
        $token = $request->cookie('access_token') ?: '';

        if (!$token) {
            $header = $request->header('Authorization', '');
            if (preg_match('/^Bearer\s+(.+)$/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            $request->expectsJson()
                ? Response::json(['success' => false, 'message' => 'Unauthenticated.'], 401)
                : Response::redirect('/login');
            return;
        }

        $jwtService = new JwtService();
        $decoded = $jwtService->verifyToken($token);

        if (!$decoded || ($decoded->type ?? '') !== 'access') {
            $request->expectsJson()
                ? Response::json(['success' => false, 'message' => 'Invalid or expired token.'], 401)
                : Response::redirect('/login');
            return;
        }

        $user = \App\Models\User::find((int) $decoded->sub);
        if (!$user) {
            $request->expectsJson()
                ? Response::json(['success' => false, 'message' => 'User not found.'], 401)
                : Response::redirect('/login');
            return;
        }

        $request->setUser($user);
        $next($request);
    }
}
