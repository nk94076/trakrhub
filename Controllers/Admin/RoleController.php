<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;

final class RoleController extends Controller
{
    public function index(): void
    {
        $roles = Role::withUserCounts();

        foreach ($roles as &$role) {
            $role['checked_permission_ids'] = Role::permissionIds((int) $role['id']);
        }
        unset($role);

        $this->view('admin.roles.index', [
            'pageTitle' => 'Roles & Permissions',
            'pageHeading' => 'Roles & Permissions',
            'roles' => $roles,
            'permissionGroups' => Permission::grouped(),
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name) ?? '', '-'));

        if ($name === '' || $slug === '') {
            Session::flash('error', 'Role name is required.');
            $this->redirect('admin/roles');

            return;
        }

        $id = Role::create(['name' => $name, 'slug' => $slug, 'description' => (string) $this->input('description', '')]);
        Role::syncPermissions($id, (array) $this->input('permissions', []));

        ActivityLog::record('role.create', 'role', $id, $name);

        Session::flash('success', 'Role created.');
        $this->redirect('admin/roles');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();

        $role = Role::find($id);

        if ($role !== null && (bool) $role['is_system']) {
            // System roles keep their name/slug fixed but permissions remain editable.
            Role::syncPermissions($id, (array) $this->input('permissions', []));
        } else {
            Role::update($id, [
                'name' => trim((string) $this->input('name', '')),
                'description' => (string) $this->input('description', ''),
            ]);
            Role::syncPermissions($id, (array) $this->input('permissions', []));
        }

        ActivityLog::record('role.update', 'role', $id);

        Session::flash('success', 'Role updated.');
        $this->redirect('admin/roles');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        $role = Role::find($id);

        if ($role !== null && (bool) $role['is_system']) {
            Session::flash('error', 'System roles cannot be deleted.');
            $this->redirect('admin/roles');

            return;
        }

        $userCount = Database::fetchOne('SELECT COUNT(*) AS total FROM users WHERE role_id = :id', ['id' => $id]);

        if ((int) ($userCount['total'] ?? 0) > 0) {
            Session::flash('error', 'This role has users assigned to it. Reassign them before deleting.');
            $this->redirect('admin/roles');

            return;
        }

        Role::forceDelete($id);
        ActivityLog::record('role.delete', 'role', $id);

        Session::flash('success', 'Role deleted.');
        $this->redirect('admin/roles');
    }
}
