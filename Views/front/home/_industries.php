<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $content */
/** @var array $industries */
?>
<?php if ($industries !== []): ?>
<section class="py-20 bg-slate-50/60">
  <div class="max-w-6xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-14">
      <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?= View::e($content['title'] ?? '') ?></h2>
      <?php if (!empty($content['subtitle'])): ?>
        <p class="mt-3 text-slate-600"><?= View::e($content['subtitle']) ?></p>
      <?php endif; ?>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
      <?php foreach ($industries as $industry): ?>
        <div class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-brand-200 transition-all duration-200 p-6 text-center">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-50 to-accent-50 text-brand-600 flex items-center justify-center mx-auto mb-4 group-hover:bg-gradient-to-br group-hover:from-brand-500 group-hover:to-accent-500 group-hover:text-white transition-colors duration-200">
            <?= Icon::render($industry['icon'] ?? null, 'w-6 h-6') ?>
          </div>
          <div class="text-sm font-semibold text-slate-800"><?= View::e($industry['title']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
