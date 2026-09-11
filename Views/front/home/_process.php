<?php

use App\Core\View;

/** @var array $content */
$items = $content['items'] ?? [];
?>
<?php if ($items !== []): ?>
<section class="py-20 bg-white">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-4"><?= View::e($content['title'] ?? '') ?></h2>
    <?php if (!empty($content['subtitle'])): ?>
      <p class="text-center text-slate-600 max-w-2xl mx-auto mb-14"><?= View::e($content['subtitle']) ?></p>
    <?php else: ?>
      <div class="mb-14"></div>
    <?php endif; ?>
    <div class="relative grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="hidden lg:block absolute top-8 left-0 right-0 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
      <?php foreach ($items as $index => $item): ?>
        <div class="group relative rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-brand-200 transition-all duration-200 p-6 pt-8">
          <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-500 to-accent-500 text-white flex items-center justify-center text-sm font-bold mb-4 shadow-lg shadow-brand-500/30">
            <?= $index + 1 ?>
          </div>
          <p class="text-sm font-semibold text-slate-800"><?= View::e($item) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
