<?php

use App\Core\View;

/** @var array $testimonials */
?>
<div class="flex justify-end mb-5">
  <button type="button" onclick="openTestimonialModal()" class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30">+ Add Testimonial</button>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
  <?php foreach ($testimonials as $t): ?>
    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
      <div class="text-brand-500 mb-2"><?= str_repeat('&#9733;', (int) $t['rating']) ?></div>
      <p class="text-sm text-slate-600">&ldquo;<?= View::e(mb_substr($t['content'], 0, 120)) ?><?= mb_strlen($t['content']) > 120 ? '…' : '' ?>&rdquo;</p>
      <div class="mt-3 text-sm font-medium text-slate-900"><?= View::e($t['name']) ?></div>
      <div class="text-xs text-slate-400"><?= View::e($t['designation']) ?><?= $t['company'] ? ', ' . View::e($t['company']) : '' ?></div>
      <div class="mt-3 flex gap-3 text-xs">
        <button type="button" onclick='openTestimonialModal(<?= json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-brand-600 hover:underline">Edit</button>
        <form action="<?= View::url('admin/testimonials/' . $t['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this testimonial?');">
          <?= View::csrfField() ?>
          <button class="text-red-600 hover:underline">Delete</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php if ($testimonials === []): ?><p class="text-sm text-slate-400 mt-6">No testimonials yet.</p><?php endif; ?>

<div id="testimonial-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md">
    <h3 id="testimonial-modal-title" class="font-semibold text-slate-900 mb-4">Add Testimonial</h3>
    <form id="testimonial-form" action="<?= View::url('admin/testimonials') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" id="t-name" name="name" placeholder="Name" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <div class="grid grid-cols-2 gap-3">
        <input type="text" id="t-designation" name="designation" placeholder="Designation" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <input type="text" id="t-company" name="company" placeholder="Company" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      </div>
      <textarea id="t-content" name="content" placeholder="Testimonial text" rows="3" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></textarea>
      <div class="grid grid-cols-3 gap-3">
        <select id="t-rating" name="rating" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= $i ?> stars</option><?php endfor; ?>
        </select>
        <input type="number" id="t-sort" name="sort_order" placeholder="Sort" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <select id="t-status" name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="published">Published</option>
          <option value="draft">Draft</option>
        </select>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('testimonial-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const testimonialsBaseUrl = '<?= View::url('admin/testimonials') ?>';
function openTestimonialModal(t) {
  const form = document.getElementById('testimonial-form');
  if (t) {
    document.getElementById('testimonial-modal-title').textContent = 'Edit Testimonial';
    document.getElementById('t-name').value = t.name;
    document.getElementById('t-designation').value = t.designation || '';
    document.getElementById('t-company').value = t.company || '';
    document.getElementById('t-content').value = t.content;
    document.getElementById('t-rating').value = t.rating;
    document.getElementById('t-sort').value = t.sort_order;
    document.getElementById('t-status').value = t.status;
    form.action = testimonialsBaseUrl + '/' + t.id;
  } else {
    document.getElementById('testimonial-modal-title').textContent = 'Add Testimonial';
    form.reset();
    form.action = testimonialsBaseUrl;
  }
  document.getElementById('testimonial-modal').classList.remove('hidden');
}
</script>
