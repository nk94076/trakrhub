<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

final class PasswordReset extends Model
{
    protected static string $table = 'password_resets';

    public static function createToken(string $email): string
    {
        $token = bin2hex(random_bytes(32));

        Database::query(
            'INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)',
            [
                'email' => $email,
                'token' => hash('sha256', $token),
                'expires_at' => date('Y-m-d H:i:s', time() + 3600),
            ]
        );

        return $token;
    }

    public static function findValid(string $token): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM password_resets
             WHERE token = :token AND used_at IS NULL AND expires_at > NOW()
             ORDER BY id DESC LIMIT 1',
            ['token' => hash('sha256', $token)]
        );
    }

    public static function markUsed(int $id): void
    {
        Database::query('UPDATE password_resets SET used_at = NOW() WHERE id = :id', ['id' => $id]);
    }
}
