<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\CsrfService;

class CsrfMiddleware
{
    public function handle(Request $request, callable $next): void
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $request->input('_csrf_token', '');

            if (!$token) {
                $header = $request->header('X-CSRF-Token', '');
                if ($header) {
                    $token = $header;
                }
            }

            $csrfService = new CsrfService();
            if (!$csrfService->validate($token)) {
                $request->expectsJson()
                    ? Response::json(['success' => false, 'message' => 'CSRF token mismatch.'], 419)
                    : Response::redirectBack();
                return;
            }
        }

        $next($request);
    }
}
