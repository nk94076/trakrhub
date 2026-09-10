<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

final class Auth
{
    private const SESSION_KEY = 'auth_user_id';

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);

        if ($user === null || $user['status'] !== 'active') {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        if (password_needs_rehash($user['password'], PASSWORD_BCRYPT)) {
            User::update((int) $user['id'], ['password' => password_hash($password, PASSWORD_BCRYPT)]);
        }

        self::login((int) $user['id']);

        return true;
    }

    public static function login(int $userId): void
    {
        Session::regenerate();
        Session::set(self::SESSION_KEY, $userId);

        User::update($userId, [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => Request::ip(),
        ]);
    }

    public static function logout(): void
    {
        Session::remove(self::SESSION_KEY);
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has(self::SESSION_KEY);
    }

    public static function id(): ?int
    {
        $id = Session::get(self::SESSION_KEY);

        return $id === null ? null : (int) $id;
    }

    public static function user(): ?array
    {
        $id = self::id();

        return $id === null ? null : User::withRole($id);
    }

    public static function can(string $permissionSlug): bool
    {
        $id = self::id();

        if ($id === null) {
            return false;
        }

        return in_array($permissionSlug, User::permissions($id), true);
    }

    public static function isSuperAdmin(): bool
    {
        $user = self::user();

        return $user !== null && $user['role_slug'] === 'super-admin';
    }
}
