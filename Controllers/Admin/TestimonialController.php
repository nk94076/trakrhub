<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Testimonial;

final class TestimonialController extends Controller
{
    public function index(): void
    {
        $this->view('admin.testimonials.index', [
            'pageTitle' => 'Testimonials',
            'pageHeading' => 'Testimonials',
            'testimonials' => Testimonial::all('sort_order ASC'),
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $id = Testimonial::create($this->payload());
        ActivityLog::record('testimonial.create', 'testimonial', $id);

        Session::flash('success', 'Testimonial added.');
        $this->redirect('admin/testimonials');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();

        Testimonial::update($id, $this->payload());
        ActivityLog::record('testimonial.update', 'testimonial', $id);

        Session::flash('success', 'Testimonial updated.');
        $this->redirect('admin/testimonials');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        Testimonial::forceDelete($id);
        ActivityLog::record('testimonial.delete', 'testimonial', $id);

        Session::flash('success', 'Testimonial removed.');
        $this->redirect('admin/testimonials');
    }

    private function payload(): array
    {
        return [
            'name' => trim((string) $this->input('name', '')),
            'designation' => trim((string) $this->input('designation', '')),
            'company' => trim((string) $this->input('company', '')),
            'rating' => max(1, min(5, (int) $this->input('rating', 5))),
            'content' => trim((string) $this->input('content', '')),
            'sort_order' => (int) $this->input('sort_order', 0),
            'status' => $this->input('status', 'published') === 'draft' ? 'draft' : 'published',
        ];
    }
}
