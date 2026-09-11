<?php

use App\Core\Icon;
use App\Core\Session;
use App\Core\View;

/** @var array $service */
/** @var array $stats */
/** @var array $highlights */
/** @var array $processSteps */
/** @var array $faqs */
/** @var array $otherServices */

$formId = 'svc-form-' . substr(md5($service['slug']), 0, 6);

// Grid column counts are computed from admin-entered data, so the literal
// Tailwind class names must appear in this file for the CSS build's content
// scanner to pick them up. A runtime-interpolated class name built by
// concatenating "grid-cols-" with a number is invisible to that scanner and
// silently renders unstyled.
$statsColsClass = match (true) {
    count($stats) >= 4 => 'sm:grid-cols-4',
    count($stats) === 3 => 'sm:grid-cols-3',
    default => 'sm:grid-cols-2',
};
$stepsColsClass = match (true) {
    count($processSteps) >= 4 => 'lg:grid-cols-4',
    count($processSteps) === 3 => 'lg:grid-cols-3',
    default => 'lg:grid-cols-2',
};

$navLinks = array_filter([
    ['id' => 'overview', 'label' => 'Overview', 'show' => !empty($service['content'])],
    ['id' => 'how-it-works', 'label' => 'How It Works', 'show' => $processSteps !== []],
    ['id' => 'whats-included', 'label' => "What's Included", 'show' => $highlights !== []],
    ['id' => 'faqs', 'label' => 'FAQs', 'show' => $faqs !== []],
], static fn ($item) => $item['show']);
?>

<!-- Hero -->
<section class="relative overflow-hidden bg-slate-950 py-16 lg:py-20">
  <div class="absolute inset-0 opacity-60" style="background-image: radial-gradient(circle at 12% 15%, rgb(var(--brand-600) / 0.35), transparent 40%), radial-gradient(circle at 88% 75%, rgb(var(--accent-600) / 0.3), transparent 40%);"></div>

  <div class="relative max-w-7xl mx-auto px-6 grid lg:grid-cols-[1fr_420px] gap-12 items-start">
    <div>
      <nav class="text-xs text-slate-400 mb-4" aria-label="Breadcrumb">
        <a href="<?= View::url('/') ?>" class="hover:text-white">Home</a>
        <span class="mx-1.5">/</span>
        <a href="<?= View::url('services') ?>" class="hover:text-white">Services</a>
        <span class="mx-1.5">/</span>
        <span class="text-slate-300"><?= View::e($service['title']) ?></span>
      </nav>
      <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/10 px-4 py-1.5 text-xs font-semibold text-brand-300 mb-6">
        <?= Icon::render($service['icon'] ?? null, 'w-3.5 h-3.5') ?>
        Service
      </span>
      <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight"><?= View::e($service['title']) ?></h1>
      <p class="mt-5 text-lg text-slate-300 leading-relaxed max-w-xl"><?= View::e($service['short_description']) ?></p>

      <?php if ($navLinks !== []): ?>
        <div class="mt-8 flex flex-wrap gap-2">
          <?php foreach ($navLinks as $link): ?>
            <a href="#<?= $link['id'] ?>" class="text-sm font-medium text-white/80 bg-white/10 border border-white/10 rounded-full px-4 py-2 hover:bg-white/15 hover:text-white transition"><?= View::e($link['label']) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div id="get-started" class="rounded-2xl bg-white shadow-xl border border-slate-100 p-6 sm:p-7 scroll-mt-24">
      <h2 class="font-semibold text-slate-900 mb-1">Talk to Us About <?= View::e($service['title']) ?></h2>
      <p class="text-sm text-slate-500 mb-5">Tell us about your goals — we'll respond within one business day.</p>

      <?php if ($success = Session::getFlash('success')): ?>
        <div class="mb-4 rounded-lg bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3"><?= View::e($success) ?></div>
      <?php endif; ?>
      <?php if ($error = Session::getFlash('error')): ?>
        <div class="mb-4 rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3"><?= View::e($error) ?></div>
      <?php endif; ?>

      <form action="<?= View::url('contact/submit') ?>" method="POST" class="space-y-3">
        <?= View::csrfField() ?>
        <input type="hidden" name="lead_type" value="general">
        <input type="hidden" name="message" value="Requested more information about <?= View::e($service['title']) ?> via the service page.">
        <div class="hidden" aria-hidden="true">
          <label for="<?= $formId ?>-website">Leave this field empty</label>
          <input type="text" id="<?= $formId ?>-website" name="website" tabindex="-1" autocomplete="off">
        </div>
        <input type="text" name="name" placeholder="Full name" required class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
        <input type="email" name="email" placeholder="Work email" required class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
        <input type="text" name="company" placeholder="Company (optional)" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
        <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white font-semibold py-3 shadow-lg shadow-brand-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
          Get Started
        </button>
        <p class="text-center text-xs text-slate-400">No spam. We respond within one business day.</p>
      </form>
    </div>
  </div>

  <!-- Floating stats card -->
  <?php if ($stats !== []): ?>
    <div class="relative max-w-5xl mx-auto px-6 pt-14 pb-2">
      <div class="rounded-2xl bg-white shadow-2xl px-6 sm:px-10 py-6 grid grid-cols-2 <?= $statsColsClass ?> gap-6 divide-x divide-slate-100">
        <?php foreach ($stats as $stat): ?>
          <div class="text-center px-2">
            <div class="text-2xl md:text-3xl font-extrabold gradient-text"><?= View::e($stat['value'] ?? '') ?></div>
            <div class="mt-1 text-xs text-slate-500 font-medium"><?= View::e($stat['label'] ?? '') ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</section>

<!-- In-page sticky nav -->
<?php if ($navLinks !== []): ?>
<div class="sticky top-[72px] z-30 bg-white/90 backdrop-blur border-b border-slate-100 hidden md:block">
  <div class="max-w-7xl mx-auto px-6 flex gap-8 text-sm font-medium text-slate-500">
    <?php foreach ($navLinks as $link): ?>
      <a href="#<?= $link['id'] ?>" class="py-4 border-b-2 border-transparent hover:text-brand-600 hover:border-brand-300 transition"><?= View::e($link['label']) ?></a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Overview -->
<?php if (!empty($service['content'])): ?>
<section id="overview" class="py-16 scroll-mt-32">
  <div class="max-w-3xl mx-auto px-6">
    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed whitespace-pre-line">
      <?= nl2br(View::e($service['content'])) ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- How It Works -->
<?php if ($processSteps !== []): ?>
<section id="how-it-works" class="py-16 bg-slate-50/60 scroll-mt-32">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 text-center mb-3">How It Works</h2>
    <p class="text-slate-500 text-center max-w-xl mx-auto mb-12">From first conversation to live campaign, here's what the process looks like.</p>
    <div class="grid sm:grid-cols-2 <?= $stepsColsClass ?> gap-6">
      <?php foreach ($processSteps as $index => $step): ?>
        <div class="relative rounded-2xl bg-white border border-slate-100 shadow-sm p-6 pt-8">
          <span class="absolute -top-4 left-6 w-8 h-8 rounded-full bg-gradient-to-br from-brand-500 to-accent-500 text-white text-sm font-bold flex items-center justify-center shadow-lg shadow-brand-500/30"><?= $index + 1 ?></span>
          <h3 class="font-semibold text-slate-900 mb-2"><?= View::e($step['title'] ?? '') ?></h3>
          <p class="text-sm text-slate-500 leading-relaxed"><?= View::e($step['description'] ?? '') ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- What's Included -->
<?php if ($highlights !== []): ?>
<section id="whats-included" class="py-16 scroll-mt-32">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 text-center mb-3">What's Included</h2>
    <p class="text-slate-500 text-center max-w-xl mx-auto mb-12">Everything that comes standard with this service — no separate add-ons required.</p>
    <div class="grid sm:grid-cols-2 gap-6">
      <?php foreach ($highlights as $item): ?>
        <div class="flex gap-4 rounded-2xl bg-white border border-slate-100 shadow-sm p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
          <span class="shrink-0 w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
            <?= Icon::render($item['icon'] ?? null, 'w-5 h-5') ?>
          </span>
          <div>
            <h3 class="font-semibold text-slate-900 mb-1"><?= View::e($item['title'] ?? '') ?></h3>
            <p class="text-sm text-slate-500 leading-relaxed"><?= View::e($item['description'] ?? '') ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Mid-page CTA break -->
<section class="py-16">
  <div class="max-w-5xl mx-auto px-6">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 via-brand-600 to-accent-600 shadow-2xl px-8 py-12 lg:px-14 text-center">
      <div class="absolute -top-16 -right-16 w-56 h-56 bg-white/10 rounded-full"></div>
      <div class="absolute -bottom-14 -left-14 w-48 h-48 bg-white/10 rounded-full"></div>
      <div class="relative">
        <h2 class="text-2xl md:text-3xl font-extrabold text-white">Ready to see <?= View::e($service['title']) ?> in action?</h2>
        <p class="mt-3 text-white/80 max-w-xl mx-auto">Talk to our team about your goals — no obligation, no generic pitch deck.</p>
        <a href="#get-started" class="mt-7 inline-flex items-center rounded-full bg-white text-brand-700 font-semibold px-8 py-3.5 shadow-lg hover:shadow-xl transition">
          Talk to Us
        </a>
      </div>
    </div>
  </div>
</section>

<!-- FAQs -->
<?php if ($faqs !== []): ?>
<section id="faqs" class="py-16 bg-slate-50/60 scroll-mt-32" x-data="{ open: null }">
  <div class="max-w-3xl mx-auto px-6">
    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 text-center mb-12">Frequently Asked Questions</h2>
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

<!-- Other services -->
<?php if ($otherServices !== []): ?>
<section class="py-16">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-2xl font-bold text-slate-900 mb-8 text-center">Other Services</h2>
    <div class="grid sm:grid-cols-3 gap-6">
      <?php foreach ($otherServices as $other): ?>
        <a href="<?= View::url('services/' . $other['slug']) ?>" class="group rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 p-6">
          <span class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mb-4 group-hover:bg-brand-500 group-hover:text-white transition">
            <?= Icon::render($other['icon'] ?? null, 'w-5 h-5') ?>
          </span>
          <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 transition"><?= View::e($other['title']) ?></h3>
          <p class="mt-2 text-sm text-slate-500"><?= View::e($other['short_description']) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
