<?php

declare(strict_types=1);

namespace App\Controllers\Front\Concerns;

use App\Core\Seo;
use App\Core\View;
use App\Models\BlogPost;
use App\Models\CaseStudy;
use App\Models\Faq;
use App\Models\Industry;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Service;
use App\Models\SeoMeta;
use App\Models\Statistic;
use App\Models\Testimonial;

/**
 * Shared renderer for any page built from page_sections. Every generic
 * section partial (statistics, services_grid, testimonials, faq, ...) can
 * appear on any page, so the same supporting data is always made available
 * regardless of which page/slug is being rendered.
 */
trait RendersFrontPage
{
    protected function renderPageBySlug(string $slug, array $overrides = []): void
    {
        $page = Page::findBySlug($slug);

        if ($page === null) {
            $this->notFoundOr404View('/' . $slug, 'Page Not Found');

            return;
        }

        $sections = Page::sectionsFor((int) $page['id']);

        $data = array_merge([
            'pageTitle' => $page['title'],
            'sections' => $sections,
            'statistics' => Statistic::published(),
            'services' => Service::published(),
            'industries' => Industry::published(),
            'testimonials' => Testimonial::published(),
            'faqs' => Faq::byGroup('general'),
            'caseStudies' => CaseStudy::published(),
            'blogPosts' => BlogPost::recentPublished(3),
            'headerMenu' => Menu::itemsForLocation('header'),
            'footerMenu' => Menu::itemsForLocation('footer'),
            'seo' => $this->buildPageSeo($page, $sections),
        ], $overrides);

        $this->view('front.pages.dynamic', $data, 'front.layouts.main');
    }

    private function buildPageSeo(array $page, array $sections): array
    {
        $seoRow = SeoMeta::forEntity('page', (int) $page['id']);

        $schemas = [];

        if ($page['slug'] !== 'home') {
            $schemas[] = Seo::breadcrumbSchema([
                ['name' => 'Home', 'url' => View::url('/')],
                ['name' => $page['title'], 'url' => View::url($page['slug'])],
            ]);
        }

        foreach ($sections as $section) {
            if ($section['component_type'] === 'faq') {
                $faqs = Faq::byGroup((string) ($section['content']['group'] ?? 'general'));

                if ($faqs !== []) {
                    $schemas[] = Seo::faqSchema($faqs);
                }

                break;
            }
        }

        return $this->seoFromRow($seoRow, $schemas);
    }

    protected function seoFromRow(?array $seoRow, array $schemas = []): array
    {
        if ($seoRow === null) {
            return $schemas === [] ? [] : ['schemas' => $schemas];
        }

        return array_filter([
            'title' => $seoRow['seo_title'] ?: null,
            'description' => $seoRow['meta_description'] ?: null,
            'keywords' => $seoRow['meta_keywords'] ?: null,
            'canonical' => $seoRow['canonical_url'] ?: null,
            'robots_index' => $seoRow['robots_index'] ?? null,
            'robots_follow' => $seoRow['robots_follow'] ?? null,
            'og_title' => $seoRow['og_title'] ?: null,
            'og_description' => $seoRow['og_description'] ?: null,
            'twitter_card' => $seoRow['twitter_card'] ?: null,
            'schemas' => $schemas,
        ], static fn ($value) => $value !== null && $value !== []);
    }
}
