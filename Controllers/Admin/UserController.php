<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;

final class UserController extends Controller
{
    public function index(): void
    {
        $this->view('admin.users.index', [
            'pageTitle' => 'Users',
            'pageHeading' => 'Admin Users',
            'users' => Database::fetchAll(
                'SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id
                 WHERE u.deleted_at IS NULL ORDER BY u.id ASC'
            ),
            'roles' => Role::all('id ASC'),
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $roleId = (int) $this->input('role_id', 0);

        $validator = $this->validate(
            ['name' => $name, 'email' => $email, 'password' => $password],
            ['name' => 'required|max:100', 'email' => 'required|email', 'password' => 'required|min:8']
        );

        if ($validator->fails() || User::findByEmail($email) !== null) {
            Session::flash('error', $validator->fails() ? 'Please check the form fields.' : 'A user with that email already exists.');
            $this->redirect('admin/users');

            return;
        }

        $id = User::create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role_id' => $roleId,
            'status' => 'active',
        ]);

        ActivityLog::record('user.create', 'user', $id, $email);

        Session::flash('success', 'User created.');
        $this->redirect('admin/users');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();

        $data = [
            'name' => trim((string) $this->input('name', '')),
            'role_id' => (int) $this->input('role_id', 0),
            'status' => in_array($this->input('status'), ['active', 'inactive', 'suspended'], true)
                ? $this->input('status')
                : 'active',
        ];

        $password = (string) $this->input('password', '');

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        User::update($id, $data);
        ActivityLog::record('user.update', 'user', $id);

        Session::flash('success', 'User updated.');
        $this->redirect('admin/users');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        if ($id === Auth::id()) {
            Session::flash('error', 'You cannot delete your own account.');
            $this->redirect('admin/users');

            return;
        }

        User::delete($id);
        ActivityLog::record('user.delete', 'user', $id);

        Session::flash('success', 'User removed.');
        $this->redirect('admin/users');
    }
}
