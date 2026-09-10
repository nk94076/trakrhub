<?php

declare(strict_types=1);

namespace App\Core;

/**
 * File-based logger writing to storage/logs/{level}-{Y-m-d}.log.
 * Kept dependency-free; swap for Monolog later without changing call sites.
 */
final class Logger
{
    private static function write(string $level, string $message, array $context = []): void
    {
        $dir = dirname(__DIR__, 2) . '/storage/logs';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $dir . '/' . $level . '-' . date('Y-m-d') . '.log';
        $line = sprintf(
            '[%s] %s%s' . PHP_EOL,
            date('Y-m-d H:i:s'),
            $message,
            $context === [] ? '' : ' ' . json_encode($context, JSON_UNESCAPED_SLASHES)
        );

        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('info', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('warning', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('error', $message, $context);
    }
}
