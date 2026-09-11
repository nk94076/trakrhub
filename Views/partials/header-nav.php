<?php

use App\Core\Icon;
use App\Core\View;

/**
 * @var array $navPrimary  Flat header links rendered as-is (Home, Publishers, Advertisers, ...).
 * @var array $navCompany  Header links grouped under the "Company" mega panel (About, Technology, ...).
 * @var array $megaServices Published services, rendered in the "Services" mega panel.
 */
$navPrimary = $navPrimary ?? [];
$navCompany = $navCompany ?? [];
$megaServices = $megaServices ?? [];

$companyIconMap = [
    'about' => 'building-office',
    'technology' => 'cog',
    'case-studies' => 'briefcase',
    'blog' => 'document',
];
?>
<div x-data="{ openPanel: null, mobileOpen: false, mobileGroup: null }" @keydown.escape.window="openPanel = null; mobileOpen = false">
<header class="sticky top-0 z-50 glass border-b border-slate-100" @mouseleave="openPanel = null">
  <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
    <a href="<?= View::url('/') ?>"><?php View::partial('partials.logo'); ?></a>

    <nav class="hidden lg:flex items-center gap-1 text-sm font-medium text-slate-600">
      <?php foreach ($navPrimary as $item): ?>
        <a href="<?= View::url(ltrim($item['url'], '/')) ?>" class="px-3 py-2 rounded-lg hover:text-brand-600 hover:bg-slate-50 transition"><?= View::e($item['label']) ?></a>
      <?php endforeach; ?>

      <div class="relative" @mouseenter="openPanel = 'services'">
        <button type="button" @click="openPanel = openPanel === 'services' ? null : 'services'"
                class="px-3 py-2 rounded-lg inline-flex items-center gap-1 hover:bg-slate-50 transition"
                :class="openPanel === 'services' ? 'text-brand-600 bg-slate-50' : ''"
                aria-haspopup="true" :aria-expanded="openPanel === 'services'">
          Services
          <svg class="w-3.5 h-3.5 transition-transform" :class="openPanel === 'services' ? 'rotate-180' : ''" viewBox="0 0 12 12" fill="none"><path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>

      <?php if ($navCompany !== []): ?>
        <div class="relative" @mouseenter="openPanel = 'company'">
          <button type="button" @click="openPanel = openPanel === 'company' ? null : 'company'"
                  class="px-3 py-2 rounded-lg inline-flex items-center gap-1 hover:bg-slate-50 transition"
                  :class="openPanel === 'company' ? 'text-brand-600 bg-slate-50' : ''"
                  aria-haspopup="true" :aria-expanded="openPanel === 'company">
            Company
            <svg class="w-3.5 h-3.5 transition-transform" :class="openPanel === 'company' ? 'rotate-180' : ''" viewBox="0 0 12 12" fill="none"><path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      <?php endif; ?>
    </nav>

    <div class="flex items-center gap-2">
      <a href="<?= View::url('contact') ?>" class="hidden md:inline-flex items-center rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-5 py-2.5 shadow-lg shadow-brand-500/20 hover:shadow-brand-500/40 transition">Get Started</a>
      <button type="button" @click="mobileOpen = true" class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-slate-50 transition" aria-label="Open menu">
        <svg class="w-6 h-6 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>
  </div>

  <!-- Services mega panel -->
  <div x-show="openPanel === 'services'" x-cloak x-transition.opacity.duration.150ms
       class="hidden lg:block absolute inset-x-0 top-full bg-white border-t border-slate-100 shadow-2xl shadow-slate-900/10">
    <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-[1fr_320px] gap-10">
      <div>
        <div class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-1">What We Do</div>
        <h3 class="text-xl font-bold text-slate-900 mb-6">Full-stack performance marketing services</h3>
        <div class="grid grid-cols-2 gap-x-8 gap-y-1">
          <?php foreach ($megaServices as $service): ?>
            <a href="<?= View::url('services/' . $service['slug']) ?>" class="group flex items-start gap-3 rounded-xl px-3 py-3 hover:bg-slate-50 transition">
              <span class="shrink-0 w-9 h-9 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-500 group-hover:text-white transition">
                <?= Icon::render($service['icon'] ?? null, 'w-5 h-5') ?>
              </span>
              <span>
                <span class="block text-sm font-semibold text-slate-900 group-hover:text-brand-600 transition"><?= View::e($service['title']) ?></span>
                <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed"><?= View::e($service['short_description'] ?? '') ?></span>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
        <a href="<?= View::url('services') ?>" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 mt-5">
          View all services
          <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h9M9 4.5L12.5 8 9 11.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>
      <div class="rounded-2xl bg-slate-950 p-6 text-white flex flex-col justify-between">
        <div>
          <div class="text-xs font-semibold uppercase tracking-wider text-white/50 mb-2">Not sure where to start?</div>
          <p class="text-sm text-white/80 leading-relaxed mb-4">Every engagement is backed by dedicated account management, real-time reporting and fraud-vetted traffic — whichever service you pick.</p>
        </div>
        <a href="<?= View::url('contact') ?>" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-5 py-2.5 hover:shadow-lg hover:shadow-brand-500/30 transition">Talk to a Strategist</a>
      </div>
    </div>
  </div>

  <!-- Company mega panel -->
  <?php if ($navCompany !== []): ?>
    <div x-show="openPanel === 'company'" x-cloak x-transition.opacity.duration.150ms
         class="hidden lg:block absolute inset-x-0 top-full bg-white border-t border-slate-100 shadow-2xl shadow-slate-900/10">
      <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-1">Company</div>
        <h3 class="text-xl font-bold text-slate-900 mb-6">Get to know Techslay</h3>
        <div class="grid grid-cols-3 gap-4 max-w-3xl">
          <?php foreach ($navCompany as $item):
            $slug = trim($item['url'], '/');
            $icon = $item['icon'] ?: ($companyIconMap[$slug] ?? 'link');
          ?>
            <a href="<?= View::url($slug) ?>" class="group flex items-start gap-3 rounded-xl px-3 py-3 hover:bg-slate-50 transition">
              <span class="shrink-0 w-9 h-9 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-500 group-hover:text-white transition">
                <?= Icon::render($icon, 'w-5 h-5') ?>
              </span>
              <span class="text-sm font-semibold text-slate-900 group-hover:text-brand-600 transition pt-1.5"><?= View::e($item['label']) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="border-t border-slate-100 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
          <span class="text-sm text-slate-500">Want to talk to a real person first?</span>
          <a href="<?= View::url('contact') ?>" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
            Contact Us
            <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h9M9 4.5L12.5 8 9 11.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>
</header>

<!-- Mobile drawer rendered outside <header> on purpose: the header's `.glass`
     backdrop-filter creates a new containing block in Chromium, which would
     make this `fixed` panel position itself relative to the header (~72px
     tall) instead of the viewport. -->
<div x-show="mobileOpen" x-cloak class="lg:hidden fixed inset-0 z-[60]">
    <div class="absolute inset-0 bg-slate-900/50" @click="mobileOpen = false" x-show="mobileOpen" x-transition.opacity></div>
    <div class="absolute inset-y-0 right-0 w-[85%] max-w-sm bg-white shadow-2xl flex flex-col"
         x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <?php View::partial('partials.logo'); ?>
        <button type="button" @click="mobileOpen = false" class="w-9 h-9 inline-flex items-center justify-center rounded-lg hover:bg-slate-50" aria-label="Close menu">
          <svg class="w-5 h-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <div class="flex-1 overflow-y-auto px-3 py-3 text-sm">
        <?php foreach ($navPrimary as $item): ?>
          <a href="<?= View::url(ltrim($item['url'], '/')) ?>" class="block px-3 py-3 rounded-lg font-medium text-slate-700 hover:bg-slate-50"><?= View::e($item['label']) ?></a>
        <?php endforeach; ?>

        <button type="button" @click="mobileGroup = mobileGroup === 'services' ? null : 'services'" class="w-full flex items-center justify-between px-3 py-3 rounded-lg font-medium text-slate-700 hover:bg-slate-50">
          Services
          <svg class="w-4 h-4 transition-transform" :class="mobileGroup === 'services' ? 'rotate-180' : ''" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6l4 4 4-4"/></svg>
        </button>
        <div x-show="mobileGroup === 'services'" x-collapse class="pl-3 pb-2">
          <?php foreach ($megaServices as $service): ?>
            <a href="<?= View::url('services/' . $service['slug']) ?>" class="block px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-brand-600"><?= View::e($service['title']) ?></a>
          <?php endforeach; ?>
        </div>

        <?php if ($navCompany !== []): ?>
          <button type="button" @click="mobileGroup = mobileGroup === 'company' ? null : 'company'" class="w-full flex items-center justify-between px-3 py-3 rounded-lg font-medium text-slate-700 hover:bg-slate-50">
            Company
            <svg class="w-4 h-4 transition-transform" :class="mobileGroup === 'company' ? 'rotate-180' : ''" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6l4 4 4-4"/></svg>
          </button>
          <div x-show="mobileGroup === 'company'" x-collapse class="pl-3 pb-2">
            <?php foreach ($navCompany as $item): ?>
              <a href="<?= View::url(ltrim($item['url'], '/')) ?>" class="block px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-brand-600"><?= View::e($item['label']) ?></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="p-4 border-t border-slate-100">
        <a href="<?= View::url('contact') ?>" class="block text-center rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-5 py-3">Get Started</a>
      </div>
    </div>
  </div>
</div>
