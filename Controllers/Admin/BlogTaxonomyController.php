<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Author;
use App\Models\BlogCategory;
use App\Models\BlogTag;

/**
 * Lightweight admin for the blog's supporting taxonomies — categories,
 * tags and authors. Each is a simple name(+slug) resource, so one
 * controller handles all three rather than triplicating near-identical CRUD.
 */
final class BlogTaxonomyController extends Controller
{
    public function index(): void
    {
        $this->view('admin.blog.taxonomy', [
            'pageTitle' => 'Blog Categories, Tags & Authors',
            'pageHeading' => 'Blog Categories, Tags & Authors',
            'categories' => BlogCategory::all('name ASC'),
            'tags' => BlogTag::all('name ASC'),
            'authors' => Author::all('name ASC'),
        ], 'admin.layouts.app');
    }

    public function createCategory(): void
    {
        $this->requireCsrf();
        $this->createNamedSlugResource(BlogCategory::class, 'category');
    }

    public function deleteCategory(int $id): void
    {
        $this->requireCsrf();
        BlogCategory::forceDelete($id);
        ActivityLog::record('blog_category.delete', 'blog_category', $id);
        Session::flash('success', 'Category deleted.');
        $this->redirect('admin/blog/taxonomy');
    }

    public function createTag(): void
    {
        $this->requireCsrf();
        $this->createNamedSlugResource(BlogTag::class, 'tag');
    }

    public function deleteTag(int $id): void
    {
        $this->requireCsrf();
        BlogTag::forceDelete($id);
        ActivityLog::record('blog_tag.delete', 'blog_tag', $id);
        Session::flash('success', 'Tag deleted.');
        $this->redirect('admin/blog/taxonomy');
    }

    public function createAuthor(): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));

        if ($name === '') {
            Session::flash('error', 'Author name is required.');
            $this->redirect('admin/blog/taxonomy');

            return;
        }

        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name) ?? '', '-'));

        $id = Author::create([
            'name' => $name,
            'slug' => $slug,
            'bio' => trim((string) $this->input('bio', '')),
            'job_title' => trim((string) $this->input('job_title', '')),
        ]);

        ActivityLog::record('author.create', 'author', $id, $name);
        Session::flash('success', 'Author added.');
        $this->redirect('admin/blog/taxonomy');
    }

    public function deleteAuthor(int $id): void
    {
        $this->requireCsrf();
        Author::forceDelete($id);
        ActivityLog::record('author.delete', 'author', $id);
        Session::flash('success', 'Author deleted.');
        $this->redirect('admin/blog/taxonomy');
    }

    /** @param class-string<BlogCategory|BlogTag> $modelClass */
    private function createNamedSlugResource(string $modelClass, string $label): void
    {
        $name = trim((string) $this->input('name', ''));

        if ($name === '') {
            Session::flash('error', ucfirst($label) . ' name is required.');
            $this->redirect('admin/blog/taxonomy');

            return;
        }

        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name) ?? '', '-'));

        if ($modelClass::firstWhere(['slug' => $slug]) !== null) {
            Session::flash('error', 'That ' . $label . ' already exists.');
            $this->redirect('admin/blog/taxonomy');

            return;
        }

        $id = $modelClass::create(['name' => $name, 'slug' => $slug]);
        ActivityLog::record($label . '.create', $label, $id, $name);

        Session::flash('success', ucfirst($label) . ' added.');
        $this->redirect('admin/blog/taxonomy');
    }
}
