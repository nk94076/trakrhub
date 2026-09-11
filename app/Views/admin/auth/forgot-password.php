<?php

use App\Core\View;
?>
<h1 class="text-xl font-bold text-slate-900 mb-2">Forgot your password?</h1>
<p class="text-sm text-slate-500 mb-6">Enter your email and we'll send you a reset link.</p>
<form action="<?= View::url('admin/forgot-password') ?>" method="POST" class="space-y-4">
  <?= View::csrfField() ?>
  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
    <input type="email" name="email" required autofocus
           class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
  </div>
  <button type="submit"
          class="w-full rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white font-semibold py-2.5 shadow-lg shadow-brand-500/30 hover:shadow-xl transition">
    Send Reset Link
  </button>
</form>
<div class="mt-4 text-center text-sm">
  <a href="<?= View::url('admin/login') ?>" class="text-brand-600 hover:underline">Back to login</a>
</div>
