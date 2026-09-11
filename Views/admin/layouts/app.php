<?php

use App\Core\Auth;
use App\Core\Icon;
use App\Core\Session;
use App\Core\View;
use App\Models\Setting;

/** @var string $content */
$adminSiteName = Setting::get('branding', 'site_name', 'Techslay');
$pageTitle = ($pageTitle ?? 'Dashboard') . ' | ' . $adminSiteName . ' Admin';
$currentUser = Auth::user();

$navItems = [
    ['label' => 'Dashboard', 'url' => 'admin/dashboard', 'icon' => 'squares'],
    ['label' => 'Pages', 'url' => 'admin/pages', 'icon' => 'document'],
    ['label' => 'Media', 'url' => 'admin/media', 'icon' => 'photo'],
    ['label' => 'Menus', 'url' => 'admin/menus', 'icon' => 'link'],
    ['label' => 'Blog', 'url' => 'admin/blog', 'icon' => 'pencil-square'],
    ['label' => 'Services', 'url' => 'admin/services', 'icon' => 'briefcase'],
    ['label' => 'Industries', 'url' => 'admin/industries', 'icon' => 'building-office'],
    ['label' => 'Case Studies', 'url' => 'admin/case-studies', 'icon' => 'chart-bar'],
    ['label' => 'Testimonials', 'url' => 'admin/testimonials', 'icon' => 'chat-bubble'],
    ['label' => 'FAQs', 'url' => 'admin/faqs', 'icon' => 'question-mark-circle'],
    ['label' => 'Statistics', 'url' => 'admin/statistics', 'icon' => 'trending-up'],
    ['label' => 'Leads', 'url' => 'admin/leads', 'icon' => 'envelope'],
    ['label' => 'Redirects', 'url' => 'admin/redirects', 'icon' => 'arrow-path'],
    ['label' => 'SEO Settings', 'url' => 'admin/settings/seo', 'icon' => 'magnifying-glass'],
    ['label' => 'Users', 'url' => 'admin/users', 'icon' => 'users'],
    ['label' => 'Roles', 'url' => 'admin/roles', 'icon' => 'lock-closed'],
    ['label' => 'Settings', 'url' => 'admin/settings', 'icon' => 'cog'],
];

$currentPath = trim($_SERVER['REQUEST_URI'] ?? '', '/');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= View::e($pageTitle) ?></title>
<meta name="robots" content="noindex, nofollow">
<?php $adminFavicon = Setting::get('branding', 'favicon', ''); ?>
<link rel="icon" type="image/svg+xml" href="<?= View::e($adminFavicon ? View::url(ltrim($adminFavicon, '/')) : View::asset('images/techslay-icon.svg')) ?>">
<?php View::partial('partials.tailwind-config'); ?>
</head>
<body class="bg-slate-50 text-slate-800">
<div class="flex min-h-screen">

  <aside class="w-64 bg-slate-950 text-slate-300 flex-shrink-0 hidden lg:flex lg:flex-col">
    <div class="px-6 py-5 border-b border-white/10">
      <?php View::partial('partials.logo', ['variant' => 'white', 'textClass' => 'text-base']); ?>
    </div>
    <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-3">
      <?php foreach ($navItems as $item): ?>
        <?php $active = str_starts_with($currentPath, $item['url']); ?>
        <a href="<?= View::url($item['url']) ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 <?= $active ? 'bg-gradient-to-r from-brand-600 to-accent-600 text-white shadow-lg shadow-brand-900/30' : 'hover:bg-white/5 text-slate-300' ?>">
          <span class="w-5 flex-shrink-0"><?= Icon::render($item['icon'], 'w-5 h-5') ?></span>
          <span><?= View::e($item['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="p-4 border-t border-white/10">
      <form action="<?= View::url('admin/logout') ?>" method="POST">
        <?= View::csrfField() ?>
        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-white/5 transition">
          &#8592; Logout
        </button>
      </form>
    </div>
  </aside>

  <div class="flex-1 flex flex-col min-w-0">
    <header class="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between">
      <h1 class="text-lg font-semibold text-slate-900"><?= View::e($pageHeading ?? ($pageTitle ?? 'Dashboard')) ?></h1>
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-500 to-accent-500 flex items-center justify-center text-white text-sm font-semibold">
          <?= View::e(mb_strtoupper(mb_substr($currentUser['name'] ?? 'A', 0, 1))) ?>
        </div>
        <div class="text-sm">
          <div class="font-medium text-slate-900"><?= View::e($currentUser['name'] ?? '') ?></div>
          <div class="text-xs text-slate-400"><?= View::e($currentUser['role_name'] ?? '') ?></div>
        </div>
      </div>
    </header>

    <main class="flex-1 p-6">
      <?php if ($error = Session::getFlash('error')): ?>
        <div class="mb-5 rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3"><?= View::e($error) ?></div>
      <?php endif; ?>
      <?php if ($success = Session::getFlash('success')): ?>
        <div class="mb-5 rounded-lg bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3"><?= View::e($success) ?></div>
      <?php endif; ?>
      <?= $content ?>
    </main>
  </div>
</div>

<?php View::partial('admin.partials._media_picker'); ?>
</body>
</html>
