<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\ActivityLog;
use App\Models\BlogPost;
use App\Models\ContactLead;
use App\Models\Media;
use App\Models\User;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin.dashboard.index', [
            'pageTitle' => 'Dashboard',
            'pageHeading' => 'Dashboard',
            'stats' => [
                'leads' => ContactLead::count(),
                'blog_posts' => BlogPost::count(),
                'media' => Media::count(),
                'users' => User::count(),
            ],
            'recentLeads' => array_slice(ContactLead::all('created_at DESC'), 0, 5),
            'recentActivity' => ActivityLog::recent(8),
        ], 'admin.layouts.app');
    }
}
