<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Icon;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\SeoMeta;

final class PageController extends Controller
{
    public function index(): void
    {
        $showTrash = $this->input('trash') === '1';

        $pages = $showTrash
            ? Database::fetchAll('SELECT * FROM pages WHERE deleted_at IS NOT NULL ORDER BY id ASC')
            : Page::all('id ASC');

        $this->view('admin.pages.index', [
            'pageTitle' => 'Pages',
            'pageHeading' => 'Pages',
            'pages' => $pages,
            'showTrash' => $showTrash,
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $title = trim((string) $this->input('title', ''));
        $slug = $this->slugify((string) $this->input('slug', '') ?: $title);

        $validator = $this->validate(['title' => $title, 'slug' => $slug], [
            'title' => 'required|max:255',
            'slug' => 'required|slug',
        ]);

        if ($validator->fails() || Page::firstWhere(['slug' => $slug]) !== null) {
            Session::flash('error', $validator->fails() ? 'Title and a valid slug are required.' : 'That slug is already in use.');
            $this->redirect('admin/pages');

            return;
        }

        $id = Page::create([
            'title' => $title,
            'slug' => $slug,
            'template' => 'default',
            'status' => 'draft',
        ]);

        ActivityLog::record('page.create', 'page', $id, $title);

        Session::flash('success', 'Page created. Add sections to build it out.');
        $this->redirect('admin/pages/' . $id . '/edit');
    }

    public function edit(int $id): void
    {
        $page = Page::find($id);

        if ($page === null) {
            $this->abort(404, 'Page not found.');

            return;
        }

        $this->view('admin.pages.edit', [
            'pageTitle' => 'Edit: ' . $page['title'],
            'pageHeading' => 'Edit Page — ' . $page['title'],
            'page' => $page,
            'sections' => PageSection::forPageAdmin($id),
            'seo' => SeoMeta::forEntity('page', $id),
            'iconKeys' => Icon::keys(),
        ], 'admin.layouts.app');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();

        $page = Page::find($id);

        if ($page === null) {
            $this->abort(404);

            return;
        }

        $title = trim((string) $this->input('title', ''));
        $slug = $this->slugify((string) $this->input('slug', ''));
        $status = in_array($this->input('status'), ['published', 'draft'], true) ? $this->input('status') : 'draft';

        $existing = Page::firstWhere(['slug' => $slug]);

        if ($existing !== null && (int) $existing['id'] !== $id) {
            Session::flash('error', 'That slug is already used by another page.');
            $this->redirect('admin/pages/' . $id . '/edit');

            return;
        }

        Page::update($id, [
            'title' => $title,
            'slug' => $slug,
            'status' => $status,
            'published_at' => $status === 'published' ? ($page['published_at'] ?? date('Y-m-d H:i:s')) : $page['published_at'],
        ]);

        SeoMeta::upsertFromRequest('page', $id);
        ActivityLog::record('page.update', 'page', $id, $title);

        Session::flash('success', 'Page updated.');
        $this->redirect('admin/pages/' . $id . '/edit');
    }

    public function duplicate(int $id): void
    {
        $this->requireCsrf();

        $page = Page::find($id);

        if ($page === null) {
            $this->abort(404);

            return;
        }

        $newSlug = $page['slug'] . '-copy-' . substr(bin2hex(random_bytes(3)), 0, 5);

        $newId = Page::create([
            'title' => $page['title'] . ' (Copy)',
            'slug' => $newSlug,
            'template' => $page['template'],
            'status' => 'draft',
        ]);

        foreach (PageSection::forPageAdmin($id) as $section) {
            PageSection::create([
                'page_id' => $newId,
                'component_type' => $section['component_type'],
                'content' => $section['content'],
                'sort_order' => $section['sort_order'],
                'status' => $section['status'],
            ]);
        }

        ActivityLog::record('page.duplicate', 'page', $newId, $page['title']);

        Session::flash('success', 'Page duplicated.');
        $this->redirect('admin/pages');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        $page = Page::find($id);

        if ($page !== null && (bool) $page['is_system']) {
            Session::flash('error', 'System pages cannot be trashed.');
            $this->redirect('admin/pages');

            return;
        }

        Page::delete($id);
        ActivityLog::record('page.trash', 'page', $id);

        Session::flash('success', 'Page moved to trash.');
        $this->redirect('admin/pages');
    }

    public function restore(int $id): void
    {
        $this->requireCsrf();

        Page::restore($id);
        ActivityLog::record('page.restore', 'page', $id);

        Session::flash('success', 'Page restored.');
        $this->redirect('admin/pages');
    }

    private function slugify(string $value): string
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';

        return trim($slug, '-');
    }
}
