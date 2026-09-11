<?php

use App\Core\View;

/** @var array $pages */
/** @var bool $showTrash */
?>
<div class="flex justify-between items-center mb-5">
  <a href="<?= View::url('admin/pages' . ($showTrash ? '' : '?trash=1')) ?>" class="text-sm text-slate-500 hover:underline">
    <?= $showTrash ? '&larr; Back to pages' : '&#128465; View trash' ?>
  </a>
  <button type="button" onclick="document.getElementById('create-page-modal').classList.remove('hidden')"
          class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30">
    + Add Page
  </button>
</div>

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
      <tr>
        <th class="text-left px-5 py-3">Title</th>
        <th class="text-left px-5 py-3">Slug</th>
        <th class="text-left px-5 py-3">Status</th>
        <th class="text-right px-5 py-3">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      <?php foreach ($pages as $page): ?>
        <tr>
          <td class="px-5 py-3 font-medium text-slate-800">
            <?= View::e($page['title']) ?>
            <?php if ($page['is_system']): ?><span class="text-[10px] uppercase tracking-wide text-brand-600 bg-brand-50 rounded-full px-2 py-0.5 ml-2">System</span><?php endif; ?>
          </td>
          <td class="px-5 py-3 text-slate-500">/<?= View::e($page['slug']) ?></td>
          <td class="px-5 py-3">
            <?php if ($page['deleted_at']): ?>
              <span class="text-xs rounded-full px-2.5 py-1 bg-red-50 text-red-600">Trashed</span>
            <?php else: ?>
              <span class="text-xs rounded-full px-2.5 py-1 <?= $page['status'] === 'published' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' ?>">
                <?= View::e(ucfirst($page['status'])) ?>
              </span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3 text-right text-xs space-x-3">
            <?php if ($page['deleted_at']): ?>
              <form action="<?= View::url('admin/pages/' . $page['id'] . '/restore') ?>" method="POST" class="inline"><?= View::csrfField() ?>
                <button class="text-green-600 hover:underline">Restore</button>
              </form>
            <?php else: ?>
              <a href="<?= View::url('admin/pages/' . $page['id'] . '/edit') ?>" class="text-brand-600 hover:underline">Edit</a>
              <?php if ($page['status'] === 'published'): ?>
                <a href="<?= View::url($page['slug'] === 'home' ? '/' : $page['slug']) ?>" target="_blank" class="text-slate-500 hover:underline">Preview</a>
              <?php endif; ?>
              <form action="<?= View::url('admin/pages/' . $page['id'] . '/duplicate') ?>" method="POST" class="inline"><?= View::csrfField() ?>
                <button class="text-slate-500 hover:underline">Duplicate</button>
              </form>
              <?php if (!$page['is_system']): ?>
                <form action="<?= View::url('admin/pages/' . $page['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Move this page to trash?');">
                  <?= View::csrfField() ?>
                  <button class="text-red-600 hover:underline">Trash</button>
                </form>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div id="create-page-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-900 mb-4">Add Page</h3>
    <form action="<?= View::url('admin/pages') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" name="title" placeholder="Page title" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <input type="text" name="slug" placeholder="URL slug (auto-generated if blank)" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('create-page-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Create</button>
      </div>
    </form>
  </div>
</div>
