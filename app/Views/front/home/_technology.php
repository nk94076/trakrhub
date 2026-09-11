<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $content */
$items = $content['items'] ?? [];
?>
<?php if ($items !== []): ?>
<section class="py-20">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-14"><?= View::e($content['title'] ?? '') ?></h2>
    <div class="grid md:grid-cols-3 gap-6">
      <?php foreach ($items as $item): ?>
        <div class="rounded-2xl p-8 bg-gradient-to-br from-slate-900 to-slate-800 text-white shadow-lg hover:-translate-y-1 transition">
          <div class="w-11 h-11 rounded-lg bg-white/10 flex items-center justify-center mb-5 text-brand-300">
            <?= Icon::render($item['icon'] ?? null, 'w-6 h-6') ?>
          </div>
          <h3 class="font-semibold text-lg"><?= View::e($item['title'] ?? '') ?></h3>
          <p class="mt-3 text-sm text-slate-300"><?= View::e($item['description'] ?? '') ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
