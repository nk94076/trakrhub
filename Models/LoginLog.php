<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Request;

final class LoginLog extends Model
{
    protected static string $table = 'login_logs';

    public static function record(?int $userId, string $email, string $status): void
    {
        static::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'status' => $status,
        ]);
    }
}
