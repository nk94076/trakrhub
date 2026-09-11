<?php

use App\Core\Icon;
use App\Core\View;

/** @var array $industries */
/** @var array $iconKeys */
?>
<div class="flex justify-end mb-5">
  <button type="button" onclick="openIndustryModal()" class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30">+ Add Industry</button>
</div>

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
      <tr><th class="text-left px-5 py-3">Icon</th><th class="text-left px-5 py-3">Title</th><th class="text-left px-5 py-3">Slug</th><th class="text-left px-5 py-3">Status</th><th class="text-right px-5 py-3">Actions</th></tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      <?php foreach ($industries as $industry): ?>
        <tr>
          <td class="px-5 py-3">
            <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center"><?= Icon::render($industry['icon'] ?? null, 'w-4 h-4') ?></div>
          </td>
          <td class="px-5 py-3 font-medium text-slate-800"><?= View::e($industry['title']) ?></td>
          <td class="px-5 py-3 text-slate-500">/<?= View::e($industry['slug']) ?></td>
          <td class="px-5 py-3"><span class="text-xs rounded-full px-2.5 py-1 <?= $industry['status'] === 'published' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= ucfirst($industry['status']) ?></span></td>
          <td class="px-5 py-3 text-right text-xs space-x-3">
            <button type="button" onclick='openIndustryModal(<?= json_encode($industry, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-brand-600 hover:underline">Edit</button>
            <form action="<?= View::url('admin/industries/' . $industry['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Trash this industry?');">
              <?= View::csrfField() ?>
              <button class="text-red-600 hover:underline">Trash</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if ($industries === []): ?><p class="text-center text-sm text-slate-400 py-10">No industries yet.</p><?php endif; ?>
</div>

<div id="industry-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md">
    <h3 id="industry-modal-title" class="font-semibold text-slate-900 mb-4">Add Industry</h3>
    <form id="industry-form" action="<?= View::url('admin/industries') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" id="industry-title" name="title" placeholder="Title" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <input type="text" id="industry-slug" name="slug" placeholder="Slug (auto if blank)" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Icon</label>
        <select id="industry-icon" name="icon" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <?php foreach ($iconKeys as $key): ?><option value="<?= $key ?>"><?= ucwords(str_replace('-', ' ', $key)) ?></option><?php endforeach; ?>
        </select>
      </div>
      <textarea id="industry-description" name="description" placeholder="Description" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></textarea>
      <div class="grid grid-cols-2 gap-3">
        <input type="number" id="industry-sort" name="sort_order" placeholder="Sort order" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <select id="industry-status" name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="published">Published</option>
          <option value="draft">Draft</option>
        </select>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('industry-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const industriesBaseUrl = '<?= View::url('admin/industries') ?>';
function openIndustryModal(i) {
  const form = document.getElementById('industry-form');
  if (i) {
    document.getElementById('industry-modal-title').textContent = 'Edit Industry';
    document.getElementById('industry-title').value = i.title;
    document.getElementById('industry-slug').value = i.slug;
    document.getElementById('industry-icon').value = i.icon || '';
    document.getElementById('industry-description').value = i.description || '';
    document.getElementById('industry-sort').value = i.sort_order;
    document.getElementById('industry-status').value = i.status;
    form.action = industriesBaseUrl + '/' + i.id;
  } else {
    document.getElementById('industry-modal-title').textContent = 'Add Industry';
    form.reset();
    form.action = industriesBaseUrl;
  }
  document.getElementById('industry-modal').classList.remove('hidden');
}
</script>
