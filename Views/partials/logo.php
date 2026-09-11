<?php

use App\Core\View;
use App\Models\Setting;

/**
 * Shared logo renderer used by the front header/footer and both admin
 * layouts, so the branding.logo setting and the icon fallback only need
 * to be wired up once.
 *
 * @var string $variant 'color' (default, for light backgrounds) or 'white' (for dark backgrounds)
 * @var bool   $withText whether to render the "Techslay" wordmark next to the icon (default true)
 * @var string $textClass extra classes for the wordmark span
 */
$variant = $variant ?? 'color';
$withText = $withText ?? true;
$textClass = $textClass ?? '';

$siteName = Setting::get('branding', 'site_name', 'Techslay');
$uploadedLogo = Setting::get('branding', 'logo', '');
?>
<span class="inline-flex items-center gap-2.5">
  <?php if ($uploadedLogo): ?>
    <img src="<?= View::e(View::url(ltrim($uploadedLogo, '/'))) ?>" alt="<?= View::e($siteName) ?>" class="h-8 w-8 rounded-lg object-contain">
  <?php else: ?>
    <!-- Built-in icon has its own gradient background, so it reads fine on light or dark surfaces without a separate white variant. -->
    <img src="<?= View::e(View::asset('images/techslay-icon.svg')) ?>" alt="<?= View::e($siteName) ?>" class="h-8 w-8" width="32" height="32">
  <?php endif; ?>
  <?php if ($withText): ?>
    <span class="text-xl font-bold <?= $variant === 'white' ? 'text-white' : 'gradient-text' ?> <?= View::e($textClass) ?>"><?= View::e($siteName) ?></span>
  <?php endif; ?>
</span>
