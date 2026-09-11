<?php

use App\Core\Csrf;
use App\Core\View;

/** @var array $menus */
/** @var int $activeMenuId */
/** @var array $items */
?>
<div class="flex gap-3 mb-6">
  <?php foreach ($menus as $menu): ?>
    <a href="<?= View::url('admin/menus?menu=' . $menu['id']) ?>"
       class="px-4 py-2 rounded-full text-sm font-medium <?= (int) $menu['id'] === $activeMenuId ? 'bg-gradient-to-r from-brand-500 to-accent-500 text-white' : 'bg-white border border-slate-200 text-slate-600' ?>">
      <?= View::e($menu['name']) ?>
    </a>
  <?php endforeach; ?>
</div>

<div class="grid lg:grid-cols-[1fr_320px] gap-6">
  <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
    <h3 class="font-semibold text-slate-900 mb-4">Menu Items <span class="text-xs text-slate-400 font-normal">(drag to reorder)</span></h3>
    <ul id="menu-items-list" class="space-y-2">
      <?php foreach ($items as $item): ?>
        <li draggable="true" data-id="<?= (int) $item['id'] ?>"
            class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 px-4 py-3 cursor-move">
          <div class="flex items-center gap-3">
            <span class="text-slate-300">&#8942;&#8942;</span>
            <div>
              <div class="text-sm font-medium text-slate-800"><?= View::e($item['label']) ?></div>
              <div class="text-xs text-slate-400"><?= View::e($item['url']) ?> <?= $item['status'] === 'draft' ? '· draft' : '' ?></div>
            </div>
          </div>
          <div class="flex items-center gap-3 text-xs">
            <button type="button" onclick="editItem(<?= (int) $item['id'] ?>, '<?= View::e(addslashes($item['label'])) ?>', '<?= View::e(addslashes($item['url'])) ?>', '<?= View::e($item['target']) ?>', '<?= View::e($item['status']) ?>')" class="text-brand-600 hover:underline">Edit</button>
            <form action="<?= View::url('admin/menus/items/' . $item['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this menu item?');">
              <?= View::csrfField() ?>
              <input type="hidden" name="menu_id" value="<?= (int) $activeMenuId ?>">
              <button class="text-red-600 hover:underline">Delete</button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php if ($items === []): ?>
      <p class="text-sm text-slate-400">No items in this menu yet.</p>
    <?php endif; ?>
  </div>

  <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 h-fit">
    <h3 id="form-title" class="font-semibold text-slate-900 mb-4">Add Menu Item</h3>
    <form id="item-form" action="<?= View::url('admin/menus/items') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="hidden" name="menu_id" value="<?= (int) $activeMenuId ?>">
      <input type="hidden" id="item-id" name="item_id" value="">
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Label</label>
        <input type="text" id="item-label" name="label" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">URL</label>
        <input type="text" id="item-url" name="url" required placeholder="/about" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Target</label>
        <select id="item-target" name="target" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="_self">Same tab</option>
          <option value="_blank">New tab</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
        <select id="item-status" name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <option value="published">Published</option>
          <option value="draft">Draft</option>
        </select>
      </div>
      <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save Item</button>
      <button type="button" onclick="resetForm()" class="w-full text-xs text-slate-400 hover:text-slate-600">Cancel edit</button>
    </form>
  </div>
</div>

<script>
const baseItemsUrl = '<?= View::url('admin/menus/items') ?>';

function editItem(id, label, url, target, status) {
  document.getElementById('form-title').textContent = 'Edit Menu Item';
  document.getElementById('item-id').value = id;
  document.getElementById('item-label').value = label;
  document.getElementById('item-url').value = url;
  document.getElementById('item-target').value = target;
  document.getElementById('item-status').value = status;
  document.getElementById('item-form').action = baseItemsUrl + '/' + id;
}

function resetForm() {
  document.getElementById('form-title').textContent = 'Add Menu Item';
  document.getElementById('item-form').reset();
  document.getElementById('item-id').value = '';
  document.getElementById('item-form').action = baseItemsUrl;
}

// Native HTML5 drag-and-drop reorder, posted via fetch on drop.
(function () {
  const list = document.getElementById('menu-items-list');
  if (!list) return;
  let dragged = null;

  list.addEventListener('dragstart', (e) => { dragged = e.target.closest('li'); });

  list.addEventListener('dragover', (e) => {
    e.preventDefault();
    const target = e.target.closest('li');
    if (!target || target === dragged) return;
    const rect = target.getBoundingClientRect();
    const after = (e.clientY - rect.top) / rect.height > 0.5;
    list.insertBefore(dragged, after ? target.nextSibling : target);
  });

  list.addEventListener('drop', () => {
    const ids = Array.from(list.children).map((li) => li.dataset.id);
    const params = new URLSearchParams();
    ids.forEach((id) => params.append('order[]', id));
    params.append('_csrf_token', '<?= View::e(Csrf::token()) ?>');

    fetch('<?= View::url('admin/menus/reorder') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: params.toString(),
    });
  });
})();
</script>
