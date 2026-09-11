<?php

declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\Front\Concerns\RendersFrontPage;
use App\Core\Controller;
use App\Core\Seo;
use App\Core\View;
use App\Models\Faq;
use App\Models\Menu;
use App\Models\Service;
use App\Models\SeoMeta;

final class ServiceController extends Controller
{
    use RendersFrontPage;

    public function index(): void
    {
        $this->view('front.services.index', [
            'pageTitle' => 'Our Services',
            'services' => Service::published(),
            'headerMenu' => Menu::itemsForLocation('header'),
            'footerMenu' => Menu::itemsForLocation('footer'),
            'seo' => $this->seoFromRow(null, [
                Seo::breadcrumbSchema([
                    ['name' => 'Home', 'url' => View::url('/')],
                    ['name' => 'Services', 'url' => View::url('services')],
                ]),
            ]),
        ], 'front.layouts.main');
    }

    public function show(string $slug): void
    {
        $service = Service::findBySlug($slug);

        if ($service === null) {
            $this->notFoundOr404View('/services/' . $slug, 'Service Not Found');

            return;
        }

        $seoRow = SeoMeta::forEntity('service', (int) $service['id']);

        // Per-service FAQs use a group named after the service slug so admins
        // can manage them from the existing FAQ screen with no new UI; sites
        // without any yet fall back to the general FAQ group.
        $faqs = Faq::byGroup($service['slug']);
        $faqs = $faqs !== [] ? $faqs : Faq::byGroup('general');

        $schemas = [
            Seo::breadcrumbSchema([
                ['name' => 'Home', 'url' => View::url('/')],
                ['name' => 'Services', 'url' => View::url('services')],
                ['name' => $service['title'], 'url' => View::url('services/' . $service['slug'])],
            ]),
        ];

        if ($faqs !== []) {
            $schemas[] = Seo::faqSchema($faqs);
        }

        $this->view('front.services.show', [
            'pageTitle' => $service['title'],
            'metaDescription' => $service['short_description'],
            'service' => $service,
            'stats' => json_decode((string) ($service['stats'] ?? '[]'), true) ?: [],
            'highlights' => json_decode((string) ($service['highlights'] ?? '[]'), true) ?: [],
            'processSteps' => json_decode((string) ($service['process_steps'] ?? '[]'), true) ?: [],
            'faqs' => $faqs,
            'otherServices' => array_slice(array_filter(Service::published(), static fn ($s) => $s['id'] !== $service['id']), 0, 3),
            'headerMenu' => Menu::itemsForLocation('header'),
            'footerMenu' => Menu::itemsForLocation('footer'),
            'seo' => $this->seoFromRow($seoRow, $schemas),
        ], 'front.layouts.main');
    }
}
