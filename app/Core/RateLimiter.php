<?php

declare(strict_types=1);

namespace App\Core;

/**
 * File-backed sliding-window rate limiter for endpoints like login,
 * forgot-password and the public contact form. Keyed by an arbitrary
 * string (e.g. "login:{ip}") so callers control the scope.
 */
final class RateLimiter
{
    private static function cacheFile(string $key): string
    {
        $dir = dirname(__DIR__, 2) . '/storage/cache/rate_limits';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir . '/' . hash('sha256', $key) . '.json';
    }

    public static function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $attempts = self::readAttempts($key, $decaySeconds);

        return count($attempts) >= $maxAttempts;
    }

    public static function hit(string $key, int $decaySeconds): void
    {
        $attempts = self::readAttempts($key, $decaySeconds);
        $attempts[] = time();

        file_put_contents(self::cacheFile($key), json_encode($attempts), LOCK_EX);
    }

    public static function clear(string $key): void
    {
        $file = self::cacheFile($key);

        if (is_file($file)) {
            unlink($file);
        }
    }

    public static function availableIn(string $key, int $decaySeconds): int
    {
        $attempts = self::readAttempts($key, $decaySeconds);

        if ($attempts === []) {
            return 0;
        }

        $oldest = min($attempts);
        $remaining = ($oldest + $decaySeconds) - time();

        return max(0, $remaining);
    }

    private static function readAttempts(string $key, int $decaySeconds): array
    {
        $file = self::cacheFile($key);

        if (!is_file($file)) {
            return [];
        }

        $attempts = json_decode((string) file_get_contents($file), true) ?: [];
        $cutoff = time() - $decaySeconds;

        return array_values(array_filter($attempts, static fn ($ts) => $ts >= $cutoff));
    }
}
