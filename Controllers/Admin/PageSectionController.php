<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\PageSection;

final class PageSectionController extends Controller
{
    /** Known homepage component types, offered as quick-add presets. New/custom types can still be typed in freely. */
    private const KNOWN_TYPES = [
        'hero', 'trusted_by', 'statistics', 'services_grid', 'campaign_types', 'why_choose_us',
        'how_it_works', 'publisher_benefits', 'advertiser_benefits', 'industries', 'technology',
        'process', 'testimonials', 'latest_blogs', 'faq', 'contact_cta', 'newsletter', 'custom_html',
    ];

    public function store(int $pageId): void
    {
        $this->requireCsrf();

        $componentType = trim((string) $this->input('component_type', ''));
        $contentRaw = (string) $this->input('content', '{}');

        $decoded = json_decode($contentRaw, true);

        if (!$this->isValidComponentType($componentType) || json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            Session::flash('error', 'Section type must be lowercase letters, numbers and underscores, and content must be valid JSON.');
            $this->redirect('admin/pages/' . $pageId . '/edit');

            return;
        }

        $id = PageSection::create([
            'page_id' => $pageId,
            'component_type' => $componentType,
            'content' => json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'sort_order' => PageSection::nextSortOrder($pageId),
            'status' => 'draft',
        ]);

        ActivityLog::record('page_section.create', 'page_section', $id, $componentType);

        Session::flash('success', 'Section added as draft. Publish it once you are happy with the content.');
        $this->redirect('admin/pages/' . $pageId . '/edit');
    }

    public function update(int $pageId, int $id): void
    {
        $this->requireCsrf();

        $componentType = trim((string) $this->input('component_type', ''));
        $contentRaw = (string) $this->input('content', '{}');

        $decoded = json_decode($contentRaw, true);

        if (!$this->isValidComponentType($componentType) || json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            Session::flash('error', 'Section type must be lowercase letters, numbers and underscores, and content must be valid JSON.');
            $this->redirect('admin/pages/' . $pageId . '/edit');

            return;
        }

        PageSection::update($id, [
            'component_type' => $componentType,
            'content' => json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);

        ActivityLog::record('page_section.update', 'page_section', $id, $componentType);

        Session::flash('success', 'Section updated.');
        $this->redirect('admin/pages/' . $pageId . '/edit');
    }

    public function togglePublish(int $pageId, int $id): void
    {
        $this->requireCsrf();

        $section = PageSection::find($id);

        if ($section !== null) {
            PageSection::update($id, ['status' => $section['status'] === 'published' ? 'draft' : 'published']);
            ActivityLog::record('page_section.toggle', 'page_section', $id);
        }

        Session::flash('success', 'Section status updated.');
        $this->redirect('admin/pages/' . $pageId . '/edit');
    }

    public function delete(int $pageId, int $id): void
    {
        $this->requireCsrf();

        PageSection::forceDelete($id);
        ActivityLog::record('page_section.delete', 'page_section', $id);

        Session::flash('success', 'Section deleted.');
        $this->redirect('admin/pages/' . $pageId . '/edit');
    }

    public function reorder(int $pageId): void
    {
        $this->requireCsrf();

        PageSection::reorder((array) ($this->input('order', []) ?? []));

        $this->json(['success' => true]);
    }

    /**
     * component_type is used to resolve a view partial path (front.home._{type}),
     * so it's restricted to a safe character set rather than relying on
     * View::resolvePath()'s incidental dot-to-slash handling to contain it.
     * New/custom types beyond KNOWN_TYPES are still allowed — this only
     * rejects unsafe characters, not unfamiliar names.
     */
    private function isValidComponentType(string $componentType): bool
    {
        return $componentType !== '' && preg_match('/^[a-z0-9_]+$/', $componentType) === 1;
    }
}
