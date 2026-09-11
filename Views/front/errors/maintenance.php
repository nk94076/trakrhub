<?php

/** @var string $siteName */
/** @var string $message */
/** @var string $heading */

use App\Core\View;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?></title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" type="image/svg+xml" href="<?= View::e(View::asset('images/techslay-icon.svg')) ?>">
<?php View::partial('partials.tailwind-config'); ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-purple-950 to-slate-900 flex items-center justify-center p-6">
  <div class="max-w-lg text-center flex flex-col items-center">
    <div class="mb-6"><?php View::partial('partials.logo', ['variant' => 'white', 'textClass' => 'text-2xl']); ?></div>
    <h1 class="text-3xl font-extrabold text-white mb-4"><?= htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="text-slate-300"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
  </div>
</body>
</html>
