<?php

/** @var array $sections */
/** @var array $statistics */
/** @var array $services */
/** @var array $industries */
/** @var array $testimonials */
/** @var array $faqs */
/** @var array $caseStudies */
/** @var array $blogPosts */

use App\Core\View;

foreach ($sections as $section):
    $partial = 'front.home._' . $section['component_type'];
    $content = $section['content'];

    View::partial($partial, [
        'content' => $content,
        'statistics' => $statistics,
        'services' => $services,
        'industries' => $industries,
        'testimonials' => $testimonials,
        'faqs' => $faqs,
        'caseStudies' => $caseStudies,
        'blogPosts' => $blogPosts ?? [],
    ]);
endforeach;
