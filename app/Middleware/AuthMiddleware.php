<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\MiddlewareInterface;
use App\Core\Session;
use App\Core\View;

final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): bool
    {
        if (Auth::check()) {
            return true;
        }

        Session::flash('error', 'Please log in to continue.');
        header('Location: ' . View::url('admin/login'));

        return false;
    }
}
