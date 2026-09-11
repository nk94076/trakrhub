<?php

use App\Core\View;

/** @var array $content */
/** @var array $caseStudies */
?>
<?php if ($caseStudies !== []): ?>
<section class="py-20 bg-slate-50/60">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 text-center mb-14"><?= View::e($content['title'] ?? 'Case Studies') ?></h2>
    <div class="grid md:grid-cols-3 gap-6">
      <?php foreach ($caseStudies as $case): ?>
        <a href="<?= View::url('case-studies/' . $case['slug']) ?>" class="rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-lg transition p-7 block">
          <h3 class="font-semibold text-slate-900"><?= View::e($case['title']) ?></h3>
          <p class="mt-2 text-sm text-slate-500"><?= View::e($case['summary'] ?? '') ?></p>
          <?php $metrics = json_decode((string) ($case['metrics'] ?? '[]'), true) ?: []; ?>
          <?php if ($metrics !== []): ?>
            <div class="mt-4 flex gap-4">
              <?php foreach (array_slice($metrics, 0, 2) as $metric): ?>
                <div>
                  <div class="text-lg font-bold gradient-text"><?= View::e($metric['value'] ?? '') ?></div>
                  <div class="text-[11px] text-slate-400"><?= View::e($metric['label'] ?? '') ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
