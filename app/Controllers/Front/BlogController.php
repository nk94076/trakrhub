<?php

declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\Front\Concerns\RendersFrontPage;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Seo;
use App\Core\View;
use App\Models\Menu;
use App\Models\SeoMeta;

final class BlogController extends Controller
{
    use RendersFrontPage;

    private const PER_PAGE = 9;

    public function index(): void
    {
        $page = max(1, (int) $this->input('page', 1));
        $categorySlug = (string) $this->input('category', '');
        $offset = ($page - 1) * self::PER_PAGE;

        $where = "WHERE p.status = 'published' AND p.published_at <= NOW()";
        $params = [];

        if ($categorySlug !== '') {
            $where .= ' AND c.slug = :category';
            $params['category'] = $categorySlug;
        }

        $total = (int) (Database::fetchOne(
            "SELECT COUNT(*) AS total FROM blog_posts p LEFT JOIN blog_categories c ON c.id = p.category_id {$where}",
            $params
        )['total'] ?? 0);

        $posts = Database::fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug, a.name AS author_name
             FROM blog_posts p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             LEFT JOIN authors a ON a.id = p.author_id
             {$where}
             ORDER BY p.published_at DESC
             LIMIT " . self::PER_PAGE . ' OFFSET ' . $offset,
            $params
        );

        $this->view('front.blog.index', [
            'pageTitle' => 'Blog',
            'posts' => $posts,
            'categories' => Database::fetchAll('SELECT * FROM blog_categories ORDER BY name ASC'),
            'activeCategory' => $categorySlug,
            'currentPage' => $page,
            'lastPage' => (int) max(1, ceil($total / self::PER_PAGE)),
            'headerMenu' => Menu::itemsForLocation('header'),
            'footerMenu' => Menu::itemsForLocation('footer'),
            'seo' => $this->seoFromRow(null, [
                Seo::breadcrumbSchema([
                    ['name' => 'Home', 'url' => View::url('/')],
                    ['name' => 'Blog', 'url' => View::url('blog')],
                ]),
            ]),
        ], 'front.layouts.main');
    }

    public function show(string $slug): void
    {
        $post = Database::fetchOne(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    a.name AS author_name, a.bio AS author_bio, a.job_title AS author_job_title,
                    a.linkedin_url AS author_linkedin, a.twitter_url AS author_twitter
             FROM blog_posts p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             LEFT JOIN authors a ON a.id = p.author_id
             WHERE p.slug = :slug AND p.status = 'published' AND p.published_at <= NOW()",
            ['slug' => $slug]
        );

        if ($post === null) {
            $this->notFoundOr404View('/blog/' . $slug, 'Post Not Found');

            return;
        }

        Database::query('UPDATE blog_posts SET views = views + 1 WHERE id = :id', ['id' => $post['id']]);

        $related = Database::fetchAll(
            'SELECT p.*, c.name AS category_name FROM blog_posts p
             JOIN blog_related_posts r ON r.related_post_id = p.id
             LEFT JOIN blog_categories c ON c.id = p.category_id
             WHERE r.blog_post_id = :id AND p.status = "published"
             LIMIT 3',
            ['id' => $post['id']]
        );

        $tags = Database::fetchAll(
            'SELECT t.* FROM blog_tags t
             JOIN blog_post_tag pt ON pt.blog_tag_id = t.id
             WHERE pt.blog_post_id = :id',
            ['id' => $post['id']]
        );

        $seoRow = SeoMeta::forEntity('blog_post', (int) $post['id']);
        $schemas = [
            Seo::articleSchema($post),
            Seo::breadcrumbSchema([
                ['name' => 'Home', 'url' => View::url('/')],
                ['name' => 'Blog', 'url' => View::url('blog')],
                ['name' => $post['title'], 'url' => View::url('blog/' . $post['slug'])],
            ]),
        ];

        $this->view('front.blog.show', [
            'pageTitle' => $post['title'],
            'metaDescription' => $post['excerpt'],
            'post' => $post,
            'tableOfContents' => json_decode((string) ($post['table_of_contents'] ?? '[]'), true) ?: [],
            'related' => $related,
            'tags' => $tags,
            'headerMenu' => Menu::itemsForLocation('header'),
            'footerMenu' => Menu::itemsForLocation('footer'),
            'seo' => $this->seoFromRow($seoRow, $schemas),
        ], 'front.layouts.main');
    }
}
