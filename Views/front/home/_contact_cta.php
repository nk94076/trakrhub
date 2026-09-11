<?php

use App\Core\Icon;
use App\Core\Session;
use App\Core\View;

/** @var array $content */
$bullets = $content['bullets'] ?? ['No setup fees', 'Dedicated account manager', 'Live in 48 hours'];
$leadType = in_array($content['lead_type'] ?? '', ['advertiser', 'publisher', 'general'], true) ? $content['lead_type'] : 'general';
$formId = 'cta-form-' . substr(md5(json_encode($content)), 0, 6);
?>
<section class="py-20">
  <div class="max-w-6xl mx-auto px-6">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 via-brand-600 to-accent-600 shadow-2xl">
      <div class="absolute -top-24 -right-24 w-72 h-72 bg-white/10 rounded-full"></div>
      <div class="absolute -bottom-16 -left-16 w-56 h-56 bg-white/10 rounded-full"></div>

      <div class="relative grid lg:grid-cols-2 gap-10 items-center px-8 py-14 lg:px-14 lg:py-16">
        <div class="text-center lg:text-left">
          <h2 class="text-3xl md:text-4xl font-extrabold text-white"><?= View::e($content['title'] ?? '') ?></h2>
          <?php if (!empty($content['subtitle'])): ?>
            <p class="mt-4 text-white/80"><?= View::e($content['subtitle']) ?></p>
          <?php endif; ?>
          <?php if ($bullets !== []): ?>
            <ul class="mt-6 space-y-2.5 inline-block text-left">
              <?php foreach ($bullets as $bullet): ?>
                <li class="flex items-center gap-2.5 text-sm text-white/90">
                  <span class="w-5 h-5 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                    <?= Icon::render('check-circle', 'w-3.5 h-3.5') ?>
                  </span>
                  <?= View::e($bullet) ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <div class="rounded-2xl bg-white shadow-xl p-6 sm:p-7">
          <?php if ($success = Session::getFlash('success')): ?>
            <div class="mb-4 rounded-lg bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3"><?= View::e($success) ?></div>
          <?php endif; ?>
          <?php if ($error = Session::getFlash('error')): ?>
            <div class="mb-4 rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3"><?= View::e($error) ?></div>
          <?php endif; ?>

          <form action="<?= View::url('contact/submit') ?>" method="POST" class="space-y-3">
            <?= View::csrfField() ?>
            <input type="hidden" name="lead_type" value="<?= View::e($leadType) ?>">
            <input type="hidden" name="message" value="<?= View::e($content['form_message'] ?? 'Requested more information via the homepage call-to-action.') ?>">
            <!-- Honeypot field — hidden from real users, bots tend to fill every input. -->
            <div class="hidden" aria-hidden="true">
              <label for="<?= $formId ?>-website">Leave this field empty</label>
              <input type="text" id="<?= $formId ?>-website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <input type="text" name="name" placeholder="Full name" required
                   class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
            <input type="email" name="email" placeholder="Work email" required
                   class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
            <input type="text" name="company" placeholder="Company (optional)"
                   class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
            <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white font-semibold py-3 shadow-lg shadow-brand-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
              <?= View::e($content['button_text'] ?? 'Get Started') ?>
            </button>
            <p class="text-center text-xs text-slate-400">No spam. We'll respond within one business day.</p>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
