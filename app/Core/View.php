<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Plain-PHP template renderer (no compiled template language). Views live
 * under app/Views and are included with $data extracted into scope.
 * All dynamic output in views must go through View::e() to prevent XSS.
 */
final class View
{
    private const BASE_PATH = __DIR__ . '/../Views/';

    public static function render(string $view, array $data = [], ?string $layout = null): string
    {
        $path = self::resolvePath($view);

        extract($data, EXTR_SKIP);

        ob_start();
        require $path;
        $content = ob_get_clean();

        if ($layout !== null) {
            $layoutPath = self::resolvePath($layout);
            extract($data, EXTR_SKIP);

            ob_start();
            require $layoutPath;

            return (string) ob_get_clean();
        }

        return (string) $content;
    }

    public static function output(string $view, array $data = [], ?string $layout = null): void
    {
        echo self::render($view, $data, $layout);
    }

    public static function partial(string $view, array $data = []): void
    {
        $path = self::resolvePath($view);
        extract($data, EXTR_SKIP);
        require $path;
    }

    private static function resolvePath(string $view): string
    {
        $relative = str_replace('.', '/', $view) . '.php';
        $path = self::BASE_PATH . $relative;

        if (!is_file($path)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        return $path;
    }

    /** Escape a value for safe HTML output. */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function csrfField(): string
    {
        return Csrf::field();
    }

    public static function asset(string $path): string
    {
        $appUrl = (require dirname(__DIR__, 2) . '/config/app.php')['url'];

        return $appUrl . '/assets/' . ltrim($path, '/');
    }

    public static function url(string $path = ''): string
    {
        $appUrl = (require dirname(__DIR__, 2) . '/config/app.php')['url'];

        return $appUrl . '/' . ltrim($path, '/');
    }
}
