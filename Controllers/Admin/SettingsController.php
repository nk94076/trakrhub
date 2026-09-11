<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Core\Theme;
use App\Models\ActivityLog;
use App\Models\Setting;

final class SettingsController extends Controller
{
    private const GROUPS = ['general', 'branding', 'theme', 'typography', 'business', 'social', 'analytics', 'smtp', 'recaptcha', 'seo', 'cookie_banner'];

    public function index(string $group = 'branding'): void
    {
        if (!in_array($group, self::GROUPS, true)) {
            $group = 'branding';
        }

        $rows = Database::fetchAll(
            'SELECT `key`, `value`, `type` FROM settings WHERE `group` = :group ORDER BY `key` ASC',
            ['group' => $group]
        );

        $this->view('admin.settings.index', [
            'pageTitle' => 'Settings',
            'pageHeading' => 'Settings',
            'groups' => self::GROUPS,
            'activeGroup' => $group,
            'settings' => $rows,
        ], 'admin.layouts.app');
    }

    public function update(string $group): void
    {
        $this->requireCsrf();

        if (!in_array($group, self::GROUPS, true)) {
            $this->abort(404);

            return;
        }

        $types = Database::fetchAll(
            'SELECT `key`, `type` FROM settings WHERE `group` = :group',
            ['group' => $group]
        );
        $typeMap = array_column($types, 'type', 'key');

        foreach ((array) $this->input('settings', []) as $key => $value) {
            $value = (string) $value;

            if ($group === 'typography' && in_array($key, ['heading_font', 'body_font'], true)
                && !array_key_exists($value, Theme::FONT_STACKS)) {
                continue;
            }

            Setting::set($group, (string) $key, $value, $typeMap[$key] ?? 'text');
        }

        ActivityLog::record('settings.update', 'settings', null, $group);

        Session::flash('success', 'Settings updated.');
        $this->redirect('admin/settings/' . $group);
    }
}
