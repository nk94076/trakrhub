<?php

use App\Core\View;

/** @var array $content */
?>
<section class="py-16 bg-slate-50/60">
  <div class="max-w-2xl mx-auto px-6 text-center">
    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900"><?= View::e($content['title'] ?? '') ?></h2>
    <p class="mt-2 text-sm text-slate-500">Get performance marketing insights straight to your inbox.</p>
    <form action="<?= View::url('newsletter/subscribe') ?>" method="POST" class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
      <?= View::csrfField() ?>
      <input type="email" name="email" required placeholder="you@company.com"
             class="w-full sm:w-72 rounded-full border border-slate-200 px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
      <button type="submit"
              class="rounded-full bg-slate-900 text-white text-sm font-semibold px-6 py-3 hover:bg-slate-800 transition">
        Subscribe
      </button>
    </form>
  </div>
</section>
