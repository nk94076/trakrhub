<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Icon;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Industry;

final class IndustryController extends Controller
{
    public function index(): void
    {
        $this->view('admin.industries.index', [
            'pageTitle' => 'Industries',
            'pageHeading' => 'Industries',
            'industries' => Industry::all('sort_order ASC'),
            'iconKeys' => Icon::keys(),
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $payload = $this->payload();

        if ($payload['title'] === '' || Industry::firstWhere(['slug' => $payload['slug']]) !== null) {
            Session::flash('error', 'Title is required and the slug must be unique.');
            $this->redirect('admin/industries');

            return;
        }

        $id = Industry::create($payload);
        ActivityLog::record('industry.create', 'industry', $id, $payload['title']);

        Session::flash('success', 'Industry added.');
        $this->redirect('admin/industries');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();

        $payload = $this->payload();
        $existing = Industry::firstWhere(['slug' => $payload['slug']]);

        if ($existing !== null && (int) $existing['id'] !== $id) {
            Session::flash('error', 'That slug is already used by another industry.');
            $this->redirect('admin/industries');

            return;
        }

        Industry::update($id, $payload);
        ActivityLog::record('industry.update', 'industry', $id);

        Session::flash('success', 'Industry updated.');
        $this->redirect('admin/industries');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        Industry::delete($id);
        ActivityLog::record('industry.delete', 'industry', $id);

        Session::flash('success', 'Industry moved to trash.');
        $this->redirect('admin/industries');
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
            'description' => trim((string) $this->input('description', '')),
            'sort_order' => (int) $this->input('sort_order', 0),
            'status' => $this->input('status', 'published') === 'draft' ? 'draft' : 'published',
        ];
    }
}
