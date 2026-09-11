<?php

use App\Core\Csrf;
use App\Core\View;

/** @var array $media */
/** @var array $folders */
/** @var int|null $currentFolder */
/** @var string $search */
/** @var string $type */
/** @var bool $showTrash */

$csrfToken = Csrf::token();
?>
<div x-data="mediaManager('<?= View::e(View::url('admin/media/upload')) ?>', '<?= View::e($csrfToken) ?>', <?= (int) ($currentFolder ?? 0) ?>)" class="grid lg:grid-cols-[220px_1fr] gap-6">

  <!-- Folder sidebar -->
  <aside class="rounded-2xl bg-white border border-slate-100 shadow-sm p-4 h-fit">
    <h3 class="text-xs font-semibold uppercase text-slate-400 mb-3">Folders</h3>
    <nav class="space-y-1">
      <a href="<?= View::url('admin/media') ?>" class="block px-3 py-2 rounded-lg text-sm <?= $currentFolder === null && !$showTrash ? 'bg-brand-50 text-brand-700 font-medium' : 'text-slate-600 hover:bg-slate-50' ?>">All Files</a>
      <?php foreach ($folders as $folder): ?>
        <a href="<?= View::url('admin/media?folder=' . $folder['id']) ?>"
           class="block px-3 py-2 rounded-lg text-sm <?= $currentFolder === (int) $folder['id'] ? 'bg-brand-50 text-brand-700 font-medium' : 'text-slate-600 hover:bg-slate-50' ?>">
          <?= View::e($folder['name']) ?>
        </a>
      <?php endforeach; ?>
      <a href="<?= View::url('admin/media?trash=1') ?>" class="block px-3 py-2 rounded-lg text-sm <?= $showTrash ? 'bg-red-50 text-red-700 font-medium' : 'text-slate-500 hover:bg-slate-50' ?>">&#128465; Trash</a>
    </nav>
    <form action="<?= View::url('admin/media/folders') ?>" method="POST" class="mt-4 pt-4 border-t border-slate-100 space-y-2">
      <?= View::csrfField() ?>
      <input type="text" name="name" placeholder="New folder name" required
             class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
      <button type="submit" class="w-full rounded-lg bg-slate-900 text-white text-sm font-medium py-2 hover:bg-slate-800 transition">+ Add Folder</button>
    </form>
  </aside>

  <!-- Main panel -->
  <div>
    <?php if (!$showTrash): ?>
    <div @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="handleDrop($event)"
         :class="dragging ? 'border-brand-400 bg-brand-50' : 'border-slate-200 bg-white'"
         class="rounded-2xl border-2 border-dashed p-8 text-center transition mb-6">
      <p class="text-sm text-slate-500 mb-3">Drag &amp; drop files here, or</p>
      <label class="inline-flex items-center rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 cursor-pointer shadow-lg shadow-brand-500/20 hover:shadow-xl transition">
        Browse Files
        <input type="file" class="hidden" multiple @change="handleInput($event)"
               accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml,video/mp4,video/webm,application/pdf">
      </label>
      <template x-for="item in queue" :key="item.id">
        <div class="mt-3 text-xs text-slate-500" x-text="item.name + ' — ' + item.status"></div>
      </template>
    </div>
    <?php endif; ?>

    <form method="GET" action="<?= View::url('admin/media') ?>" class="flex flex-wrap gap-3 mb-5">
      <?php if ($showTrash): ?><input type="hidden" name="trash" value="1"><?php endif; ?>
      <?php if ($currentFolder !== null): ?><input type="hidden" name="folder" value="<?= (int) $currentFolder ?>"><?php endif; ?>
      <input type="text" name="q" value="<?= View::e($search) ?>" placeholder="Search files..."
             class="rounded-lg border border-slate-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-400">
      <select name="type" class="rounded-lg border border-slate-200 px-4 py-2 text-sm">
        <option value="">All types</option>
        <?php foreach (['image', 'video', 'svg', 'document'] as $t): ?>
          <option value="<?= $t ?>" <?= $type === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="rounded-lg bg-slate-100 text-slate-700 text-sm font-medium px-5 py-2 hover:bg-slate-200 transition">Filter</button>
    </form>

    <?php if ($media === []): ?>
      <div class="rounded-2xl border border-dashed border-slate-200 p-12 text-center text-slate-400 text-sm">No files found.</div>
    <?php else: ?>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($media as $item): ?>
          <div class="rounded-xl bg-white border border-slate-100 shadow-sm overflow-hidden group">
            <div class="h-32 bg-slate-50 flex items-center justify-center overflow-hidden">
              <?php if ($item['type'] === 'image' || $item['type'] === 'svg'): ?>
                <img src="<?= View::e(View::url(ltrim($item['path'], '/'))) ?>" alt="<?= View::e($item['alt_text'] ?? '') ?>" class="w-full h-full object-cover">
              <?php else: ?>
                <span class="text-3xl text-slate-300">&#128196;</span>
              <?php endif; ?>
            </div>
            <div class="p-3">
              <div class="text-xs font-medium text-slate-700 truncate" title="<?= View::e($item['original_name']) ?>"><?= View::e($item['original_name']) ?></div>
              <div class="text-[11px] text-slate-400 mt-0.5"><?= number_format($item['size_bytes'] / 1024, 1) ?> KB</div>
              <div class="mt-2 flex gap-2 text-xs">
                <?php if ($showTrash): ?>
                  <form action="<?= View::url('admin/media/' . $item['id'] . '/restore') ?>" method="POST"><?= View::csrfField() ?>
                    <button class="text-green-600 hover:underline">Restore</button>
                  </form>
                  <form action="<?= View::url('admin/media/' . $item['id'] . '/force-delete') ?>" method="POST" onsubmit="return confirm('Permanently delete this file? This cannot be undone.');">
                    <?= View::csrfField() ?>
                    <button class="text-red-600 hover:underline">Delete Forever</button>
                  </form>
                <?php else: ?>
                  <form action="<?= View::url('admin/media/' . $item['id'] . '/delete') ?>" method="POST">
                    <?= View::csrfField() ?>
                    <button class="text-red-600 hover:underline">Trash</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function mediaManager(uploadUrl, csrfToken, folderId) {
  return {
    dragging: false,
    queue: [],
    handleDrop(e) {
      this.dragging = false;
      this.uploadFiles(e.dataTransfer.files);
    },
    handleInput(e) {
      this.uploadFiles(e.target.files);
    },
    uploadFiles(fileList) {
      Array.from(fileList).forEach((file) => {
        const item = { id: crypto.randomUUID(), name: file.name, status: 'Uploading...' };
        this.queue.push(item);

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_csrf_token', csrfToken);
        if (folderId) formData.append('folder_id', folderId);

        fetch(uploadUrl, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
          .then((res) => res.json())
          .then((data) => {
            item.status = data.success ? 'Done' : ('Failed: ' + data.message);
            if (data.success) setTimeout(() => window.location.reload(), 600);
          })
          .catch(() => { item.status = 'Failed: network error'; });
      });
    },
  };
}
</script>
