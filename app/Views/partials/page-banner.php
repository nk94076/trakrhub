<?php

use App\Core\View;

/**
 * Shared dark "enterprise SaaS" banner for simple text-only page headers
 * (Services index, Blog index, Case Studies, Search) — the richer pages
 * with their own hero layout (Home, Publishers, Advertisers, Technology,
 * Service detail) build their own markup instead of using this partial.
 *
 * @var string $title
 * @var string $subtitle Optional
 * @var string $badge    Optional small pill shown above the title
 */
$title = $title ?? '';
$subtitle = $subtitle ?? '';
$badge = $badge ?? '';
?>
<section class="relative overflow-hidden bg-slate-950 py-20">
  <div class="absolute inset-0 opacity-60" style="background-image: radial-gradient(circle at 12% 15%, rgb(var(--brand-600) / 0.35), transparent 40%), radial-gradient(circle at 88% 75%, rgb(var(--accent-600) / 0.3), transparent 40%);"></div>
  <div class="relative max-w-3xl mx-auto px-6 text-center">
    <?php if ($badge !== ''): ?>
      <span class="inline-block rounded-full bg-white/10 border border-white/10 px-4 py-1.5 text-xs font-semibold text-brand-300 mb-6"><?= View::e($badge) ?></span>
    <?php endif; ?>
    <h1 class="text-4xl md:text-5xl font-extrabold text-white"><?= View::e($title) ?></h1>
    <?php if ($subtitle !== ''): ?>
      <p class="mt-4 text-slate-300"><?= View::e($subtitle) ?></p>
    <?php endif; ?>
  </div>
</section>
