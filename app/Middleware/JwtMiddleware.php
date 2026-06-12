<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Services\JwtService;

class JwtMiddleware
{
    public function handle(Request $request, callable $next): void
    {
        $header = $request->header('Authorization', '');
        $token = '';

        if (preg_match('/^Bearer\s+(.+)$/', $header, $matches)) {
            $token = $matches[1];
        }

        if (!$token) {
            Response::json(['success' => false, 'message' => 'Authentication required.'], 401);
            return;
        }

        $jwtService = new JwtService();
        $decoded = $jwtService->verifyToken($token);

        if (!$decoded || ($decoded->type ?? '') !== 'access') {
            Response::json(['success' => false, 'message' => 'Invalid or expired token.'], 401);
            return;
        }

        $user = User::find((int) $decoded->sub);
        if (!$user) {
            Response::json(['success' => false, 'message' => 'User not found.'], 401);
            return;
        }

        $request->setUser($user);
        $next($request);
    }
}
