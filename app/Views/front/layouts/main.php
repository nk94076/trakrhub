<?php

/** @var string $content */
/** @var array $headerMenu */
/** @var array $footerMenu */
/** @var array $seo Optional per-page SEO overrides: title, description, keywords,
 *  canonical, robots_index, robots_follow, og_title, og_description, og_image,
 *  twitter_card, schemas (array of pre-built JSON-LD arrays). */

use App\Core\Seo;
use App\Core\View;
use App\Models\Service;
use App\Models\Setting;

$siteName = Setting::get('branding', 'site_name', 'Techslay');

/**
 * The header's "Services" and "Company" nav items become rich mega panels
 * instead of plain links. Services are pulled from the Services module
 * directly (already richer than a menu link); "Company" groups whichever
 * admin-managed header menu items point at these core informational pages,
 * so adding/removing/reordering them in the Menu Builder still works without
 * touching this view.
 */
$companyGroupSlugs = ['about', 'technology', 'case-studies', 'blog'];
$navPrimary = [];
$navCompany = [];

foreach ($headerMenu ?? [] as $item) {
    $slug = trim($item['url'], '/');

    if (in_array($slug, ['services', 'contact'], true)) {
        continue;
    }

    if (in_array($slug, $companyGroupSlugs, true)) {
        $navCompany[] = $item;

        continue;
    }

    $navPrimary[] = $item;
}

$megaServices = Service::published();
$seo = $seo ?? [];

$resolvedTitle = $seo['title'] ?? ($pageTitle ?? '');
$fullTitle = $resolvedTitle !== '' ? $resolvedTitle . ' | ' . $siteName : $siteName;
$resolvedDescription = $seo['description'] ?? ($metaDescription ?? Setting::get('seo', 'default_meta_description', ''));
$resolvedCanonical = $seo['canonical'] ?? View::url(ltrim($_SERVER['REQUEST_URI'] ?? '/', '/'));
$robotsIndex = $seo['robots_index'] ?? 'index';
$robotsFollow = $seo['robots_follow'] ?? 'follow';
$ogTitle = $seo['og_title'] ?? ($resolvedTitle ?: $siteName);
$ogDescription = $seo['og_description'] ?? $resolvedDescription;
$ogImage = $seo['og_image'] ?? '';
$twitterCard = $seo['twitter_card'] ?? 'summary_large_image';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= View::e($fullTitle) ?></title>
<meta name="description" content="<?= View::e($resolvedDescription) ?>">
<?php if ($seo['keywords'] ?? ''): ?><meta name="keywords" content="<?= View::e($seo['keywords']) ?>"><?php endif; ?>
<meta name="robots" content="<?= View::e($robotsIndex . ', ' . $robotsFollow) ?>">
<link rel="canonical" href="<?= View::e($resolvedCanonical) ?>">

<?php $faviconSetting = Setting::get('branding', 'favicon', ''); ?>
<link rel="icon" type="image/svg+xml" href="<?= View::e($faviconSetting ? View::url(ltrim($faviconSetting, '/')) : View::asset('images/techslay-icon.svg')) ?>">

<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= View::e($siteName) ?>">
<meta property="og:title" content="<?= View::e($ogTitle) ?>">
<meta property="og:description" content="<?= View::e($ogDescription) ?>">
<meta property="og:url" content="<?= View::e($resolvedCanonical) ?>">
<?php if ($ogImage !== ''): ?><meta property="og:image" content="<?= View::e($ogImage) ?>"><?php endif; ?>
<meta name="twitter:card" content="<?= View::e($twitterCard) ?>">
<meta name="twitter:title" content="<?= View::e($ogTitle) ?>">
<meta name="twitter:description" content="<?= View::e($ogDescription) ?>">
<?php if ($ogImage !== ''): ?><meta name="twitter:image" content="<?= View::e($ogImage) ?>"><?php endif; ?>

<?= Seo::render(array_merge([Seo::organizationSchema(), Seo::websiteSchema()], $seo['schemas'] ?? [])) ?>

<?php
$gaId = Setting::get('analytics', 'google_analytics_id', '');
$gtmId = Setting::get('analytics', 'gtm_id', '');
$metaPixelId = Setting::get('analytics', 'meta_pixel_id', '');
$clarityId = Setting::get('analytics', 'clarity_id', '');
$customCss = Setting::get('general', 'custom_css', '');
?>
<?php if ($gtmId): ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= View::e($gtmId) ?>');</script>
<?php endif; ?>
<?php if ($gaId): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= View::e($gaId) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= View::e($gaId) ?>');</script>
<?php endif; ?>
<?php if ($clarityId): ?>
<script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y)})(window,document,"clarity","script","<?= View::e($clarityId) ?>");</script>
<?php endif; ?>
<?php if ($metaPixelId): ?>
<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?= View::e($metaPixelId) ?>');fbq('track','PageView');</script>
<?php endif; ?>
<!-- Admin-authored CSS (not user input, requires settings.manage permission) — intentionally unescaped raw CSS. -->
<?php if ($customCss): ?><style><?= $customCss ?></style><?php endif; ?>

<?php View::partial('partials.tailwind-config'); ?>
<script defer src="<?= View::e(View::asset('js/alpine-collapse.min.js')) ?>"></script>
<script defer src="<?= View::e(View::asset('js/alpine.min.js')) ?>"></script>
<script defer src="<?= View::e(View::asset('js/reveal.js')) ?>"></script>
</head>
<body class="bg-white text-slate-800 antialiased">

<?php View::partial('partials.header-nav', [
  'navPrimary' => $navPrimary,
  'navCompany' => $navCompany,
  'megaServices' => $megaServices,
]); ?>

<main><?= $content ?></main>

<footer class="bg-slate-950 text-slate-300 mt-24">
  <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-4 gap-10">
    <div>
      <div class="mb-3"><?php View::partial('partials.logo', ['variant' => 'white']); ?></div>
      <p class="text-sm text-slate-400"><?= View::e(Setting::get('branding', 'tagline', '')) ?></p>
    </div>
    <div>
      <div class="font-semibold text-white mb-3">Company</div>
      <ul class="space-y-2 text-sm">
        <?php foreach ($footerMenu ?? [] as $item): ?>
          <li><a href="<?= View::url(ltrim($item['url'], '/')) ?>" class="hover:text-white transition"><?= View::e($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <div class="font-semibold text-white mb-3">Contact</div>
      <ul class="space-y-2 text-sm text-slate-400">
        <li><?= View::e(Setting::get('business', 'email', '')) ?></li>
        <li><?= View::e(Setting::get('business', 'phone', '')) ?></li>
        <li><?= View::e(Setting::get('business', 'address', '')) ?></li>
      </ul>
    </div>
    <div>
      <div class="font-semibold text-white mb-3">Legal</div>
      <ul class="space-y-2 text-sm">
        <li><a href="<?= View::url('privacy-policy') ?>" class="hover:text-white transition">Privacy Policy</a></li>
        <li><a href="<?= View::url('terms') ?>" class="hover:text-white transition">Terms of Service</a></li>
        <li><a href="<?= View::url('cookie-policy') ?>" class="hover:text-white transition">Cookie Policy</a></li>
      </ul>
    </div>
  </div>
  <div class="border-t border-white/10 py-6 text-center text-xs text-slate-500">
    &copy; <?= date('Y') ?> <?= View::e($siteName) ?>. All rights reserved.
  </div>
</footer>

<?php View::partial('partials.cookie-banner'); ?>

<?php $customJs = Setting::get('general', 'custom_js', ''); ?>
<!-- Admin-authored JS (not user input, requires settings.manage permission) — intentionally unescaped raw script. -->
<?php if ($customJs): ?><script><?= $customJs ?></script><?php endif; ?>

</body>
</html>
