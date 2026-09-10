<?php

declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\Front\Concerns\RendersFrontPage;
use App\Core\Controller;
use App\Core\Seo;
use App\Core\View;
use App\Models\CaseStudy;
use App\Models\Menu;
use App\Models\SeoMeta;

final class CaseStudyController extends Controller
{
    use RendersFrontPage;

    public function index(): void
    {
        $this->view('front.case-studies.index', [
            'pageTitle' => 'Case Studies',
            'caseStudies' => CaseStudy::published(),
            'headerMenu' => Menu::itemsForLocation('header'),
            'footerMenu' => Menu::itemsForLocation('footer'),
            'seo' => $this->seoFromRow(null, [
                Seo::breadcrumbSchema([
                    ['name' => 'Home', 'url' => View::url('/')],
                    ['name' => 'Case Studies', 'url' => View::url('case-studies')],
                ]),
            ]),
        ], 'front.layouts.main');
    }

    public function show(string $slug): void
    {
        $case = CaseStudy::findBySlug($slug);

        if ($case === null) {
            $this->notFoundOr404View('/case-studies/' . $slug, 'Case Study Not Found');

            return;
        }

        $seoRow = SeoMeta::forEntity('case_study', (int) $case['id']);
        $schemas = [
            Seo::breadcrumbSchema([
                ['name' => 'Home', 'url' => View::url('/')],
                ['name' => 'Case Studies', 'url' => View::url('case-studies')],
                ['name' => $case['title'], 'url' => View::url('case-studies/' . $case['slug'])],
            ]),
        ];

        $this->view('front.case-studies.show', [
            'pageTitle' => $case['title'],
            'metaDescription' => $case['summary'],
            'case' => $case,
            'metrics' => json_decode((string) ($case['metrics'] ?? '[]'), true) ?: [],
            'headerMenu' => Menu::itemsForLocation('header'),
            'footerMenu' => Menu::itemsForLocation('footer'),
            'seo' => $this->seoFromRow($seoRow, $schemas),
        ], 'front.layouts.main');
    }
}
