<?php

use App\Core\View;
use App\Models\Setting;

/** @var string $content */
$guestSiteName = Setting::get('branding', 'site_name', 'Techslay');
$pageTitle = ($pageTitle ?? 'Admin') . ' | ' . $guestSiteName . ' Admin';
$guestFavicon = Setting::get('branding', 'favicon', '');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= View::e($pageTitle) ?></title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" type="image/svg+xml" href="<?= View::e($guestFavicon ? View::url(ltrim($guestFavicon, '/')) : View::asset('images/techslay-icon.svg')) ?>">
<?php View::partial('partials.tailwind-config'); ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-purple-950 to-slate-900 flex items-center justify-center p-6">
<div class="w-full max-w-md">
  <div class="text-center mb-8 flex justify-center">
    <?php View::partial('partials.logo', ['variant' => 'white', 'textClass' => 'text-2xl']); ?>
  </div>
  <div class="rounded-2xl bg-white shadow-2xl p-8">
    <?php if ($error = \App\Core\Session::getFlash('error')): ?>
      <div class="mb-5 rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3"><?= View::e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = \App\Core\Session::getFlash('success')): ?>
      <div class="mb-5 rounded-lg bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3"><?= View::e($success) ?></div>
    <?php endif; ?>
    <?= $content ?>
  </div>
</div>
</body>
</html>
