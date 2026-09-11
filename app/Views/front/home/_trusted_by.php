<?php

use App\Core\View;

/** @var array $content */
$logos = $content['logos'] ?? [];
?>
<?php if ($logos !== []): ?>
<section class="py-12 border-y border-slate-100 bg-slate-50/50">
  <div class="max-w-6xl mx-auto px-6">
    <p class="text-center text-xs font-semibold tracking-widest text-slate-400 uppercase mb-8">
      <?= View::e($content['title'] ?? '') ?>
    </p>
    <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
      <?php foreach ($logos as $logo): ?>
        <?php if (is_array($logo) && !empty($logo['image'])): ?>
          <img src="<?= View::e(View::url(ltrim($logo['image'], '/'))) ?>" alt="<?= View::e($logo['name'] ?? '') ?>" class="h-7 w-auto grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-200">
        <?php else: ?>
          <span class="text-slate-400 hover:text-slate-600 font-bold text-lg tracking-tight transition-colors"><?= View::e(is_array($logo) ? ($logo['name'] ?? '') : $logo) ?></span>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
