<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $content */
$items = $content['items'] ?? [];
?>
<?php if ($items !== []): ?>
<section class="py-20 bg-slate-50/60">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-14"><?= View::e($content['title'] ?? '') ?></h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($items as $item): ?>
        <div class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-brand-200 transition-all duration-200 p-7">
          <div class="w-11 h-11 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mb-5 group-hover:bg-gradient-to-br group-hover:from-brand-500 group-hover:to-accent-500 group-hover:text-white transition-colors duration-200">
            <?= Icon::render($item['icon'] ?? null, 'w-6 h-6') ?>
          </div>
          <h3 class="font-semibold text-slate-900"><?= View::e($item['title'] ?? '') ?></h3>
          <p class="mt-2 text-sm text-slate-500"><?= View::e($item['description'] ?? '') ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
