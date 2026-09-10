<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;
use App\Core\Request;

final class SeoMeta extends Model
{
    protected static string $table = 'seo_meta';

    public static function forEntity(string $entityType, int $entityId): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM seo_meta WHERE entity_type = :type AND entity_id = :id',
            ['type' => $entityType, 'id' => $entityId]
        );
    }

    public static function upsert(string $entityType, int $entityId, array $data): void
    {
        $existing = static::forEntity($entityType, $entityId);
        $data['entity_type'] = $entityType;
        $data['entity_id'] = $entityId;

        if ($existing === null) {
            static::create($data);

            return;
        }

        static::update((int) $existing['id'], $data);
    }

    /**
     * Persists the SEO fieldset (see admin/partials/_seo_fields.php) from the
     * current request's POST body. Shared by every admin form that embeds
     * that partial (Pages, Blog posts, ...) so the field list only lives
     * in one place.
     */
    public static function upsertFromRequest(string $entityType, int $entityId): void
    {
        $schemaJsonRaw = trim((string) Request::input('schema_json', ''));
        $schemaJson = null;

        if ($schemaJsonRaw !== '') {
            $decoded = json_decode($schemaJsonRaw, true);
            $schemaJson = json_last_error() === JSON_ERROR_NONE ? json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
        }

        static::upsert($entityType, $entityId, [
            'seo_title' => trim((string) Request::input('seo_title', '')) ?: null,
            'meta_description' => trim((string) Request::input('meta_description', '')) ?: null,
            'meta_keywords' => trim((string) Request::input('meta_keywords', '')) ?: null,
            'canonical_url' => trim((string) Request::input('canonical_url', '')) ?: null,
            'robots_index' => Request::input('robots_index') === 'noindex' ? 'noindex' : 'index',
            'robots_follow' => Request::input('robots_follow') === 'nofollow' ? 'nofollow' : 'follow',
            'og_title' => trim((string) Request::input('og_title', '')) ?: null,
            'og_description' => trim((string) Request::input('og_description', '')) ?: null,
            'twitter_card' => Request::input('twitter_card') === 'summary' ? 'summary' : 'summary_large_image',
            'schema_type' => trim((string) Request::input('schema_type', '')) ?: null,
            'schema_json' => $schemaJson,
        ]);
    }
}
