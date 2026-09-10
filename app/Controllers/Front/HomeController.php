<?php

declare(strict_types=1);

namespace App\Controllers\Front;

use App\Controllers\Front\Concerns\RendersFrontPage;
use App\Core\Controller;

final class HomeController extends Controller
{
    use RendersFrontPage;

    public function index(): void
    {
        $this->renderPageBySlug('home', [
            'metaDescription' => 'ClickNet connects advertisers and publishers through transparent CPS, CPL and CPI performance marketing campaigns.',
        ]);
    }
}
