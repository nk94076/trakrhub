<?php

use App\Core\View;

/** @var array $content */
/** @var array $testimonials */
?>
<?php if ($testimonials !== []): ?>
<section class="py-20">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-14"><?= View::e($content['title'] ?? '') ?></h2>
    <div class="grid md:grid-cols-3 gap-6">
      <?php foreach ($testimonials as $t): ?>
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-lg transition p-7">
          <div class="text-brand-500 mb-3"><?= str_repeat('&#9733;', (int) $t['rating']) ?></div>
          <p class="text-sm text-slate-600">&ldquo;<?= View::e($t['content']) ?>&rdquo;</p>
          <div class="mt-5 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-400 to-accent-500 flex items-center justify-center text-white text-sm font-semibold">
              <?= View::e(mb_strtoupper(mb_substr($t['name'], 0, 1))) ?>
            </div>
            <div>
              <div class="text-sm font-semibold text-slate-900"><?= View::e($t['name']) ?></div>
              <div class="text-xs text-slate-500"><?= View::e($t['designation']) ?><?= $t['company'] ? ', ' . View::e($t['company']) : '' ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
