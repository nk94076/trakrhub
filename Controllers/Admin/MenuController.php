<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Menu;
use App\Models\MenuItem;

final class MenuController extends Controller
{
    public function index(): void
    {
        $menus = Menu::all('id ASC');
        $activeMenuId = $this->input('menu') !== null && $this->input('menu') !== ''
            ? (int) $this->input('menu')
            : (int) ($menus[0]['id'] ?? 0);

        $this->view('admin.menus.index', [
            'pageTitle' => 'Menus',
            'pageHeading' => 'Menu Builder',
            'menus' => $menus,
            'activeMenuId' => $activeMenuId,
            'items' => $activeMenuId ? MenuItem::forMenuAdmin($activeMenuId) : [],
        ], 'admin.layouts.app');
    }

    public function storeItem(): void
    {
        $this->requireCsrf();

        $menuId = (int) $this->input('menu_id', 0);
        $label = trim((string) $this->input('label', ''));
        $url = trim((string) $this->input('url', ''));

        $validator = $this->validate(['label' => $label, 'url' => $url], [
            'label' => 'required|max:100',
            'url' => 'required|max:500',
        ]);

        if ($validator->fails()) {
            Session::flash('error', 'Label and URL are required.');
            $this->redirect('admin/menus?menu=' . $menuId);

            return;
        }

        $id = MenuItem::create([
            'menu_id' => $menuId,
            'label' => $label,
            'url' => $url,
            'target' => $this->input('target', '_self') === '_blank' ? '_blank' : '_self',
            'sort_order' => MenuItem::nextSortOrder($menuId),
            'status' => 'published',
        ]);

        ActivityLog::record('menu_item.create', 'menu_item', $id, $label);

        Session::flash('success', 'Menu item added.');
        $this->redirect('admin/menus?menu=' . $menuId);
    }

    public function updateItem(int $id): void
    {
        $this->requireCsrf();

        $menuId = (int) $this->input('menu_id', 0);

        MenuItem::update($id, [
            'label' => trim((string) $this->input('label', '')),
            'url' => trim((string) $this->input('url', '')),
            'target' => $this->input('target', '_self') === '_blank' ? '_blank' : '_self',
            'status' => $this->input('status', 'published') === 'draft' ? 'draft' : 'published',
        ]);

        ActivityLog::record('menu_item.update', 'menu_item', $id);

        Session::flash('success', 'Menu item updated.');
        $this->redirect('admin/menus?menu=' . $menuId);
    }

    public function deleteItem(int $id): void
    {
        $this->requireCsrf();

        $menuId = (int) $this->input('menu_id', 0);

        MenuItem::delete($id);
        ActivityLog::record('menu_item.delete', 'menu_item', $id);

        Session::flash('success', 'Menu item deleted.');
        $this->redirect('admin/menus?menu=' . $menuId);
    }

    public function reorder(): void
    {
        $this->requireCsrf();

        $orderedIds = (array) ($this->input('order', []) ?? []);
        MenuItem::reorder($orderedIds);

        $this->json(['success' => true]);
    }
}
