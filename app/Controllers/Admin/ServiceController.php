<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Icon;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Service;

final class ServiceController extends Controller
{
    public function index(): void
    {
        $this->view('admin.services.index', [
            'pageTitle' => 'Services',
            'pageHeading' => 'Services',
            'services' => Service::all('sort_order ASC'),
            'iconKeys' => Icon::keys(),
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $payload = $this->payload();

        if ($payload['title'] === '' || Service::firstWhere(['slug' => $payload['slug']]) !== null) {
            Session::flash('error', 'Title is required and the slug must be unique.');
            $this->redirect('admin/services');

            return;
        }

        $id = Service::create($payload);
        ActivityLog::record('service.create', 'service', $id, $payload['title']);

        Session::flash('success', 'Service added.');
        $this->redirect('admin/services');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();

        $payload = $this->payload();
        $existing = Service::firstWhere(['slug' => $payload['slug']]);

        if ($existing !== null && (int) $existing['id'] !== $id) {
            Session::flash('error', 'That slug is already used by another service.');
            $this->redirect('admin/services');

            return;
        }

        Service::update($id, $payload);
        ActivityLog::record('service.update', 'service', $id);

        Session::flash('success', 'Service updated.');
        $this->redirect('admin/services');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        Service::delete($id);
        ActivityLog::record('service.delete', 'service', $id);

        Session::flash('success', 'Service moved to trash.');
        $this->redirect('admin/services');
    }

    private function payload(): array
    {
        $title = trim((string) $this->input('title', ''));
        $slug = (string) $this->input('slug', '') ?: $title;
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $slug) ?? '', '-'));

        $icon = (string) $this->input('icon', '');

        return [
            'title' => $title,
            'slug' => $slug,
            'icon' => in_array($icon, Icon::keys(), true) ? $icon : null,
            'short_description' => trim((string) $this->input('short_description', '')),
            'content' => trim((string) $this->input('content', '')),
            'stats' => $this->jsonArrayField('stats'),
            'highlights' => $this->jsonArrayField('highlights'),
            'process_steps' => $this->jsonArrayField('process_steps'),
            'sort_order' => (int) $this->input('sort_order', 0),
            'status' => $this->input('status', 'published') === 'draft' ? 'draft' : 'published',
        ];
    }

    /** Repeater fields (stats/highlights/process_steps) arrive as a JSON string built client-side; store [] rather than an invalid value if it's missing or malformed. */
    private function jsonArrayField(string $key): string
    {
        $decoded = json_decode((string) $this->input($key, '[]'), true);

        return json_encode(is_array($decoded) ? array_values($decoded) : [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
