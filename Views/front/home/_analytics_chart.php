<?php

use App\Core\Chart;
use App\Core\Icon;
use App\Core\View;

/** @var array $content */
$points = $content['data'] ?? [];
$highlights = $content['highlights'] ?? [];
$chartType = $content['chart_type'] ?? 'bar';
?>
<?php if ($points !== []): ?>
<section class="py-20 bg-slate-50/60 overflow-hidden">
  <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
    <div>
      <span class="inline-block rounded-full bg-white border border-brand-100 px-4 py-1.5 text-xs font-semibold text-brand-600 shadow-sm mb-5">
        <?= View::e($content['eyebrow'] ?? 'Live Reporting') ?>
      </span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?= View::e($content['title'] ?? '') ?></h2>
      <p class="mt-4 text-slate-600"><?= View::e($content['subtitle'] ?? '') ?></p>

      <?php if ($highlights !== []): ?>
        <div class="mt-8 space-y-4">
          <?php foreach ($highlights as $item): ?>
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <?= Icon::render($item['icon'] ?? null, 'w-4 h-4') ?>
              </div>
              <div>
                <div class="text-sm font-semibold text-slate-900"><?= View::e($item['title'] ?? '') ?></div>
                <div class="text-sm text-slate-500"><?= View::e($item['description'] ?? '') ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="relative">
      <div class="absolute -inset-4 bg-gradient-to-br from-brand-200/40 to-accent-200/40 blur-2xl rounded-3xl"></div>
      <div class="relative rounded-2xl bg-white border border-slate-100 shadow-xl p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <div class="text-sm font-semibold text-slate-900"><?= View::e($content['chart_title'] ?? 'Weekly Performance') ?></div>
            <div class="text-xs text-slate-400"><?= View::e($content['chart_subtitle'] ?? 'Last 7 days') ?></div>
          </div>
          <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 text-green-700 text-xs font-semibold px-2.5 py-1">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
            Live
          </span>
        </div>
        <?= $chartType === 'line' ? Chart::line($points) : Chart::bar($points) ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
