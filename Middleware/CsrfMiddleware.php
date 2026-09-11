<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\MiddlewareInterface;
use App\Core\Request;

/**
 * Applied to every state-changing route (POST/PUT/PATCH/DELETE).
 * GET requests never mutate state so they are exempt by definition.
 */
final class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        if (!in_array(Request::method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return true;
        }

        if (Csrf::verify(Request::input('_csrf_token'))) {
            return true;
        }

        http_response_code(419);
        echo 'Invalid or expired security token. Please refresh the page and try again.';

        return false;
    }
}
