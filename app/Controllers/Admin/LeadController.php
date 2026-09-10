<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\ContactLead;

final class LeadController extends Controller
{
    private const STATUSES = ['new', 'contacted', 'qualified', 'closed', 'spam'];

    public function index(): void
    {
        $status = (string) $this->input('status', '');
        $type = (string) $this->input('type', '');

        $sql = 'SELECT * FROM contact_leads WHERE 1=1';
        $params = [];

        if (in_array($status, self::STATUSES, true)) {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        }

        if (in_array($type, ['advertiser', 'publisher', 'general', 'career'], true)) {
            $sql .= ' AND lead_type = :type';
            $params['type'] = $type;
        }

        $sql .= ' ORDER BY created_at DESC';

        $this->view('admin.leads.index', [
            'pageTitle' => 'Leads',
            'pageHeading' => 'Leads',
            'leads' => Database::fetchAll($sql, $params),
            'statuses' => self::STATUSES,
            'activeStatus' => $status,
            'activeType' => $type,
        ], 'admin.layouts.app');
    }

    public function updateStatus(int $id): void
    {
        $this->requireCsrf();

        $status = (string) $this->input('status', '');

        if (!in_array($status, self::STATUSES, true)) {
            $this->abort(422, 'Invalid status.');

            return;
        }

        ContactLead::update($id, ['status' => $status]);
        ActivityLog::record('lead.status_update', 'contact_lead', $id, $status);

        Session::flash('success', 'Lead status updated.');
        $this->back();
    }

    public function export(): void
    {
        $leads = Database::fetchAll('SELECT * FROM contact_leads ORDER BY created_at DESC');

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="leads-' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Company', 'Type', 'Message', 'Status', 'Received At'], ',', '"', '\\');

        foreach ($leads as $lead) {
            fputcsv($out, [
                $lead['id'],
                self::sanitizeCsvCell($lead['name']),
                self::sanitizeCsvCell($lead['email']),
                self::sanitizeCsvCell($lead['phone']),
                self::sanitizeCsvCell($lead['company']),
                $lead['lead_type'],
                self::sanitizeCsvCell($lead['message']),
                $lead['status'],
                $lead['created_at'],
            ], ',', '"', '\\');
        }

        fclose($out);
    }

    /**
     * Every field here comes from the public, unauthenticated contact form —
     * prefix any value that a spreadsheet app would interpret as a formula
     * (=, +, -, @, or a leading tab/CR) with a single quote so opening the
     * export can't execute attacker-supplied formulas ("CSV injection").
     */
    private static function sanitizeCsvCell(?string $value): string
    {
        $value = (string) $value;

        if ($value !== '' && str_contains("=+-@\t\r", $value[0])) {
            return "'" . $value;
        }

        return $value;
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        Database::query('DELETE FROM contact_leads WHERE id = :id', ['id' => $id]);
        ActivityLog::record('lead.delete', 'contact_lead', $id);

        Session::flash('success', 'Lead deleted.');
        $this->back();
    }
}
