<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Redirect;

final class RedirectController extends Controller
{
    public function index(): void
    {
        $this->view('admin.redirects.index', [
            'pageTitle' => 'Redirects',
            'pageHeading' => 'Redirects',
            'redirects' => Redirect::all('id DESC'),
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $payload = $this->payload();

        if ($payload === null) {
            Session::flash('error', 'From and To paths are required, and must start with /.');
            $this->redirect('admin/redirects');

            return;
        }

        if (Redirect::findByPath($payload['from_path']) !== null) {
            Session::flash('error', 'A redirect for that path already exists.');
            $this->redirect('admin/redirects');

            return;
        }

        $id = Redirect::create($payload);
        ActivityLog::record('redirect.create', 'redirect', $id, $payload['from_path']);

        Session::flash('success', 'Redirect added.');
        $this->redirect('admin/redirects');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        Redirect::forceDelete($id);
        ActivityLog::record('redirect.delete', 'redirect', $id);

        Session::flash('success', 'Redirect deleted.');
        $this->redirect('admin/redirects');
    }

    private function payload(): ?array
    {
        $from = trim((string) $this->input('from_path', ''));
        $to = trim((string) $this->input('to_path', ''));
        $status = (int) $this->input('status_code', 301);

        if ($from === '' || $to === '' || !str_starts_with($from, '/') || !str_starts_with($to, '/')) {
            return null;
        }

        return [
            'from_path' => rtrim($from, '/') ?: '/',
            'to_path' => $to,
            'status_code' => in_array($status, [301, 302, 307, 308], true) ? $status : 301,
        ];
    }
}
