<?php

use App\Core\View;

/** @var array $leads */
/** @var array $statuses */
/** @var string $activeStatus */
/** @var string $activeType */

$statusColors = [
    'new' => 'bg-blue-50 text-blue-700', 'contacted' => 'bg-amber-50 text-amber-700',
    'qualified' => 'bg-green-50 text-green-700', 'closed' => 'bg-slate-100 text-slate-500',
    'spam' => 'bg-red-50 text-red-700',
];
?>
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
  <form method="GET" class="flex flex-wrap gap-2">
    <a href="<?= View::url('admin/leads') ?>" class="px-3 py-1.5 rounded-full text-xs font-medium <?= $activeStatus === '' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600' ?>">All</a>
    <?php foreach ($statuses as $s): ?>
      <a href="<?= View::url('admin/leads?status=' . $s) ?>" class="px-3 py-1.5 rounded-full text-xs font-medium <?= $activeStatus === $s ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600' ?>"><?= ucfirst($s) ?></a>
    <?php endforeach; ?>
  </form>
  <a href="<?= View::url('admin/leads/export') ?>" class="rounded-full bg-slate-100 text-slate-700 text-sm font-medium px-5 py-2 hover:bg-slate-200 transition">Export CSV</a>
</div>

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
      <tr>
        <th class="text-left px-5 py-3">Contact</th>
        <th class="text-left px-5 py-3">Type</th>
        <th class="text-left px-5 py-3">Message</th>
        <th class="text-left px-5 py-3">Status</th>
        <th class="text-left px-5 py-3">Received</th>
        <th class="text-right px-5 py-3">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      <?php foreach ($leads as $lead): ?>
        <tr>
          <td class="px-5 py-3">
            <div class="font-medium text-slate-800"><?= View::e($lead['name']) ?></div>
            <div class="text-xs text-slate-400"><?= View::e($lead['email']) ?><?= $lead['phone'] ? ' · ' . View::e($lead['phone']) : '' ?></div>
          </td>
          <td class="px-5 py-3 text-slate-500 text-xs"><?= View::e(ucfirst($lead['lead_type'])) ?></td>
          <td class="px-5 py-3 text-slate-600 text-xs max-w-xs truncate" title="<?= View::e($lead['message'] ?? '') ?>"><?= View::e($lead['message'] ?? '') ?></td>
          <td class="px-5 py-3">
            <form action="<?= View::url('admin/leads/' . $lead['id'] . '/status') ?>" method="POST" class="inline">
              <?= View::csrfField() ?>
              <select name="status" onchange="this.form.submit()" class="text-xs rounded-full px-2.5 py-1 border-0 <?= $statusColors[$lead['status']] ?? 'bg-slate-100' ?>">
                <?php foreach ($statuses as $s): ?>
                  <option value="<?= $s ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </td>
          <td class="px-5 py-3 text-slate-400 text-xs"><?= View::e($lead['created_at']) ?></td>
          <td class="px-5 py-3 text-right text-xs">
            <form action="<?= View::url('admin/leads/' . $lead['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this lead?');">
              <?= View::csrfField() ?>
              <button class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if ($leads === []): ?>
    <p class="text-center text-sm text-slate-400 py-10">No leads yet.</p>
  <?php endif; ?>
</div>
