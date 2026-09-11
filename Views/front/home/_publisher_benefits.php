<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $content */
$items = $content['items'] ?? [];
?>
<?php if ($items !== []): ?>
<section class="py-20 bg-slate-50/60">
  <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
    <div>
      <span class="inline-block rounded-full bg-white border border-brand-100 px-4 py-1.5 text-xs font-semibold text-brand-600 shadow-sm mb-5">For Publishers</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?= View::e($content['title'] ?? '') ?></h2>
      <a href="<?= View::url('publishers') ?>" class="mt-6 inline-flex items-center rounded-full bg-slate-900 text-white text-sm font-semibold px-6 py-3 hover:bg-slate-800 transition">Become a Publisher</a>

      <div class="mt-10 relative max-w-xs">
        <div class="absolute -inset-4 bg-gradient-to-br from-brand-200/40 to-accent-200/30 blur-2xl rounded-full"></div>
        <div class="relative rounded-2xl bg-white border border-slate-100 shadow-lg p-5 flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-500 to-accent-500 text-white flex items-center justify-center shadow-lg shadow-brand-500/30 flex-shrink-0">
            <?= Icon::render('currency-dollar', 'w-6 h-6') ?>
          </div>
          <div>
            <div class="text-2xl font-extrabold text-slate-900"><?= View::e($content['highlight_value'] ?? '$500K+') ?></div>
            <div class="text-xs text-slate-500"><?= View::e($content['highlight_label'] ?? 'Paid to publishers monthly') ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="space-y-4">
      <?php foreach ($items as $item): ?>
        <div class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md hover:border-brand-200 transition-all duration-200 p-6 flex items-start gap-4">
          <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
            <?= Icon::render($item['icon'] ?? null, 'w-5 h-5') ?>
          </div>
          <div>
            <h3 class="font-semibold text-slate-900"><?= View::e($item['title'] ?? '') ?></h3>
            <p class="mt-1 text-sm text-slate-500"><?= View::e($item['description'] ?? '') ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
