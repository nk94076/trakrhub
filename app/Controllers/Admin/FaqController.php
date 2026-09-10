<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Faq;

final class FaqController extends Controller
{
    public function index(): void
    {
        $this->view('admin.faqs.index', [
            'pageTitle' => 'FAQs',
            'pageHeading' => 'FAQs',
            'faqs' => Faq::all('`group` ASC, sort_order ASC'),
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $id = Faq::create($this->payload());
        ActivityLog::record('faq.create', 'faq', $id);

        Session::flash('success', 'FAQ added.');
        $this->redirect('admin/faqs');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();

        Faq::update($id, $this->payload());
        ActivityLog::record('faq.update', 'faq', $id);

        Session::flash('success', 'FAQ updated.');
        $this->redirect('admin/faqs');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        Faq::forceDelete($id);
        ActivityLog::record('faq.delete', 'faq', $id);

        Session::flash('success', 'FAQ removed.');
        $this->redirect('admin/faqs');
    }

    private function payload(): array
    {
        return [
            'group' => trim((string) $this->input('group', 'general')) ?: 'general',
            'question' => trim((string) $this->input('question', '')),
            'answer' => trim((string) $this->input('answer', '')),
            'sort_order' => (int) $this->input('sort_order', 0),
            'status' => $this->input('status', 'published') === 'draft' ? 'draft' : 'published',
        ];
    }
}
