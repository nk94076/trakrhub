<?php

declare(strict_types=1);

namespace App\Controllers\Front;

use App\Core\Controller;
use App\Core\Database;
use App\Core\View;
use App\Models\Setting;

/**
 * XML sitemap (with inline <image:image> entries, satisfying the image
 * sitemap requirement in the same file — Google supports this combined
 * format) and the robots.txt endpoint. A dedicated Google News sitemap is
 * not included: this is a B2B affiliate network, not a news publisher, so
 * a news sitemap has no eligible content to list.
 */
final class SitemapController extends Controller
{
    public function xml(): void
    {
        header('Content-Type: application/xml; charset=utf-8');

        $urls = [];

        foreach (Database::fetchAll('SELECT slug, updated_at FROM pages WHERE status = "published" AND deleted_at IS NULL') as $page) {
            $urls[] = ['loc' => View::url($page['slug'] === 'home' ? '/' : $page['slug']), 'lastmod' => $page['updated_at']];
        }

        foreach (Database::fetchAll('SELECT slug, updated_at FROM services WHERE status = "published" AND deleted_at IS NULL') as $service) {
            $urls[] = ['loc' => View::url('services/' . $service['slug']), 'lastmod' => $service['updated_at']];
        }

        foreach (Database::fetchAll('SELECT slug, updated_at FROM case_studies WHERE status = "published" AND deleted_at IS NULL') as $case) {
            $urls[] = ['loc' => View::url('case-studies/' . $case['slug']), 'lastmod' => $case['updated_at']];
        }

        foreach (Database::fetchAll(
            'SELECT p.slug, p.updated_at, m.path AS image_path
             FROM blog_posts p
             LEFT JOIN media m ON m.id = p.featured_image_id
             WHERE p.status = "published" AND p.published_at <= NOW() AND p.deleted_at IS NULL'
        ) as $post) {
            $urls[] = [
                'loc' => View::url('blog/' . $post['slug']),
                'lastmod' => $post['updated_at'],
                'image' => $post['image_path'] ? View::url(ltrim($post['image_path'], '/')) : null,
            ];
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($urls as $url) {
            echo '  <url>' . "\n";
            echo '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') . '</loc>' . "\n";

            if (!empty($url['lastmod'])) {
                echo '    <lastmod>' . date('c', strtotime((string) $url['lastmod'])) . '</lastmod>' . "\n";
            }

            if (!empty($url['image'])) {
                echo '    <image:image><image:loc>' . htmlspecialchars($url['image'], ENT_XML1, 'UTF-8') . '</image:loc></image:image>' . "\n";
            }

            echo '  </url>' . "\n";
        }

        echo '</urlset>';
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');

        $custom = Setting::get('seo', 'robots_txt', '');

        if (trim((string) $custom) !== '') {
            echo $custom;

            return;
        }

        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin/\n";
        echo 'Sitemap: ' . View::url('sitemap.xml') . "\n";
    }
}
