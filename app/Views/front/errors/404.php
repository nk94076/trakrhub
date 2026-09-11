<?php

use App\Core\View;

$pageTitle = $pageTitle ?? 'Page Not Found';
?>
<section class="min-h-[60vh] flex items-center justify-center py-24">
  <div class="text-center px-6">
    <div class="text-7xl font-extrabold gradient-text">404</div>
    <h1 class="mt-4 text-2xl font-bold text-slate-900">Page Not Found</h1>
    <p class="mt-2 text-slate-500">The page you are looking for does not exist or has been moved.</p>
    <a href="<?= View::url('/') ?>" class="mt-8 inline-flex items-center rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white font-semibold px-7 py-3 shadow-lg shadow-brand-500/30 hover:shadow-xl transition">
      Back to Home
    </a>
  </div>
</section>
