<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Redirect;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        View::output($view, $data, $layout);
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function redirect(string $path, int $status = 302): void
    {
        header('Location: ' . View::url($path), true, $status);
        exit;
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? View::url('/');
        header('Location: ' . $referer);
        exit;
    }

    protected function abort(int $status, string $message = ''): void
    {
        http_response_code($status);
        echo $message !== '' ? $message : 'Error ' . $status;
        exit;
    }

    /**
     * Call this from a "not found" branch instead of rendering the 404 view
     * directly — it checks the admin-managed redirects table first, since
     * the catch-all /{slug} front route means Router::dispatch() never sees
     * these paths as genuinely unmatched.
     */
    protected function notFoundOr404View(string $requestPath, string $pageTitle = 'Page Not Found'): void
    {
        $redirect = Redirect::findByPath($requestPath);

        if ($redirect !== null) {
            Redirect::recordHit((int) $redirect['id']);
            $this->redirect(ltrim($redirect['to_path'], '/'), (int) $redirect['status_code']);

            return;
        }

        http_response_code(404);
        $this->view('front.errors.404', ['pageTitle' => $pageTitle], 'front.layouts.main');
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return Request::input($key, $default);
    }

    /** Reads an optional integer field, treating missing/empty as null instead of casting to 0. */
    protected function nullableInt(string $key): ?int
    {
        $value = Request::input($key);

        return ($value === null || $value === '') ? null : (int) $value;
    }

    protected function validate(array $data, array $rules): Validator
    {
        return new Validator($data, $rules);
    }

    protected function requireCsrf(): void
    {
        if (!Csrf::verify(Request::input('_csrf_token'))) {
            $this->abort(419, 'Invalid or expired security token. Please refresh and try again.');
        }
    }
}
