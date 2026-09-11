<?php

use App\Core\View;

/** @var array $caseStudies */
?>
<?php View::partial('partials.page-banner', [
  'title' => 'Case Studies',
  'subtitle' => 'Real results from advertisers and publishers on the Techslay network.',
]); ?>

<section class="py-16">
  <div class="max-w-6xl mx-auto px-6">
    <?php if ($caseStudies === []): ?>
      <p class="text-center text-slate-400">Case studies will be published here soon.</p>
    <?php else: ?>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($caseStudies as $case): ?>
          <a href="<?= View::url('case-studies/' . $case['slug']) ?>" class="rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-lg transition p-7 block">
            <h3 class="font-semibold text-slate-900"><?= View::e($case['title']) ?></h3>
            <p class="mt-2 text-sm text-slate-500"><?= View::e($case['summary'] ?? '') ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
