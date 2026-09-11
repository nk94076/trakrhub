<?php

use App\Core\View;

/** @var array $faqs */
?>
<div class="flex justify-end mb-5">
  <button type="button" onclick="openFaqModal()" class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30">+ Add FAQ</button>
</div>

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm divide-y divide-slate-50">
  <?php foreach ($faqs as $faq): ?>
    <div class="p-5 flex items-start justify-between gap-4">
      <div>
        <div class="text-[10px] uppercase tracking-wide text-brand-600 bg-brand-50 inline-block rounded-full px-2 py-0.5 mb-1"><?= View::e($faq['group']) ?></div>
        <div class="font-medium text-slate-800"><?= View::e($faq['question']) ?></div>
        <div class="text-sm text-slate-500 mt-1"><?= View::e($faq['answer']) ?></div>
      </div>
      <div class="flex gap-3 text-xs whitespace-nowrap">
        <span class="text-[10px] rounded-full px-2 py-0.5 <?= $faq['status'] === 'published' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= $faq['status'] ?></span>
        <button type="button" onclick='openFaqModal(<?= json_encode($faq, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-brand-600 hover:underline">Edit</button>
        <form action="<?= View::url('admin/faqs/' . $faq['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this FAQ?');">
          <?= View::csrfField() ?>
          <button class="text-red-600 hover:underline">Delete</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if ($faqs === []): ?><p class="text-center text-sm text-slate-400 py-10">No FAQs yet.</p><?php endif; ?>
</div>

<div id="faq-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg">
    <h3 id="faq-modal-title" class="font-semibold text-slate-900 mb-4">Add FAQ</h3>
    <form id="faq-form" action="<?= View::url('admin/faqs') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" id="faq-group" name="group" placeholder="Group (e.g. general, publishers)" value="general" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <input type="text" id="faq-question" name="question" placeholder="Question" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <textarea id="faq-answer" name="answer" placeholder="Answer" rows="4" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></textarea>
      <div class="grid grid-cols-2 gap-3">
        <input type="number" id="faq-sort" name="sort_order" placeholder="Sort order" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <select id="faq-status" name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="published">Published</option>
          <option value="draft">Draft</option>
        </select>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('faq-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const faqsBaseUrl = '<?= View::url('admin/faqs') ?>';
function openFaqModal(faq) {
  const form = document.getElementById('faq-form');
  if (faq) {
    document.getElementById('faq-modal-title').textContent = 'Edit FAQ';
    document.getElementById('faq-group').value = faq.group;
    document.getElementById('faq-question').value = faq.question;
    document.getElementById('faq-answer').value = faq.answer;
    document.getElementById('faq-sort').value = faq.sort_order;
    document.getElementById('faq-status').value = faq.status;
    form.action = faqsBaseUrl + '/' + faq.id;
  } else {
    document.getElementById('faq-modal-title').textContent = 'Add FAQ';
    form.reset();
    document.getElementById('faq-group').value = 'general';
    form.action = faqsBaseUrl;
  }
  document.getElementById('faq-modal').classList.remove('hidden');
}
</script>
