<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Auth;
use App\Core\Model;
use App\Core\Request;

final class ActivityLog extends Model
{
    protected static string $table = 'activity_logs';

    public static function record(string $action, ?string $subjectType = null, ?int $subjectId = null, string $description = ''): void
    {
        static::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }

    public static function recent(int $limit = 10): array
    {
        return array_slice(static::all('created_at DESC'), 0, $limit);
    }
}
