<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\CaseStudy;
use App\Models\Industry;

final class CaseStudyController extends Controller
{
    public function index(): void
    {
        $this->view('admin.case-studies.index', [
            'pageTitle' => 'Case Studies',
            'pageHeading' => 'Case Studies',
            'caseStudies' => CaseStudy::all('sort_order ASC'),
            'industries' => Industry::all('title ASC'),
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $payload = $this->payload();

        if ($payload === null) {
            Session::flash('error', 'Title is required, slug must be unique, and metrics must be valid JSON.');
            $this->redirect('admin/case-studies');

            return;
        }

        if (CaseStudy::firstWhere(['slug' => $payload['slug']]) !== null) {
            Session::flash('error', 'That slug is already in use.');
            $this->redirect('admin/case-studies');

            return;
        }

        $id = CaseStudy::create($payload);
        ActivityLog::record('case_study.create', 'case_study', $id, $payload['title']);

        Session::flash('success', 'Case study added.');
        $this->redirect('admin/case-studies');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();

        $payload = $this->payload();

        if ($payload === null) {
            Session::flash('error', 'Title is required, slug must be unique, and metrics must be valid JSON.');
            $this->redirect('admin/case-studies');

            return;
        }

        $existing = CaseStudy::firstWhere(['slug' => $payload['slug']]);

        if ($existing !== null && (int) $existing['id'] !== $id) {
            Session::flash('error', 'That slug is already used by another case study.');
            $this->redirect('admin/case-studies');

            return;
        }

        if ($payload['status'] === 'published') {
            $current = CaseStudy::find($id);
            $payload['published_at'] = $current['published_at'] ?? date('Y-m-d H:i:s');
        }

        CaseStudy::update($id, $payload);
        ActivityLog::record('case_study.update', 'case_study', $id);

        Session::flash('success', 'Case study updated.');
        $this->redirect('admin/case-studies');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        CaseStudy::delete($id);
        ActivityLog::record('case_study.delete', 'case_study', $id);

        Session::flash('success', 'Case study moved to trash.');
        $this->redirect('admin/case-studies');
    }

    private function payload(): ?array
    {
        $title = trim((string) $this->input('title', ''));
        $slug = (string) $this->input('slug', '') ?: $title;
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $slug) ?? '', '-'));
        $metricsRaw = (string) $this->input('metrics', '[]');
        $metrics = json_decode($metricsRaw, true);

        if ($title === '' || $slug === '' || json_last_error() !== JSON_ERROR_NONE || !is_array($metrics)) {
            return null;
        }

        return [
            'title' => $title,
            'slug' => $slug,
            'industry_id' => $this->nullableInt('industry_id'),
            'summary' => trim((string) $this->input('summary', '')),
            'content' => trim((string) $this->input('content', '')),
            'metrics' => json_encode($metrics, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'sort_order' => (int) $this->input('sort_order', 0),
            'status' => $this->input('status', 'published') === 'draft' ? 'draft' : 'published',
        ];
    }
}
