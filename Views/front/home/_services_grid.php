<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $content */
/** @var array $services */
?>
<?php if ($services !== []): ?>
<section class="py-20 bg-slate-50/60">
  <div class="max-w-6xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-14">
      <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?= View::e($content['title'] ?? '') ?></h2>
      <p class="mt-3 text-slate-600"><?= View::e($content['subtitle'] ?? '') ?></p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($services as $service): ?>
        <a href="<?= View::url('services/' . $service['slug']) ?>"
           class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 p-7">
          <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-500 to-accent-500 text-white flex items-center justify-center mb-5 shadow-lg shadow-brand-500/20 group-hover:scale-110 transition-transform duration-200">
            <?= Icon::render($service['icon'] ?? null, 'w-6 h-6') ?>
          </div>
          <h3 class="font-semibold text-lg text-slate-900 group-hover:text-brand-600 transition"><?= View::e($service['title']) ?></h3>
          <p class="mt-2 text-sm text-slate-500"><?= View::e($service['short_description']) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
