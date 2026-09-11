<?php

use App\Core\View;

/** @var array $content */
/** @var array $faqs */
?>
<?php if ($faqs !== []): ?>
<section class="py-20" x-data="{ open: null }">
  <div class="max-w-3xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-14"><?= View::e($content['title'] ?? '') ?></h2>
    <div class="space-y-3">
      <?php foreach ($faqs as $index => $faq): ?>
        <div class="rounded-2xl border bg-white shadow-sm overflow-hidden transition-colors"
             :class="open === <?= (int) $index ?> ? 'border-brand-200' : 'border-slate-100'">
          <button type="button" @click="open = open === <?= (int) $index ?> ? null : <?= (int) $index ?>"
                  class="w-full flex items-center justify-between text-left px-6 py-4 font-medium text-slate-900 hover:bg-slate-50 transition-colors">
            <span><?= View::e($faq['question']) ?></span>
            <span class="flex-shrink-0 ml-4 w-6 h-6 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center text-sm transition-transform"
                  :class="open === <?= (int) $index ?> ? 'rotate-45' : ''">+</span>
          </button>
          <div x-show="open === <?= (int) $index ?>" x-collapse class="px-6 pb-4 text-sm text-slate-600">
            <?= View::e($faq['answer']) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
