<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\MiddlewareInterface;
use App\Core\View;

final class GuestMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        if (!Auth::check()) {
            return true;
        }

        header('Location: ' . View::url('admin/dashboard'));

        return false;
    }
}
