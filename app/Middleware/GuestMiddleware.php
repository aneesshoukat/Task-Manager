<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Services\JwtService;

class GuestMiddleware
{
    public function handle(Request $request, callable $next): void
    {
        $token = $request->cookie('access_token', '');

        if ($token) {
            $jwtService = new JwtService();
            $decoded = $jwtService->verifyToken($token);

            if ($decoded && ($decoded->type ?? '') === 'access') {
                $user = User::find((int) $decoded->sub);
                if ($user) {
                    Response::redirect('/dashboard');
                    return;
                }
            }
        }

        $next($request);
    }
}
