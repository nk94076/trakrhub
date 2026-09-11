<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $statistics */
/** @var array $iconKeys */
?>
<div class="flex justify-end mb-5">
  <button type="button" onclick="openStatModal()" class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30">+ Add Statistic</button>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
  <?php foreach ($statistics as $stat): ?>
    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
      <?php if (!empty($stat['icon'])): ?>
        <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mb-3"><?= Icon::render($stat['icon'], 'w-4 h-4') ?></div>
      <?php endif; ?>
      <div class="text-2xl font-extrabold gradient-text"><?= View::e($stat['value']) ?><?= View::e($stat['suffix'] ?? '') ?></div>
      <div class="mt-1 text-sm text-slate-500"><?= View::e($stat['label']) ?></div>
      <div class="mt-3 flex gap-3 text-xs">
        <button type="button" onclick='openStatModal(<?= json_encode($stat, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-brand-600 hover:underline">Edit</button>
        <form action="<?= View::url('admin/statistics/' . $stat['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this statistic?');">
          <?= View::csrfField() ?>
          <button class="text-red-600 hover:underline">Delete</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php if ($statistics === []): ?><p class="text-sm text-slate-400 mt-6">No statistics yet.</p><?php endif; ?>

<div id="stat-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md">
    <h3 id="stat-modal-title" class="font-semibold text-slate-900 mb-4">Add Statistic</h3>
    <form id="stat-form" action="<?= View::url('admin/statistics') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" id="stat-label" name="label" placeholder="Label (e.g. Active Advertisers)" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <div class="grid grid-cols-2 gap-3">
        <input type="text" id="stat-value" name="value" placeholder="Value (e.g. 350)" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <input type="text" id="stat-suffix" name="suffix" placeholder="Suffix (e.g. +)" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Icon (optional)</label>
        <select id="stat-icon" name="icon" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="">None</option>
          <?php foreach ($iconKeys as $key): ?><option value="<?= $key ?>"><?= ucwords(str_replace('-', ' ', $key)) ?></option><?php endforeach; ?>
        </select>
      </div>
      <input type="number" id="stat-sort" name="sort_order" placeholder="Sort order" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <select id="stat-status" name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <option value="published">Published</option>
        <option value="draft">Draft</option>
      </select>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('stat-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const statsBaseUrl = '<?= View::url('admin/statistics') ?>';
function openStatModal(stat) {
  const form = document.getElementById('stat-form');
  if (stat) {
    document.getElementById('stat-modal-title').textContent = 'Edit Statistic';
    document.getElementById('stat-label').value = stat.label;
    document.getElementById('stat-value').value = stat.value;
    document.getElementById('stat-suffix').value = stat.suffix || '';
    document.getElementById('stat-icon').value = stat.icon || '';
    document.getElementById('stat-sort').value = stat.sort_order;
    document.getElementById('stat-status').value = stat.status;
    form.action = statsBaseUrl + '/' + stat.id;
  } else {
    document.getElementById('stat-modal-title').textContent = 'Add Statistic';
    form.reset();
    form.action = statsBaseUrl;
  }
  document.getElementById('stat-modal').classList.remove('hidden');
}
</script>
