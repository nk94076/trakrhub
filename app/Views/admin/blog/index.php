<?php

use App\Core\View;

/** @var array $posts */
/** @var bool $showTrash */
/** @var string $search */
?>
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
  <div class="flex items-center gap-4">
    <a href="<?= View::url('admin/blog' . ($showTrash ? '' : '?trash=1')) ?>" class="text-sm text-slate-500 hover:underline">
      <?= $showTrash ? '&larr; Back to posts' : '&#128465; View trash' ?>
    </a>
    <a href="<?= View::url('admin/blog/taxonomy') ?>" class="text-sm text-slate-500 hover:underline">Categories, Tags & Authors</a>
  </div>
  <a href="<?= View::url('admin/blog/create') ?>" class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30">+ New Post</a>
</div>

<form method="GET" class="mb-5">
  <?php if ($showTrash): ?><input type="hidden" name="trash" value="1"><?php endif; ?>
  <input type="text" name="q" value="<?= View::e($search) ?>" placeholder="Search posts..." class="rounded-lg border border-slate-200 px-4 py-2 text-sm w-72">
</form>

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
      <tr><th class="text-left px-5 py-3">Title</th><th class="text-left px-5 py-3">Category</th><th class="text-left px-5 py-3">Status</th><th class="text-left px-5 py-3">Views</th><th class="text-right px-5 py-3">Actions</th></tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      <?php foreach ($posts as $post): ?>
        <tr>
          <td class="px-5 py-3 font-medium text-slate-800"><?= View::e($post['title']) ?></td>
          <td class="px-5 py-3 text-slate-500"><?= View::e($post['category_name'] ?? '—') ?></td>
          <td class="px-5 py-3">
            <span class="text-xs rounded-full px-2.5 py-1 <?= $post['status'] === 'published' ? 'bg-green-50 text-green-700' : ($post['status'] === 'scheduled' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-500') ?>">
              <?= ucfirst($post['status']) ?>
            </span>
          </td>
          <td class="px-5 py-3 text-slate-400"><?= (int) $post['views'] ?></td>
          <td class="px-5 py-3 text-right text-xs space-x-3">
            <?php if ($showTrash): ?>
              <form action="<?= View::url('admin/blog/' . $post['id'] . '/restore') ?>" method="POST" class="inline"><?= View::csrfField() ?>
                <button class="text-green-600 hover:underline">Restore</button>
              </form>
            <?php else: ?>
              <a href="<?= View::url('admin/blog/' . $post['id'] . '/edit') ?>" class="text-brand-600 hover:underline">Edit</a>
              <?php if ($post['status'] === 'published'): ?>
                <a href="<?= View::url('blog/' . $post['slug']) ?>" target="_blank" class="text-slate-500 hover:underline">View</a>
              <?php endif; ?>
              <form action="<?= View::url('admin/blog/' . $post['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Move this post to trash?');">
                <?= View::csrfField() ?>
                <button class="text-red-600 hover:underline">Trash</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if ($posts === []): ?><p class="text-center text-sm text-slate-400 py-10">No posts yet.</p><?php endif; ?>
</div>
