<?php

use App\Core\View;

/** @var array|null $post */
/** @var array $categories */
/** @var array $authors */
/** @var array $postTags */
/** @var array|null $seo */

$isEdit = $post !== null;
$action = $isEdit ? View::url('admin/blog/' . $post['id']) : View::url('admin/blog');
?>
<form action="<?= $action ?>" method="POST" class="grid lg:grid-cols-[1fr_320px] gap-6">
  <?= View::csrfField() ?>

  <div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 space-y-4">
      <input type="text" name="title" value="<?= View::e($post['title'] ?? '') ?>" placeholder="Post title" required
             class="w-full text-xl font-semibold rounded-lg border border-slate-200 px-4 py-3">
      <input type="text" name="slug" value="<?= View::e($post['slug'] ?? '') ?>" placeholder="URL slug (auto-generated if blank)"
             class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm">
      <textarea name="excerpt" placeholder="Short excerpt (used in listings and meta description)" rows="2"
                class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm"><?= View::e($post['excerpt'] ?? '') ?></textarea>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Featured Image</label>
        <div class="flex items-center gap-2">
          <input type="text" id="featured-image-input" name="featured_image" placeholder="No image selected"
                 value="<?= View::e($post['featured_image'] ?? '') ?>"
                 class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <button type="button" onclick="openMediaPicker((path) => { document.getElementById('featured-image-input').value = path; })"
                  class="shrink-0 rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50">Browse&hellip;</button>
        </div>
      </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
      <label class="block text-sm font-medium text-slate-700 mb-2">Content</label>
      <p class="text-xs text-slate-400 mb-3">
        Use Heading 2/3 for section headings — they automatically appear in the post's table of contents.
      </p>
      <div id="content-editor" style="min-height: 400px;"></div>
      <textarea name="content" id="content-raw" required class="hidden"><?= View::e($post['content'] ?? '') ?></textarea>
    </div>

    <?php View::partial('admin.partials._seo_fields', ['seo' => $seo]); ?>
  </div>

  <div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 space-y-4">
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
        <select name="status" id="post-status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
          <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
          <option value="scheduled" <?= ($post['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
        </select>
      </div>
      <div id="scheduled-at-field" class="<?= ($post['status'] ?? '') === 'scheduled' ? '' : 'hidden' ?>">
        <label class="block text-xs font-medium text-slate-600 mb-1">Publish at</label>
        <input type="datetime-local" name="scheduled_at" value="<?= View::e($post['scheduled_at'] ? str_replace(' ', 'T', substr($post['scheduled_at'], 0, 16)) : '') ?>"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
        <select name="category_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="">None</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int) $cat['id'] ?>" <?= ($post['category_id'] ?? null) == $cat['id'] ? 'selected' : '' ?>><?= View::e($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Author</label>
        <select name="author_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="">None</option>
          <?php foreach ($authors as $author): ?>
            <option value="<?= (int) $author['id'] ?>" <?= ($post['author_id'] ?? null) == $author['id'] ? 'selected' : '' ?>><?= View::e($author['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Tags (comma separated)</label>
        <input type="text" name="tags" value="<?= View::e(implode(', ', $postTags)) ?>" placeholder="marketing, tracking, fraud"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      </div>
      <?php if ($isEdit): ?>
        <p class="text-xs text-slate-400">Reading time: <?= (int) $post['reading_time_minutes'] ?> min &middot; Views: <?= (int) $post['views'] ?></p>
      <?php endif; ?>
      <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save Post</button>
      <a href="<?= View::url('admin/blog') ?>" class="block text-center text-xs text-slate-400 hover:text-slate-600">Cancel</a>
    </div>
  </div>
</form>

<script>
document.getElementById('post-status').addEventListener('change', function () {
  document.getElementById('scheduled-at-field').classList.toggle('hidden', this.value !== 'scheduled');
});
</script>

<link rel="stylesheet" href="<?= View::e(View::asset('vendor/quill/quill.snow.css')) ?>">
<script src="<?= View::e(View::asset('vendor/quill/quill.min.js')) ?>"></script>
<script>
(function () {
  var rawField = document.getElementById('content-raw');

  var quill = new Quill('#content-editor', {
    theme: 'snow',
    modules: {
      toolbar: {
        container: [
          [{ header: [2, 3, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          ['blockquote', 'code-block'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['link', 'image'],
          ['clean'],
        ],
        handlers: {
          image: function () {
            var range = quill.getSelection(true);
            openMediaPicker(function (path) {
              quill.insertEmbed(range.index, 'image', path, 'user');
              quill.setSelection(range.index + 1);
            });
          },
        },
      },
    },
  });

  if (rawField.value) {
    quill.clipboard.dangerouslyPasteHTML(rawField.value);
  }

  document.querySelector('form').addEventListener('submit', function () {
    rawField.value = quill.root.innerHTML;
  });
})();
</script>
