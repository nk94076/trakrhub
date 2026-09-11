<?php

use App\Core\View;

/** @var string $token */
?>
<h1 class="text-xl font-bold text-slate-900 mb-6">Set a new password</h1>
<form action="<?= View::url('admin/reset-password/' . $token) ?>" method="POST" class="space-y-4">
  <?= View::csrfField() ?>
  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1">New password</label>
    <input type="password" name="password" required minlength="8"
           class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
  </div>
  <div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Confirm password</label>
    <input type="password" name="password_confirmation" required minlength="8"
           class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
  </div>
  <button type="submit"
          class="w-full rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white font-semibold py-2.5 shadow-lg shadow-brand-500/30 hover:shadow-xl transition">
    Reset Password
  </button>
</form>
