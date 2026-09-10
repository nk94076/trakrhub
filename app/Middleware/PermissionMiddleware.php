<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\MiddlewareInterface;

/**
 * Routes reference it as: PermissionMiddleware::require('settings.manage')
 * which returns an already-constructed instance (not a class name) because
 * the required permission differs per-route — Router::dispatch() accepts
 * both class-name strings and pre-built MiddlewareInterface instances.
 */
final class PermissionMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly string $permissionSlug = '')
    {
    }

    public static function require(string $permissionSlug): self
    {
        return new self($permissionSlug);
    }

    public function handle(): bool
    {
        if (Auth::isSuperAdmin() || Auth::can($this->permissionSlug)) {
            return true;
        }

        http_response_code(403);
        echo 'You do not have permission to access this resource.';

        return false;
    }
}
