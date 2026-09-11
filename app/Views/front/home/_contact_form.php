<?php

use App\Core\View;

/** @var array $content */
?>
<section class="py-20">
  <div class="max-w-xl mx-auto px-6">
    <div class="text-center mb-10">
      <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900"><?= View::e($content['title'] ?? 'Get in Touch') ?></h2>
      <p class="mt-3 text-slate-600"><?= View::e($content['subtitle'] ?? "We'll get back to you within one business day.") ?></p>
    </div>

    <?php if ($success = \App\Core\Session::getFlash('success')): ?>
      <div class="mb-5 rounded-lg bg-green-50 border border-green-100 text-green-700 text-sm px-4 py-3"><?= View::e($success) ?></div>
    <?php endif; ?>
    <?php if ($error = \App\Core\Session::getFlash('error')): ?>
      <div class="mb-5 rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3"><?= View::e($error) ?></div>
    <?php endif; ?>

    <form action="<?= View::url('contact/submit') ?>" method="POST" class="space-y-4 rounded-2xl bg-white border border-slate-100 shadow-sm p-8">
      <?= View::csrfField() ?>
      <!-- Honeypot field — hidden from real users, bots tend to fill every input. -->
      <div class="hidden" aria-hidden="true">
        <label>Leave this field empty</label>
        <input type="text" name="website" tabindex="-1" autocomplete="off">
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <input type="text" name="name" placeholder="Full name" required class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
        <input type="email" name="email" placeholder="Email address" required class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
      </div>
      <div class="grid sm:grid-cols-2 gap-4">
        <input type="text" name="phone" placeholder="Phone (optional)" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
        <input type="text" name="company" placeholder="Company (optional)" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
      </div>
      <select name="lead_type" class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm">
        <option value="general">General Inquiry</option>
        <option value="advertiser">I'm an Advertiser</option>
        <option value="publisher">I'm a Publisher</option>
      </select>
      <textarea name="message" rows="4" placeholder="How can we help?" required class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400"></textarea>
      <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white font-semibold py-3 shadow-lg shadow-brand-500/30 hover:shadow-xl transition">
        Send Message
      </button>
    </form>
  </div>
</section>
