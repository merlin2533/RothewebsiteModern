<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\AuditLogger;
use App\Core\Csrf;
use App\Core\View;

final class MediaController
{
    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
        'image/gif'  => 'gif',
    ];
    private const MAX_BYTES = 8 * 1024 * 1024; // 8 MB

    private function generateAvifSibling(string $absFile, string $mime): void
    {
        if (!function_exists('imageavif')) {
            return; // PHP not built with libavif
        }
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return;
        }
        try {
            $img = $mime === 'image/jpeg'
                ? @imagecreatefromjpeg($absFile)
                : @imagecreatefrompng($absFile);
            if (!$img) return;
            if ($mime === 'image/png') {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
            $avifPath = preg_replace('/\.(jpe?g|png)$/i', '.avif', $absFile);
            if ($avifPath && $avifPath !== $absFile) {
                // quality=70, speed=6 → strong compression at sane CPU cost
                imageavif($img, $avifPath, 70, 6);
            }
            imagedestroy($img);
        } catch (\Throwable) {
            // best-effort
        }
    }

    private function generateWebpSibling(string $absFile, string $mime): void
    {
        if (!extension_loaded('gd') || !function_exists('imagewebp')) {
            return;
        }
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return; // skip svg/gif/already-webp
        }
        try {
            $img = $mime === 'image/jpeg'
                ? @imagecreatefromjpeg($absFile)
                : @imagecreatefrompng($absFile);
            if (!$img) return;
            if ($mime === 'image/png') {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
            $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $absFile);
            if ($webpPath && $webpPath !== $absFile) {
                imagewebp($img, $webpPath, 85);
            }
            imagedestroy($img);
        } catch (\Throwable) {
            // best-effort, never block upload
        }
    }

    public function index(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('media_index', ['media' => $GLOBALS['mediaRepo']->all()]);
    }

    public function upload(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/media');
        }
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Upload fehlgeschlagen.');
            redirect('/admin/media');
        }
        $file = $_FILES['file'];
        if ($file['size'] > self::MAX_BYTES) {
            flash('error', 'Datei zu groß (max. 8 MB).');
            redirect('/admin/media');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($file['tmp_name']);
        if (!isset(self::ALLOWED_MIME[$mime])) {
            flash('error', 'Dateityp nicht erlaubt: ' . $mime);
            redirect('/admin/media');
        }

        $alt = trim((string) post('alt_text', ''));
        if ($alt === '') {
            flash('error', 'Bitte einen Alt-Text angeben (Pflicht für SEO/Barrierefreiheit).');
            redirect('/admin/media');
        }

        $ext = self::ALLOWED_MIME[$mime];
        $randomName = bin2hex(random_bytes(8)) . '.' . $ext;

        $config = $GLOBALS['config'];
        $relPath = date('Y') . '/' . date('m');
        $absDir = $config['uploads_path'] . '/' . $relPath;
        if (!is_dir($absDir)) {
            mkdir($absDir, 0755, true);
        }
        $absFile = $absDir . '/' . $randomName;
        if (!move_uploaded_file($file['tmp_name'], $absFile)) {
            flash('error', 'Datei konnte nicht gespeichert werden.');
            redirect('/admin/media');
        }

        [$w, $h] = @getimagesize($absFile) ?: [null, null];

        // Auto-generate WebP variant for jpg/png originals (front-end perf)
        $this->generateWebpSibling($absFile, $mime);
        // Auto-generate AVIF variant if PHP/GD supports it (best compression)
        $this->generateAvifSibling($absFile, $mime);

        $id = $GLOBALS['mediaRepo']->save([
            'filename'      => $relPath . '/' . $randomName,
            'original_name' => $file['name'],
            'mime'          => $mime,
            'width'         => $w,
            'height'        => $h,
            'alt_text'      => $alt,
            'size_bytes'    => (int) $file['size'],
        ]);
        AuditLogger::log('media.upload', 'media', $id, ['mime' => $mime]);
        flash('success', 'Bild hochgeladen (#' . $id . ').');
        redirect('/admin/media');
    }

    public function updateAlt(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/media');
        }
        $id = (int) ($args['id'] ?? 0);
        $GLOBALS['mediaRepo']->updateAlt($id, (string) post('alt_text', ''));
        AuditLogger::log('media.alt_update', 'media', $id);
        flash('success', 'Alt-Text aktualisiert.');
        redirect('/admin/media');
    }

    public function delete(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/media');
        }
        $id = (int) ($args['id'] ?? 0);
        $media = $GLOBALS['mediaRepo']->find($id);
        if ($media) {
            $config = $GLOBALS['config'];
            $abs = $config['uploads_path'] . '/' . $media['filename'];
            if (is_file($abs)) {
                @unlink($abs);
            }
            $GLOBALS['mediaRepo']->delete($id);
        }
        AuditLogger::log('media.delete', 'media', $id);
        flash('success', 'Datei gelöscht.');
        redirect('/admin/media');
    }
}
