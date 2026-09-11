<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Redirect;

/**
 * Minimal regex-based router: supports GET/POST/PUT/PATCH/DELETE,
 * {param} placeholders, per-route middleware stacks and route groups
 * with a shared prefix + middleware (used for the /admin area).
 */
final class Router
{
    private array $routes = [];
    private string $groupPrefix = '';
    private array $groupMiddleware = [];

    public function get(string $uri, array $action, array $middleware = []): void
    {
        $this->addRoute('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, array $action, array $middleware = []): void
    {
        $this->addRoute('POST', $uri, $action, $middleware);
    }

    public function put(string $uri, array $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $uri, $action, $middleware);
    }

    public function patch(string $uri, array $action, array $middleware = []): void
    {
        $this->addRoute('PATCH', $uri, $action, $middleware);
    }

    public function delete(string $uri, array $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $uri, $action, $middleware);
    }

    public function group(string $prefix, array $middleware, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($previousMiddleware, $middleware);

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $uri, array $action, array $middleware): void
    {
        $fullUri = rtrim($this->groupPrefix . $uri, '/');
        $fullUri = $fullUri === '' ? '/' : $fullUri;

        $this->routes[] = [
            'method' => $method,
            'uri' => $fullUri,
            'action' => $action,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
            'pattern' => $this->toPattern($fullUri),
        ];
    }

    private function toPattern(string $uri): string
    {
        $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $uri);

        return '#^' . $pattern . '$#';
    }

    public function dispatch(): void
    {
        $method = Request::method();
        $uri = Request::uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (!preg_match($route['pattern'], $uri, $matches)) {
                continue;
            }

            $params = array_filter($matches, static fn ($key) => !is_int($key), ARRAY_FILTER_USE_KEY);

            // Route params always arrive as strings from the regex match; coerce
            // purely-numeric ones (ids) to int so they satisfy typed controller
            // parameters under strict_types. Non-numeric params (e.g. {token}) pass through untouched.
            $params = array_map(
                static fn ($value) => ctype_digit($value) ? (int) $value : $value,
                $params
            );

            foreach ($route['middleware'] as $middlewareEntry) {
                /** @var MiddlewareInterface $middleware */
                $middleware = $middlewareEntry instanceof MiddlewareInterface
                    ? $middlewareEntry
                    : new $middlewareEntry();

                if (!$middleware->handle()) {
                    return;
                }
            }

            [$controllerClass, $method2] = $route['action'];
            $controller = new $controllerClass();
            $controller->$method2(...array_values($params));

            return;
        }

        if ($method === 'GET' && $this->redirectTo($uri)) {
            return;
        }

        $this->notFound();
    }

    /** Checks the admin-managed redirects table for an unmatched GET path. Returns true if a redirect was sent. */
    private function redirectTo(string $uri): bool
    {
        $redirect = Redirect::findByPath($uri);

        if ($redirect === null) {
            return false;
        }

        Redirect::recordHit((int) $redirect['id']);
        header('Location: ' . View::url(ltrim($redirect['to_path'], '/')), true, (int) $redirect['status_code']);

        return true;
    }

    private function notFound(): void
    {
        http_response_code(404);
        View::output('front.errors.404', [], 'front.layouts.main');
    }
}
