<?php

use App\Core\View;

/** @var array $content */
$align = ($content['align'] ?? 'left') === 'center' ? 'text-center mx-auto' : '';
?>
<section class="py-16">
  <div class="max-w-3xl <?= $align ?> px-6 mx-auto">
    <?php if (!empty($content['title'])): ?>
      <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-6"><?= View::e($content['title']) ?></h1>
    <?php endif; ?>
    <?php if (!empty($content['body'])): ?>
      <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed whitespace-pre-line">
        <?= nl2br(View::e($content['body'])) ?>
      </div>
    <?php endif; ?>
  </div>
</section>
