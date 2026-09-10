<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Author;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\SeoMeta;

final class BlogPostController extends Controller
{
    public function index(): void
    {
        $showTrash = $this->input('trash') === '1';
        $search = (string) $this->input('q', '');

        $sql = 'SELECT p.*, c.name AS category_name FROM blog_posts p LEFT JOIN blog_categories c ON c.id = p.category_id WHERE 1=1';
        $params = [];

        $sql .= $showTrash ? ' AND p.deleted_at IS NOT NULL' : ' AND p.deleted_at IS NULL';

        if ($search !== '') {
            $sql .= ' AND p.title LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY p.created_at DESC';

        $this->view('admin.blog.index', [
            'pageTitle' => 'Blog Posts',
            'pageHeading' => 'Blog Posts',
            'posts' => Database::fetchAll($sql, $params),
            'showTrash' => $showTrash,
            'search' => $search,
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->view('admin.blog.form', [
            'pageTitle' => 'New Blog Post',
            'pageHeading' => 'New Blog Post',
            'post' => null,
            'categories' => BlogCategory::all('name ASC'),
            'authors' => Author::all('name ASC'),
            'postTags' => [],
            'seo' => null,
        ], 'admin.layouts.app');
    }

    public function store(): void
    {
        $this->requireCsrf();
        $this->save(null);
    }

    public function edit(int $id): void
    {
        $post = BlogPost::find($id);

        if ($post === null) {
            $this->abort(404, 'Post not found.');

            return;
        }

        $tagRows = Database::fetchAll(
            'SELECT t.id, t.name FROM blog_tags t JOIN blog_post_tag pt ON pt.blog_tag_id = t.id WHERE pt.blog_post_id = :id',
            ['id' => $id]
        );

        $this->view('admin.blog.form', [
            'pageTitle' => 'Edit: ' . $post['title'],
            'pageHeading' => 'Edit Blog Post',
            'post' => $post,
            'categories' => BlogCategory::all('name ASC'),
            'authors' => Author::all('name ASC'),
            'postTags' => array_column($tagRows, 'name'),
            'seo' => SeoMeta::forEntity('blog_post', $id),
        ], 'admin.layouts.app');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();
        $this->save($id);
    }

    private function save(?int $id): void
    {
        $title = trim((string) $this->input('title', ''));
        $slug = (string) $this->input('slug', '') ?: $title;
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $slug) ?? '', '-'));
        $content = (string) $this->input('content', '');
        $status = in_array($this->input('status'), ['published', 'draft', 'scheduled'], true)
            ? $this->input('status')
            : 'draft';

        if ($title === '' || $slug === '' || $content === '') {
            Session::flash('error', 'Title, slug and content are required.');
            $this->redirect($id ? 'admin/blog/' . $id . '/edit' : 'admin/blog/create');

            return;
        }

        $existing = BlogPost::firstWhere(['slug' => $slug]);

        if ($existing !== null && (int) $existing['id'] !== $id) {
            Session::flash('error', 'That slug is already in use by another post.');
            $this->redirect($id ? 'admin/blog/' . $id . '/edit' : 'admin/blog/create');

            return;
        }

        $content = $this->injectHeadingIds($content);
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = max(1, (int) ceil($wordCount / 200));
        $tableOfContents = $this->extractToc($content);

        $scheduledAt = trim((string) $this->input('scheduled_at', ''));
        $publishedAt = null;

        if ($status === 'published') {
            $current = $id !== null ? BlogPost::find($id) : null;
            $publishedAt = $current['published_at'] ?? date('Y-m-d H:i:s');
        } elseif ($status === 'scheduled' && $scheduledAt !== '') {
            $publishedAt = $scheduledAt;
        }

        $payload = [
            'category_id' => $this->nullableInt('category_id'),
            'author_id' => $this->nullableInt('author_id'),
            'title' => $title,
            'slug' => $slug,
            'excerpt' => trim((string) $this->input('excerpt', '')),
            'featured_image' => trim((string) $this->input('featured_image', '')) ?: null,
            'content' => $content,
            'table_of_contents' => json_encode($tableOfContents, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'reading_time_minutes' => $readingTime,
            'status' => $status,
            'scheduled_at' => $status === 'scheduled' && $scheduledAt !== '' ? $scheduledAt : null,
            'published_at' => $publishedAt,
        ];

        if ($id === null) {
            $id = BlogPost::create($payload);
            ActivityLog::record('blog_post.create', 'blog_post', $id, $title);
        } else {
            BlogPost::update($id, $payload);
            ActivityLog::record('blog_post.update', 'blog_post', $id, $title);
        }

        $this->syncTags($id, (string) $this->input('tags', ''));
        SeoMeta::upsertFromRequest('blog_post', $id);

        Session::flash('success', 'Post saved.');
        $this->redirect('admin/blog/' . $id . '/edit');
    }

    private function syncTags(int $postId, string $tagsCsv): void
    {
        Database::query('DELETE FROM blog_post_tag WHERE blog_post_id = :id', ['id' => $postId]);

        $names = array_filter(array_map('trim', explode(',', $tagsCsv)));

        foreach ($names as $name) {
            $tagId = BlogTag::findOrCreateByName($name);
            Database::query(
                'INSERT IGNORE INTO blog_post_tag (blog_post_id, blog_tag_id) VALUES (:post_id, :tag_id)',
                ['post_id' => $postId, 'tag_id' => $tagId]
            );
        }
    }

    /** Pulls <h2>/<h3> headings with id attributes out of the content HTML to build a simple TOC. */
    /**
     * The WYSIWYG editor has no way for an admin to type id="..." onto a
     * heading by hand, so auto-slugify every <h2>/<h3> that doesn't already
     * have one — extractToc() (and the front-end anchor links it powers)
     * depends entirely on that id attribute being present.
     */
    private function injectHeadingIds(string $content): string
    {
        $seen = [];

        return (string) preg_replace_callback(
            '/<h([23])([^>]*)>(.*?)<\/h\1>/is',
            static function (array $m) use (&$seen): string {
                [, $level, $attrs, $inner] = $m;

                if (preg_match('/\bid\s*=/i', $attrs)) {
                    return $m[0];
                }

                $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', strip_tags($inner)), '-'));
                $slug = $slug !== '' ? $slug : 'section';

                $base = $slug;
                $i = 2;

                while (isset($seen[$slug])) {
                    $slug = $base . '-' . $i++;
                }

                $seen[$slug] = true;

                return "<h{$level}{$attrs} id=\"{$slug}\">{$inner}</h{$level}>";
            },
            $content
        );
    }

    private function extractToc(string $content): array
    {
        if (!preg_match_all('/<h([23])[^>]*id="([^"]+)"[^>]*>(.*?)<\/h\1>/i', $content, $matches, PREG_SET_ORDER)) {
            return [];
        }

        return array_map(
            static fn ($m) => ['anchor' => $m[2], 'label' => trim(strip_tags($m[3]))],
            $matches
        );
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        BlogPost::delete($id);
        ActivityLog::record('blog_post.trash', 'blog_post', $id);

        Session::flash('success', 'Post moved to trash.');
        $this->redirect('admin/blog');
    }

    public function restore(int $id): void
    {
        $this->requireCsrf();

        BlogPost::restore($id);
        ActivityLog::record('blog_post.restore', 'blog_post', $id);

        Session::flash('success', 'Post restored.');
        $this->redirect('admin/blog');
    }
}
