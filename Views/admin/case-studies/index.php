<?php

use App\Core\View;

/** @var array $caseStudies */
/** @var array $industries */
?>
<div class="flex justify-end mb-5">
  <button type="button" onclick="openCaseModal()" class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30">+ Add Case Study</button>
</div>

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
      <tr><th class="text-left px-5 py-3">Title</th><th class="text-left px-5 py-3">Slug</th><th class="text-left px-5 py-3">Status</th><th class="text-right px-5 py-3">Actions</th></tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      <?php foreach ($caseStudies as $case): ?>
        <tr>
          <td class="px-5 py-3 font-medium text-slate-800"><?= View::e($case['title']) ?></td>
          <td class="px-5 py-3 text-slate-500">/case-studies/<?= View::e($case['slug']) ?></td>
          <td class="px-5 py-3"><span class="text-xs rounded-full px-2.5 py-1 <?= $case['status'] === 'published' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= ucfirst($case['status']) ?></span></td>
          <td class="px-5 py-3 text-right text-xs space-x-3">
            <button type="button" onclick='openCaseModal(<?= json_encode($case, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-brand-600 hover:underline">Edit</button>
            <form action="<?= View::url('admin/case-studies/' . $case['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Trash this case study?');">
              <?= View::csrfField() ?>
              <button class="text-red-600 hover:underline">Trash</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if ($caseStudies === []): ?><p class="text-center text-sm text-slate-400 py-10">No case studies yet.</p><?php endif; ?>
</div>

<div id="case-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4 overflow-y-auto">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg my-8">
    <h3 id="case-modal-title" class="font-semibold text-slate-900 mb-4">Add Case Study</h3>
    <form id="case-form" action="<?= View::url('admin/case-studies') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" id="case-title" name="title" placeholder="Title" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <input type="text" id="case-slug" name="slug" placeholder="Slug (auto if blank)" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <select id="case-industry" name="industry_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <option value="">No industry</option>
        <?php foreach ($industries as $industry): ?>
          <option value="<?= (int) $industry['id'] ?>"><?= View::e($industry['title']) ?></option>
        <?php endforeach; ?>
      </select>
      <textarea id="case-summary" name="summary" placeholder="Summary" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></textarea>
      <textarea id="case-content" name="content" placeholder="Full write-up" rows="4" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></textarea>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Metrics (JSON array)</label>
        <textarea id="case-metrics" name="metrics" rows="4" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs font-mono">[
  {"label": "Conversion Growth", "value": "+160%"}
]</textarea>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <input type="number" id="case-sort" name="sort_order" placeholder="Sort order" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <select id="case-status" name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="published">Published</option>
          <option value="draft">Draft</option>
        </select>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('case-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const casesBaseUrl = '<?= View::url('admin/case-studies') ?>';
function openCaseModal(c) {
  const form = document.getElementById('case-form');
  if (c) {
    document.getElementById('case-modal-title').textContent = 'Edit Case Study';
    document.getElementById('case-title').value = c.title;
    document.getElementById('case-slug').value = c.slug;
    document.getElementById('case-industry').value = c.industry_id || '';
    document.getElementById('case-summary').value = c.summary || '';
    document.getElementById('case-content').value = c.content || '';
    document.getElementById('case-metrics').value = JSON.stringify(JSON.parse(c.metrics || '[]'), null, 2);
    document.getElementById('case-sort').value = c.sort_order;
    document.getElementById('case-status').value = c.status;
    form.action = casesBaseUrl + '/' + c.id;
  } else {
    document.getElementById('case-modal-title').textContent = 'Add Case Study';
    form.reset();
    form.action = casesBaseUrl;
  }
  document.getElementById('case-modal').classList.remove('hidden');
}
</script>
