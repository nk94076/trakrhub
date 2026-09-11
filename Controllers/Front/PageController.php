<?php

declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\Front\Concerns\RendersFrontPage;
use App\Core\Controller;

/**
 * Renders any CMS page purely from its slug + page_sections content —
 * About, Publishers, Advertisers, Technology, Career, and the legal pages
 * all go through this one controller. Nothing here is page-specific.
 */
final class PageController extends Controller
{
    use RendersFrontPage;

    public function show(string $slug): void
    {
        $this->renderPageBySlug($slug);
    }
}
