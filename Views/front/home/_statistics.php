<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $content */
/** @var array $statistics */
?>
<?php if ($statistics !== []): ?>
<section class="relative overflow-hidden bg-slate-950 py-20">
  <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(circle at 15% 20%, rgb(var(--brand-600) / 0.35), transparent 40%), radial-gradient(circle at 85% 80%, rgb(var(--accent-600) / 0.3), transparent 40%);"></div>
  <div class="relative max-w-6xl mx-auto px-6">
    <?php if (!empty($content['title'])): ?>
      <h2 class="text-2xl md:text-3xl font-extrabold text-white text-center mb-14"><?= View::e($content['title']) ?></h2>
    <?php endif; ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
      <?php foreach ($statistics as $stat): ?>
        <div class="group text-center">
          <?php if (!empty($stat['icon'])): ?>
            <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 text-white flex items-center justify-center mx-auto mb-5 group-hover:bg-white/10 group-hover:border-brand-400/40 transition">
              <?= Icon::render($stat['icon'], 'w-6 h-6') ?>
            </div>
          <?php endif; ?>
          <div class="text-4xl md:text-5xl font-extrabold bg-gradient-to-r from-brand-300 to-accent-300 bg-clip-text text-transparent">
            <?= View::e($stat['value']) ?><?= View::e($stat['suffix'] ?? '') ?>
          </div>
          <div class="mt-3 text-sm text-slate-400 font-medium"><?= View::e($stat['label']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
