<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $content */
/** @var array $statistics */

$heroStats = array_slice($statistics ?? [], 0, 4);
?>
<section class="relative overflow-hidden bg-slate-950">
  <div class="absolute inset-0 opacity-60" style="background-image: radial-gradient(circle at 12% 15%, rgb(var(--brand-600) / 0.35), transparent 40%), radial-gradient(circle at 88% 75%, rgb(var(--accent-600) / 0.3), transparent 40%);"></div>
  <div class="absolute -top-40 -right-40 w-[36rem] h-[36rem] bg-gradient-to-br from-brand-500 to-accent-500 opacity-20 blur-3xl rounded-full"></div>
  <div class="absolute -bottom-40 -left-40 w-[30rem] h-[30rem] bg-gradient-to-tr from-accent-500 to-brand-400 opacity-10 blur-3xl rounded-full"></div>

  <div class="relative max-w-7xl mx-auto px-6 pt-20 pb-24 lg:pt-28 lg:pb-32 grid lg:grid-cols-2 gap-16 items-center">
    <div class="text-center lg:text-left">
      <span class="inline-block rounded-full bg-white/10 border border-white/10 px-4 py-1.5 text-xs font-semibold text-brand-300 mb-6">
        <?= View::e($content['eyebrow'] ?? '') ?>
      </span>
      <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
        <?= View::e($content['title'] ?? '') ?>
      </h1>
      <p class="mt-6 text-lg text-slate-300 max-w-xl mx-auto lg:mx-0">
        <?= View::e($content['subtitle'] ?? '') ?>
      </p>
      <div class="mt-10 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
        <a href="<?= View::url(ltrim($content['primary_button_url'] ?? '/', '/')) ?>"
           class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white font-semibold px-8 py-3.5 shadow-lg shadow-brand-500/30 hover:shadow-xl hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all duration-200">
          <?= View::e($content['primary_button_text'] ?? 'Get Started') ?>
        </a>
        <a href="<?= View::url(ltrim($content['secondary_button_url'] ?? '/', '/')) ?>"
           class="inline-flex items-center justify-center rounded-full bg-white/10 border border-white/20 text-white font-semibold px-8 py-3.5 hover:bg-white/15 transition-all duration-200">
          <?= View::e($content['secondary_button_text'] ?? 'Learn More') ?>
        </a>
      </div>
    </div>

    <div class="relative hidden lg:block" aria-hidden="true">
      <div class="absolute -inset-10 bg-gradient-to-br from-brand-200/30 to-accent-200/30 blur-3xl rounded-full"></div>

      <!-- Dashboard mockup card, built entirely from SVG/CSS (no stock imagery) -->
      <div class="relative mx-auto w-[380px] rotate-2 rounded-2xl bg-white shadow-2xl border border-slate-100 p-5">
        <div class="flex items-center justify-end mb-4">
          <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide">Live Dashboard</span>
        </div>
        <svg viewBox="0 0 320 150" class="w-full h-auto">
          <defs>
            <linearGradient id="heroBar" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="rgb(var(--brand-500))"/>
              <stop offset="100%" stop-color="rgb(var(--accent-500))"/>
            </linearGradient>
            <linearGradient id="heroBarSoft" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="rgb(var(--brand-200))"/>
              <stop offset="100%" stop-color="rgb(var(--brand-100))"/>
            </linearGradient>
          </defs>
          <line x1="0" y1="120" x2="320" y2="120" stroke="rgb(var(--brand-50))" stroke-width="1"/>
          <rect x="10" y="70" width="28" height="50" rx="5" fill="url(#heroBarSoft)"/>
          <rect x="50" y="50" width="28" height="70" rx="5" fill="url(#heroBarSoft)"/>
          <rect x="90" y="85" width="28" height="35" rx="5" fill="url(#heroBarSoft)"/>
          <rect x="130" y="35" width="28" height="85" rx="5" fill="url(#heroBar)"/>
          <rect x="170" y="60" width="28" height="60" rx="5" fill="url(#heroBarSoft)"/>
          <rect x="210" y="20" width="28" height="100" rx="5" fill="url(#heroBar)"/>
          <rect x="250" y="45" width="28" height="75" rx="5" fill="url(#heroBarSoft)"/>
        </svg>
        <div class="grid grid-cols-2 gap-3 mt-4">
          <div class="rounded-xl bg-brand-50 px-3 py-2.5">
            <div class="text-lg font-extrabold text-slate-900">98.6%</div>
            <div class="text-[11px] text-slate-500">Fraud-Free Traffic</div>
          </div>
          <div class="rounded-xl bg-accent-50 px-3 py-2.5">
            <div class="text-lg font-extrabold text-slate-900">2.4x</div>
            <div class="text-[11px] text-slate-500">Avg. Publisher ROI</div>
          </div>
        </div>
      </div>

      <!-- Floating status badges -->
      <div class="absolute -top-4 -left-6 flex items-center gap-2 rounded-xl bg-white shadow-lg border border-slate-100 px-3.5 py-2.5">
        <span class="w-7 h-7 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
          <?= Icon::render('check-circle', 'w-4 h-4') ?>
        </span>
        <span class="text-xs font-semibold text-slate-700">Conversion Verified</span>
      </div>

      <div class="absolute -bottom-6 -right-4 flex items-center gap-2 rounded-xl bg-white shadow-lg border border-slate-100 px-3.5 py-2.5">
        <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-brand-500 to-accent-500 text-white flex items-center justify-center">
          <?= Icon::render('shield-check', 'w-4 h-4') ?>
        </span>
        <span class="text-xs font-semibold text-slate-700">Fraud Detection Active</span>
      </div>
    </div>
  </div>

  <?php if ($heroStats !== []): ?>
    <div class="relative max-w-5xl mx-auto px-6 pb-16 lg:pb-20">
      <div class="rounded-2xl bg-white shadow-2xl px-6 sm:px-10 py-6 grid grid-cols-2 md:grid-cols-4 gap-6 divide-x divide-slate-100">
        <?php foreach ($heroStats as $stat): ?>
          <div class="text-center px-2">
            <div class="text-2xl md:text-3xl font-extrabold gradient-text"><?= View::e($stat['value']) ?><?= View::e($stat['suffix'] ?? '') ?></div>
            <div class="mt-1 text-xs text-slate-500 font-medium"><?= View::e($stat['label']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</section>
