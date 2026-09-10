<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Icon;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Statistic;

final class StatisticController extends Controller
{
    public function index(): void
    {
        $this->view('admin.statistics.index', [
            'pageTitle' => 'Statistics',
            'pageHeading' => 'Statistics',
            'statistics' => Statistic::all('sort_order ASC'),
            'iconKeys' => Icon::keys(),
        ], 'admin.layouts.app');
    }

    public function create(): void
    {
        $this->requireCsrf();

        $id = Statistic::create($this->payload());
        ActivityLog::record('statistic.create', 'statistic', $id);

        Session::flash('success', 'Statistic added.');
        $this->redirect('admin/statistics');
    }

    public function update(int $id): void
    {
        $this->requireCsrf();

        Statistic::update($id, $this->payload());
        ActivityLog::record('statistic.update', 'statistic', $id);

        Session::flash('success', 'Statistic updated.');
        $this->redirect('admin/statistics');
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        Statistic::forceDelete($id);
        ActivityLog::record('statistic.delete', 'statistic', $id);

        Session::flash('success', 'Statistic removed.');
        $this->redirect('admin/statistics');
    }

    private function payload(): array
    {
        $icon = (string) $this->input('icon', '');

        return [
            'label' => trim((string) $this->input('label', '')),
            'value' => trim((string) $this->input('value', '')),
            'suffix' => trim((string) $this->input('suffix', '')),
            'icon' => in_array($icon, Icon::keys(), true) ? $icon : null,
            'sort_order' => (int) $this->input('sort_order', 0),
            'status' => $this->input('status', 'published') === 'draft' ? 'draft' : 'published',
        ];
    }
}
