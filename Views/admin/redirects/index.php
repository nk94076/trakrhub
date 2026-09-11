<?php

use App\Core\View;

/** @var array $redirects */
?>
<div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 mb-6 max-w-xl">
  <h3 class="font-semibold text-slate-900 mb-4">Add Redirect</h3>
  <form action="<?= View::url('admin/redirects') ?>" method="POST" class="grid sm:grid-cols-2 gap-3">
    <?= View::csrfField() ?>
    <input type="text" name="from_path" placeholder="/old-path" required class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
    <input type="text" name="to_path" placeholder="/new-path" required class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
    <select name="status_code" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <option value="301">301 Permanent</option>
      <option value="302">302 Temporary</option>
      <option value="307">307 Temporary (method preserved)</option>
      <option value="308">308 Permanent (method preserved)</option>
    </select>
    <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2">Add Redirect</button>
  </form>
</div>

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
      <tr><th class="text-left px-5 py-3">From</th><th class="text-left px-5 py-3">To</th><th class="text-left px-5 py-3">Status</th><th class="text-left px-5 py-3">Hits</th><th class="text-right px-5 py-3">Actions</th></tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      <?php foreach ($redirects as $r): ?>
        <tr>
          <td class="px-5 py-3 font-mono text-xs text-slate-700"><?= View::e($r['from_path']) ?></td>
          <td class="px-5 py-3 font-mono text-xs text-slate-700"><?= View::e($r['to_path']) ?></td>
          <td class="px-5 py-3 text-slate-500"><?= (int) $r['status_code'] ?></td>
          <td class="px-5 py-3 text-slate-400"><?= (int) $r['hits'] ?></td>
          <td class="px-5 py-3 text-right text-xs">
            <form action="<?= View::url('admin/redirects/' . $r['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this redirect?');">
              <?= View::csrfField() ?>
              <button class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if ($redirects === []): ?><p class="text-center text-sm text-slate-400 py-10">No redirects yet.</p><?php endif; ?>
</div>
