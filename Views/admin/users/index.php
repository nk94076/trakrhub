<?php

use App\Core\Auth;
use App\Core\View;

/** @var array $users */
/** @var array $roles */

$currentUserId = Auth::id();
?>
<div class="flex justify-end mb-5">
  <button type="button" onclick="document.getElementById('create-user-modal').classList.remove('hidden')"
          class="rounded-full bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold px-6 py-2.5 shadow-lg shadow-brand-500/30">
    + Add User
  </button>
</div>

<div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
      <tr>
        <th class="text-left px-5 py-3">Name</th>
        <th class="text-left px-5 py-3">Email</th>
        <th class="text-left px-5 py-3">Role</th>
        <th class="text-left px-5 py-3">Status</th>
        <th class="text-left px-5 py-3">Last Login</th>
        <th class="text-right px-5 py-3">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      <?php foreach ($users as $user): ?>
        <tr>
          <td class="px-5 py-3 font-medium text-slate-800"><?= View::e($user['name']) ?></td>
          <td class="px-5 py-3 text-slate-500"><?= View::e($user['email']) ?></td>
          <td class="px-5 py-3 text-slate-500"><?= View::e($user['role_name']) ?></td>
          <td class="px-5 py-3">
            <span class="text-xs rounded-full px-2.5 py-1 <?= $user['status'] === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' ?>">
              <?= View::e(ucfirst($user['status'])) ?>
            </span>
          </td>
          <td class="px-5 py-3 text-slate-400 text-xs"><?= View::e($user['last_login_at'] ?? 'Never') ?></td>
          <td class="px-5 py-3 text-right">
            <?php $safeUser = array_diff_key($user, array_flip(['password', 'remember_token', 'two_factor_secret'])); ?>
            <button type="button" onclick='openEdit(<?= json_encode($safeUser, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-brand-600 hover:underline text-xs">Edit</button>
            <?php if ((int) $user['id'] !== $currentUserId): ?>
              <form action="<?= View::url('admin/users/' . $user['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Remove this user?');">
                <?= View::csrfField() ?>
                <button class="text-red-600 hover:underline text-xs ml-3">Remove</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Create modal -->
<div id="create-user-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-900 mb-4">Add User</h3>
    <form action="<?= View::url('admin/users') ?>" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" name="name" placeholder="Full name" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <input type="email" name="email" placeholder="Email address" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <input type="password" name="password" placeholder="Password (min 8 chars)" required minlength="8" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <select name="role_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <?php foreach ($roles as $role): ?>
          <option value="<?= (int) $role['id'] ?>"><?= View::e($role['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('create-user-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Create</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit modal -->
<div id="edit-user-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-900 mb-4">Edit User</h3>
    <form id="edit-user-form" method="POST" class="space-y-3">
      <?= View::csrfField() ?>
      <input type="text" id="edit-name" name="name" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <input type="password" name="password" placeholder="New password (leave blank to keep current)" minlength="8" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
      <select id="edit-role" name="role_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <?php foreach ($roles as $role): ?>
          <option value="<?= (int) $role['id'] ?>"><?= View::e($role['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="edit-status" name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="suspended">Suspended</option>
      </select>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="document.getElementById('edit-user-modal').classList.add('hidden')" class="flex-1 rounded-lg bg-slate-100 text-slate-600 text-sm font-medium py-2.5">Cancel</button>
        <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 text-white text-sm font-semibold py-2.5">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const usersUpdateBase = '<?= View::url('admin/users') ?>';
function openEdit(user) {
  document.getElementById('edit-name').value = user.name;
  document.getElementById('edit-role').value = user.role_id;
  document.getElementById('edit-status').value = user.status;
  document.getElementById('edit-user-form').action = usersUpdateBase + '/' + user.id;
  document.getElementById('edit-user-modal').classList.remove('hidden');
}
</script>
