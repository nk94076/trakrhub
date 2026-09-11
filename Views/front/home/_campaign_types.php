<?php

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
        <div class="rounded-2xl border border-slate-100 p-8 bg-gradient-to-b from-white to-slate-50 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-brand-200 transition-all duration-200">
          <div class="text-sm font-bold text-white bg-gradient-to-r from-brand-500 to-accent-500 inline-block rounded-full px-4 py-1.5 mb-5">
            <?= View::e($item['code'] ?? '') ?>
          </div>
          <h3 class="text-xl font-bold text-slate-900"><?= View::e($item['name'] ?? '') ?></h3>
          <p class="mt-3 text-sm text-slate-600"><?= View::e($item['description'] ?? '') ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
