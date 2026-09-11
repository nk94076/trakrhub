<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Validates and stores an uploaded file under public/uploads/{Y}/{m}/,
 * generating WebP and AVIF siblings for raster images. Every check runs
 * against the actual file content (finfo + getimagesize), never the
 * client-supplied filename or Content-Type header.
 */
final class MediaUploader
{
    private const IMAGE_MIME_MAP = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    public static function store(array $file, ?int $folderId, ?int $uploadedBy): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed with error code ' . $file['error']);
        }

        $config = require dirname(__DIR__, 2) . '/config/app.php';

        if ($file['size'] > $config['upload_max_size']) {
            throw new RuntimeException('File exceeds the maximum upload size.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = (string) finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $originalName = basename($file['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/' . date('Y') . '/' . date('m');

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $diskName = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $uploadDir . '/' . $diskName;
        $publicPath = '/uploads/' . date('Y') . '/' . date('m') . '/' . $diskName;

        if ($mimeType === 'image/svg+xml' && $extension === 'svg') {
            self::storeSvg($file['tmp_name'], $destination);

            return [
                'folder_id' => $folderId,
                'uploaded_by' => $uploadedBy,
                'disk_name' => $diskName,
                'original_name' => $originalName,
                'path' => $publicPath,
                'webp_path' => null,
                'avif_path' => null,
                'mime_type' => 'image/svg+xml',
                'type' => 'svg',
                'size_bytes' => filesize($destination) ?: 0,
                'width' => null,
                'height' => null,
            ];
        }

        if (isset(self::IMAGE_MIME_MAP[$mimeType])) {
            if (!in_array($extension, $config['upload_allowed_images'], true)) {
                throw new RuntimeException('File extension does not match its content.');
            }

            move_uploaded_file($file['tmp_name'], $destination);

            $dimensions = @getimagesize($destination);
            [$webpPath, $avifPath] = self::generateModernFormats($destination, $uploadDir, $diskName);

            return [
                'folder_id' => $folderId,
                'uploaded_by' => $uploadedBy,
                'disk_name' => $diskName,
                'original_name' => $originalName,
                'path' => $publicPath,
                'webp_path' => $webpPath,
                'avif_path' => $avifPath,
                'mime_type' => $mimeType,
                'type' => 'image',
                'size_bytes' => filesize($destination) ?: 0,
                'width' => $dimensions[0] ?? null,
                'height' => $dimensions[1] ?? null,
            ];
        }

        if (str_starts_with($mimeType, 'video/') && in_array($extension, $config['upload_allowed_videos'], true)) {
            move_uploaded_file($file['tmp_name'], $destination);

            return [
                'folder_id' => $folderId,
                'uploaded_by' => $uploadedBy,
                'disk_name' => $diskName,
                'original_name' => $originalName,
                'path' => $publicPath,
                'webp_path' => null,
                'avif_path' => null,
                'mime_type' => $mimeType,
                'type' => 'video',
                'size_bytes' => filesize($destination) ?: 0,
                'width' => null,
                'height' => null,
            ];
        }

        if ($mimeType === 'application/pdf' && in_array($extension, $config['upload_allowed_documents'], true)) {
            move_uploaded_file($file['tmp_name'], $destination);

            return [
                'folder_id' => $folderId,
                'uploaded_by' => $uploadedBy,
                'disk_name' => $diskName,
                'original_name' => $originalName,
                'path' => $publicPath,
                'webp_path' => null,
                'avif_path' => null,
                'mime_type' => $mimeType,
                'type' => 'document',
                'size_bytes' => filesize($destination) ?: 0,
                'width' => null,
                'height' => null,
            ];
        }

        throw new RuntimeException('Unsupported file type.');
    }

    /**
     * Strips <script> tags, on*= event handlers (quoted or unquoted), and
     * javascript: URIs from href/xlink:href before saving. This is
     * defense-in-depth, not a full sanitizer (SMIL <animate> handlers and
     * <foreignObject> content aren't covered) — SVGs are only accepted from
     * authenticated admin users, and the app always renders them via <img>,
     * which never executes embedded scripts. Still worth tightening since a
     * lower-privileged admin (media.manage only) could otherwise plant a
     * payload a higher-privileged admin might open directly.
     */
    private static function storeSvg(string $tmpPath, string $destination): void
    {
        $contents = (string) file_get_contents($tmpPath);
        $contents = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $contents) ?? $contents;
        $contents = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $contents) ?? $contents;
        $contents = preg_replace("/\son\w+\s*=\s*'[^']*'/i", '', $contents) ?? $contents;
        $contents = preg_replace('/\son\w+\s*=\s*[^\s">]+/i', '', $contents) ?? $contents;
        $contents = preg_replace('/((?:xlink:)?href\s*=\s*)(["\'])\s*javascript:[^"\']*\2/i', '$1$2$2', $contents) ?? $contents;

        file_put_contents($destination, $contents);
    }

    /**
     * Generates WebP and (where GD supports it) AVIF siblings from a single
     * decode of the source image. Returns [webpPath, avifPath]; either may
     * be null if the format isn't supported or the source can't be decoded.
     */
    private static function generateModernFormats(string $sourcePath, string $uploadDir, string $diskName): array
    {
        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($sourcePath),
            'png' => @imagecreatefrompng($sourcePath),
            'gif' => @imagecreatefromgif($sourcePath),
            default => null,
        };

        if ($image === null || $image === false) {
            return [null, null];
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $year = date('Y');
        $month = date('m');
        $baseName = pathinfo($diskName, PATHINFO_FILENAME);

        $webpPath = null;

        if (function_exists('imagewebp')) {
            $webpName = $baseName . '.webp';
            imagewebp($image, $uploadDir . '/' . $webpName, 82);
            $webpPath = "/uploads/{$year}/{$month}/{$webpName}";
        }

        $avifPath = null;

        if (function_exists('imageavif')) {
            $avifName = $baseName . '.avif';
            imageavif($image, $uploadDir . '/' . $avifName, 60);
            $avifPath = "/uploads/{$year}/{$month}/{$avifName}";
        }

        imagedestroy($image);

        return [$webpPath, $avifPath];
    }
}
