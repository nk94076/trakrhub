<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\MediaUploader;
use App\Core\Request;
use App\Core\Session;
use App\Models\ActivityLog;
use App\Models\Media;
use App\Models\MediaFolder;

final class MediaController extends Controller
{
    public function index(): void
    {
        $folderId = $this->nullableInt('folder');
        $search = (string) $this->input('q', '');
        $type = (string) $this->input('type', '');
        $showTrash = $this->input('trash') === '1';

        $sql = 'SELECT m.*, u.name AS uploader_name FROM media m LEFT JOIN users u ON u.id = m.uploaded_by WHERE 1=1';
        $params = [];

        if ($showTrash) {
            $sql .= ' AND m.deleted_at IS NOT NULL';
        } else {
            $sql .= ' AND m.deleted_at IS NULL';
        }

        if ($folderId !== null) {
            $sql .= ' AND m.folder_id = :folder_id';
            $params['folder_id'] = $folderId;
        }

        if ($search !== '') {
            $sql .= ' AND m.original_name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        if ($type !== '') {
            $sql .= ' AND m.type = :type';
            $params['type'] = $type;
        }

        $sql .= ' ORDER BY m.created_at DESC';

        $this->view('admin.media.index', [
            'pageTitle' => 'Media Manager',
            'pageHeading' => 'Media Manager',
            'media' => Database::fetchAll($sql, $params),
            'folders' => MediaFolder::all('name ASC'),
            'currentFolder' => $folderId,
            'search' => $search,
            'type' => $type,
            'showTrash' => $showTrash,
        ], 'admin.layouts.app');
    }

    /**
     * Read-only JSON feed of images for the Media Picker modal (used from
     * Settings image fields and the page-section content editor). Deliberately
     * separate from index() so it isn't gated behind media.manage — any
     * authenticated admin picking a logo/section image just needs to browse,
     * not manage, the library.
     */
    public function picker(): void
    {
        $search = (string) $this->input('q', '');

        $sql = "SELECT id, path, webp_path, original_name, alt_text FROM media
                WHERE deleted_at IS NULL AND type IN ('image', 'svg')";
        $params = [];

        if ($search !== '') {
            $sql .= ' AND original_name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY created_at DESC LIMIT 60';

        $this->json(['success' => true, 'media' => Database::fetchAll($sql, $params)]);
    }

    public function upload(): void
    {
        $this->requireCsrf();

        $file = Request::file('file');

        if ($file === null) {
            $this->json(['success' => false, 'message' => 'No file received.'], 422);

            return;
        }

        $folderId = $this->nullableInt('folder_id');

        try {
            $data = MediaUploader::store($file, $folderId, Auth::id());
            $id = Media::create($data);
            ActivityLog::record('media.upload', 'media', $id, $data['original_name']);

            $media = Media::find($id);

            $this->json(['success' => true, 'media' => $media]);
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function createFolder(): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));

        if ($name === '') {
            Session::flash('error', 'Folder name is required.');
            $this->back();

            return;
        }

        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name) ?? '', '-'));
        $parentId = $this->nullableInt('parent_id');

        $id = MediaFolder::create(['name' => $name, 'slug' => $slug, 'parent_id' => $parentId]);
        ActivityLog::record('media.folder.create', 'media_folder', $id, $name);

        Session::flash('success', 'Folder created.');
        $this->redirect('admin/media');
    }

    public function updateMeta(int $id): void
    {
        $this->requireCsrf();

        Media::update($id, [
            'alt_text' => (string) $this->input('alt_text', ''),
            'title' => (string) $this->input('title', ''),
        ]);

        ActivityLog::record('media.update', 'media', $id);

        $this->json(['success' => true]);
    }

    public function rename(int $id): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('original_name', ''));

        if ($name !== '') {
            Media::update($id, ['original_name' => $name]);
            ActivityLog::record('media.rename', 'media', $id, $name);
        }

        $this->json(['success' => true]);
    }

    public function delete(int $id): void
    {
        $this->requireCsrf();

        Media::delete($id);
        ActivityLog::record('media.trash', 'media', $id);

        Session::flash('success', 'File moved to trash.');
        $this->back();
    }

    public function restore(int $id): void
    {
        $this->requireCsrf();

        Media::restore($id);
        ActivityLog::record('media.restore', 'media', $id);

        Session::flash('success', 'File restored.');
        $this->back();
    }

    public function forceDelete(int $id): void
    {
        $this->requireCsrf();

        $media = Media::findTrashed($id);

        if ($media !== null) {
            $filePath = dirname(__DIR__, 3) . '/public' . $media['path'];

            if (is_file($filePath)) {
                unlink($filePath);
            }

            if ($media['webp_path']) {
                $webpPath = dirname(__DIR__, 3) . '/public' . $media['webp_path'];

                if (is_file($webpPath)) {
                    unlink($webpPath);
                }
            }

            if (!empty($media['avif_path'])) {
                $avifPath = dirname(__DIR__, 3) . '/public' . $media['avif_path'];

                if (is_file($avifPath)) {
                    unlink($avifPath);
                }
            }
        }

        Media::forceDelete($id);
        ActivityLog::record('media.force_delete', 'media', $id);

        Session::flash('success', 'File permanently deleted.');
        $this->back();
    }
}
