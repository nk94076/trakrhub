<?php

use App\Core\View;

/** @var array $roles */
/** @var array $permissionGroups */
?>
<div class="flex justify-end mb-5">
  <button type="button" onclick="document.getElementById('create-role-modal').classList.remove('hidden')"
          class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30">
    + Add Role
  </button>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
  <?php foreach ($roles as $role): ?>
    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-slate-900"><?= View::e($role['name']) ?></h3>
        <?php if ($role['is_system']): ?><span class="text-[10px] uppercase tracking-wide text-brand-600 bg-brand-50 rounded-full px-2 py-0.5">System</span><?php endif; ?>
      </div>
      <p class="text-xs text-slate-400 mt-1"><?= View::e($role['description'] ?? '') ?></p>
      <p class="text-xs text-slate-500 mt-3"><?= (int) $role['user_count'] ?> user(s)</p>
      <div class="mt-4 flex gap-3 text-xs">
        <button type="button" onclick='openEditRole(<?= json_encode($role, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-brand-600 hover:underline">Edit Permissions</button>
        <?php if (!$role['is_system']): ?>
          <form action="<?= View::url('admin/roles/' . $role['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this role?');">
            <?= View::csrfField() ?>
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php
// Shared permission-checkbox markup, used by both the create and edit modals.
$renderPermissionGroups = static function (array $checkedIds = []) use ($permissionGroups): void {
    foreach ($permissionGroups as $groupName => $permissions) {
        echo '<div class="mb-3"><div class="text-xs font-semibold uppercase text-slate-400 mb-1.5">' . View::e($groupName) . '</div><div class="grid grid-cols-2 gap-1.5">';
        foreach ($permissions as $permission) {
            $checked = in_array((int) $permission['id'], $checkedIds, true) ? 'checked' : '';
            echo '<label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="permissions[]" value="' . (int) $permission['id'] . '" ' . $checked . ' class="rounded border-slate-300 text-brand-600"> ' . View::e($permission['name']) . '</label>';
        }
        echo '</div></div>';
    }
};
?>

<!-- Create modal -->
<div id="create-role-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4 overflow-y-auto">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg my-8">
    <h3 class="font-semibold text-slate-900 mb-4">Add Role</h3>
    <form action="<?= View::url('admin/roles') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" name="name" placeholder="Role name" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <textarea name="description" placeholder="Description" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></textarea>
      <div class="max-h-64 overflow-y-auto border border-slate-100 rounded-lg p-3">
        <?php $renderPermissionGroups([]); ?>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('create-role-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Create</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit modal -->
<div id="edit-role-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4 overflow-y-auto">
  <div class="bg-white rounded-2xl p-6 w-full max-w-lg my-8">
    <h3 class="font-semibold text-slate-900 mb-4">Edit Role</h3>
    <form id="edit-role-form" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" id="edit-role-name" name="name" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <textarea id="edit-role-description" name="description" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"></textarea>
      <div id="edit-role-permissions" class="max-h-64 overflow-y-auto border border-slate-100 rounded-lg p-3"></div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('edit-role-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const rolesUpdateBase = '<?= View::url('admin/roles') ?>';
const allPermissionGroups = <?= json_encode($permissionGroups, JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

function openEditRole(role) {
  document.getElementById('edit-role-name').value = role.name;
  document.getElementById('edit-role-description').value = role.description || '';
  document.getElementById('edit-role-form').action = rolesUpdateBase + '/' + role.id;

  const container = document.getElementById('edit-role-permissions');
  container.innerHTML = '';
  const checkedIds = (role.checked_permission_ids || []);

  Object.keys(allPermissionGroups).forEach((groupName) => {
    const wrap = document.createElement('div');
    wrap.className = 'mb-3';
    const title = document.createElement('div');
    title.className = 'text-xs font-semibold uppercase text-slate-400 mb-1.5';
    title.textContent = groupName;
    wrap.appendChild(title);

    const grid = document.createElement('div');
    grid.className = 'grid grid-cols-2 gap-1.5';

    allPermissionGroups[groupName].forEach((perm) => {
      const isChecked = checkedIds.includes(perm.id);
      const label = document.createElement('label');
      label.className = 'flex items-center gap-2 text-sm text-slate-600';
      label.innerHTML = '<input type="checkbox" name="permissions[]" value="' + perm.id + '" class="rounded border-slate-300 text-brand-600" ' + (isChecked ? 'checked' : '') + '> ' + perm.name;
      grid.appendChild(label);
    });

    wrap.appendChild(grid);
    container.appendChild(wrap);
  });

  document.getElementById('edit-role-modal').classList.remove('hidden');
}
</script>
