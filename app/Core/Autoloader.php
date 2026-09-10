<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Zero-dependency PSR-4-style autoloader for the App\ namespace.
 * Avoids requiring Composer for a fresh checkout to boot.
 */
final class Autoloader
{
    public static function register(string $baseDir): void
    {
        spl_autoload_register(static function (string $class) use ($baseDir): void {
            $prefix = 'App\\';

            if (!str_starts_with($class, $prefix)) {
                return;
            }

            $relative = substr($class, strlen($prefix));
            $path = $baseDir . '/app/' . str_replace('\\', '/', $relative) . '.php';

            if (is_file($path)) {
                require $path;
            }
        });
    }
}
