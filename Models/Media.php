<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Media extends Model
{
    protected static string $table = 'media';
    protected static bool $softDeletes = true;
}
