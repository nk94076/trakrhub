<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $services */
?>
<?php View::partial('partials.page-banner', [
  'title' => 'Our Services',
  'subtitle' => 'End-to-end performance marketing infrastructure for advertisers and publishers.',
]); ?>

<section class="py-16">
  <div class="max-w-6xl mx-auto px-6">
    <?php if ($services === []): ?>
      <p class="text-center text-slate-400">Services will be listed here soon.</p>
    <?php else: ?>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($services as $service): ?>
          <a href="<?= View::url('services/' . $service['slug']) ?>"
             class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 p-7">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-500 to-accent-500 text-white flex items-center justify-center mb-5">
              <?= Icon::render($service['icon'] ?? null, 'w-6 h-6') ?>
            </div>
            <h3 class="font-semibold text-lg text-slate-900 group-hover:text-brand-600 transition"><?= View::e($service['title']) ?></h3>
            <p class="mt-2 text-sm text-slate-500"><?= View::e($service['short_description']) ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
