<?php

declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\Front\Concerns\RendersFrontPage;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Seo;
use App\Core\View;
use App\Models\Menu;

/**
 * Site-wide search across blog posts, services and pages — also the
 * target referenced by the WebSite SearchAction schema on every page.
 */
final class SearchController extends Controller
{
    use RendersFrontPage;

    public function index(): void
    {
        $query = trim((string) $this->input('q', ''));

        $posts = [];
        $services = [];
        $pages = [];

        if ($query !== '') {
            $posts = Database::fetchAll(
                "SELECT title, slug, excerpt FROM blog_posts
                 WHERE status = 'published' AND published_at <= NOW()
                   AND MATCH(title, excerpt) AGAINST (:query IN NATURAL LANGUAGE MODE)
                 LIMIT 10",
                ['query' => $query]
            );

            $services = Database::fetchAll(
                'SELECT title, slug, short_description FROM services
                 WHERE status = "published" AND (title LIKE :like1 OR short_description LIKE :like2)
                 LIMIT 10',
                ['like1' => '%' . $query . '%', 'like2' => '%' . $query . '%']
            );

            $pages = Database::fetchAll(
                'SELECT title, slug FROM pages
                 WHERE status = "published" AND deleted_at IS NULL AND title LIKE :like
                 LIMIT 10',
                ['like' => '%' . $query . '%']
            );
        }

        $this->view('front.search.index', [
            'pageTitle' => $query !== '' ? 'Search results for "' . $query . '"' : 'Search',
            'query' => $query,
            'posts' => $posts,
            'services' => $services,
            'pages' => $pages,
            'headerMenu' => Menu::itemsForLocation('header'),
            'footerMenu' => Menu::itemsForLocation('footer'),
            'seo' => $this->seoFromRow(null, [
                Seo::breadcrumbSchema([
                    ['name' => 'Home', 'url' => View::url('/')],
                    ['name' => 'Search', 'url' => View::url('search')],
                ]),
            ]),
        ], 'front.layouts.main');
    }
}
