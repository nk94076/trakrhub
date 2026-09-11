<?php

use App\Core\View;

/** @var array $categories */
/** @var array $tags */
/** @var array $authors */
?>
<div class="mb-6">
  <a href="<?= View::url('admin/blog') ?>" class="text-sm text-slate-500 hover:underline">&larr; Back to posts</a>
</div>

<div class="grid md:grid-cols-3 gap-6">
  <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
    <h3 class="font-semibold text-slate-900 mb-4">Categories</h3>
    <ul class="space-y-2 mb-4">
      <?php foreach ($categories as $cat): ?>
        <li class="flex items-center justify-between text-sm">
          <span><?= View::e($cat['name']) ?></span>
          <form action="<?= View::url('admin/blog/taxonomy/categories/' . $cat['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this category?');">
            <?= View::csrfField() ?>
            <button class="text-red-600 hover:underline text-xs">Delete</button>
          </form>
        </li>
      <?php endforeach; ?>
      <?php if ($categories === []): ?><li class="text-xs text-slate-400">None yet.</li><?php endif; ?>
    </ul>
    <form action="<?= View::url('admin/blog/taxonomy/categories') ?>" method="POST" class="flex gap-2">
      <?= View::csrfField() ?>
      <input type="text" name="name" placeholder="New category" required class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <button class="rounded-lg bg-slate-900 text-white text-sm font-medium px-3">Add</button>
    </form>
  </div>

  <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
    <h3 class="font-semibold text-slate-900 mb-4">Tags</h3>
    <ul class="space-y-2 mb-4">
      <?php foreach ($tags as $tag): ?>
        <li class="flex items-center justify-between text-sm">
          <span><?= View::e($tag['name']) ?></span>
          <form action="<?= View::url('admin/blog/taxonomy/tags/' . $tag['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this tag?');">
            <?= View::csrfField() ?>
            <button class="text-red-600 hover:underline text-xs">Delete</button>
          </form>
        </li>
      <?php endforeach; ?>
      <?php if ($tags === []): ?><li class="text-xs text-slate-400">None yet.</li><?php endif; ?>
    </ul>
    <form action="<?= View::url('admin/blog/taxonomy/tags') ?>" method="POST" class="flex gap-2">
      <?= View::csrfField() ?>
      <input type="text" name="name" placeholder="New tag" required class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <button class="rounded-lg bg-slate-900 text-white text-sm font-medium px-3">Add</button>
    </form>
  </div>

  <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
    <h3 class="font-semibold text-slate-900 mb-4">Authors</h3>
    <ul class="space-y-2 mb-4">
      <?php foreach ($authors as $author): ?>
        <li class="flex items-center justify-between text-sm">
          <span><?= View::e($author['name']) ?></span>
          <form action="<?= View::url('admin/blog/taxonomy/authors/' . $author['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this author?');">
            <?= View::csrfField() ?>
            <button class="text-red-600 hover:underline text-xs">Delete</button>
          </form>
        </li>
      <?php endforeach; ?>
      <?php if ($authors === []): ?><li class="text-xs text-slate-400">None yet.</li><?php endif; ?>
    </ul>
    <form action="<?= View::url('admin/blog/taxonomy/authors') ?>" method="POST" class="space-y-2">
      <?= View::csrfField() ?>
      <input type="text" name="name" placeholder="Name" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <input type="text" name="job_title" placeholder="Job title" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <button class="w-full rounded-lg bg-slate-900 text-white text-sm font-medium py-2">Add Author</button>
    </form>
  </div>
</div>
