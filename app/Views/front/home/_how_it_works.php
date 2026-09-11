<?php

use App\Core\View;

/** @var array $content */
$items = $content['items'] ?? [];
?>
<?php if ($items !== []): ?>
<section class="py-20">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-14"><?= View::e($content['title'] ?? '') ?></h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($items as $item): ?>
        <div class="relative rounded-2xl border border-slate-100 p-7 bg-white shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-brand-200 transition-all duration-200">
          <div class="text-4xl font-extrabold text-brand-100"><?= View::e($item['step'] ?? '') ?></div>
          <h3 class="mt-2 font-semibold text-slate-900"><?= View::e($item['title'] ?? '') ?></h3>
          <p class="mt-2 text-sm text-slate-500"><?= View::e($item['description'] ?? '') ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
