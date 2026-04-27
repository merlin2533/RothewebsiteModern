<?php

declare(strict_types=1);

namespace App\Core;

/**
 * MediaResolver
 * ============================================================================
 * Loest fuer einen `media`-Eintrag die optimale Variante fuer eine bestimmte
 * Zielbreite auf, generiert sie bei Bedarf on-the-fly aus dem Original.
 *
 * Konvention:
 *   - media.filename hat das Schema `from-original/<base>-<W>.<ext>`
 *     (so geschrieben von resize_uploads.php / seed_media_from_originals.php).
 *   - Quelle liegt unter uploads/<base>.<orig_ext> (Original).
 *   - Varianten unter public/assets/images/from-original/<base>-<W>.{jpg,webp}
 *
 * Wenn die Variante schon existiert: nur URL zurueckgeben.
 * Wenn nicht: aus dem Original generieren (best-effort, schweigt bei Fehler).
 *
 * Keine Abhaengigkeiten ausser PHP-GD und der existierenden Verzeichnis-Layout.
 */
final class MediaResolver
{
    /** Bevorzugte Standard-Breiten fuer responsives `<picture>`. */
    public const WIDTHS = [1600, 1200, 800];

    /**
     * Liefert die public-URL fuer das Media in der gegebenen Breite + Format.
     *
     * @param array       $media   Zeile aus der `media`-Tabelle
     * @param int         $width   Wunschbreite (px)
     * @param 'jpg'|'webp' $format
     * @return string|null         null wenn weder Original noch Variante existieren
     */
    public static function variantUrl(array $media, int $width = 1600, string $format = 'jpg'): ?string
    {
        $rel = self::variantPath($media, $width, $format);
        if ($rel === null) return null;

        $abs = self::baseDir() . '/public' . $rel;
        if (!is_file($abs)) {
            // Lazy generieren
            self::generateOnDemand($media, $width, $format);
        }
        if (!is_file($abs)) {
            // Fallback A: Original aus uploads/ direkt ausliefern (besser als
            // ein 404-Bild). Der MediaResolver weiss dank Symlink
            // public/uploads -> ../uploads, dass /uploads/<file> erreichbar ist.
            $orig = self::findOriginal($media);
            if ($orig !== null) {
                $relUploads = '/uploads/' . basename($orig);
                if (is_file(self::baseDir() . '/public' . $relUploads)
                    || is_file(self::baseDir() . '/uploads/' . basename($orig))) {
                    return $relUploads;
                }
            }
            // Fallback B: was in media.filename steht (Vor-Resize-Stand)
            return '/' . ltrim(self::ensureWebPathFromFilename($media['filename'] ?? ''), '/');
        }
        return $rel;
    }

    /**
     * Sucht die groesste verfuegbare Variante <= $maxWidth aus den Standard-Breiten.
     */
    public static function bestUnder(array $media, int $maxWidth, string $format = 'jpg'): ?string
    {
        foreach (self::WIDTHS as $w) {
            if ($w <= $maxWidth) return self::variantUrl($media, $w, $format);
        }
        return self::variantUrl($media, end(self::WIDTHS), $format);
    }

    /**
     * Erzeugt das public-Pfad-Schema, ohne zu pruefen ob die Datei existiert.
     */
    private static function variantPath(array $media, int $width, string $format): ?string
    {
        $base = self::baseFromFilename($media['filename'] ?? '');
        if ($base === null) {
            // Vor-Resize-Stand: filename zeigt direkt auf eine Datei.
            $existing = (string) ($media['filename'] ?? '');
            if ($existing === '') return null;
            return '/uploads/' . ltrim($existing, '/');
        }
        return '/assets/images/from-original/' . $base . '-' . $width . '.' . $format;
    }

    /**
     * `from-original/foo-1600.jpg`  ->  `foo`
     * Returns null when filename does not match the known variant pattern.
     */
    private static function baseFromFilename(string $filename): ?string
    {
        if (!preg_match('#^from-original/(?<base>.+?)-\d+\.(?:jpe?g|webp)$#i', $filename, $m)) {
            return null;
        }
        return $m['base'];
    }

    /**
     * Versucht, das Original-File in uploads/ zu finden (verschiedene
     * Naming-Conventions werden geprueft: exakt, lowercase, base-prefix).
     */
    private static function findOriginal(array $media): ?string
    {
        $uploadsDir = self::baseDir() . '/uploads';
        if (!is_dir($uploadsDir)) return null;

        $base = self::baseFromFilename($media['filename'] ?? '');
        $tryNames = array_filter([
            $media['original_name'] ?? null,
            $base ? $base . '.jpg' : null,
            $base ? $base . '.jpeg' : null,
            $base ? $base . '.png' : null,
            $base ? $base . '.webp' : null,
        ]);
        foreach ($tryNames as $name) {
            $candidate = $uploadsDir . '/' . $name;
            if (is_file($candidate)) return $candidate;
        }
        // Case-insensitive Suche
        if ($base) {
            $low = strtolower($base);
            foreach ((scandir($uploadsDir) ?: []) as $f) {
                if ($f === '.' || $f === '..' || str_starts_with($f, '.')) continue;
                if (strtolower(pathinfo($f, PATHINFO_FILENAME)) === $low
                    || str_starts_with(strtolower(pathinfo($f, PATHINFO_FILENAME)), $low)) {
                    return $uploadsDir . '/' . $f;
                }
            }
        }
        return null;
    }

    /**
     * Erzeugt die Variante on-the-fly aus dem Original in uploads/.
     */
    private static function generateOnDemand(array $media, int $width, string $format): void
    {
        if (!extension_loaded('gd')) return;
        $base = self::baseFromFilename($media['filename'] ?? '');
        if ($base === null) return;
        $orig = self::findOriginal($media);
        if (!$orig) return;

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = (string) $finfo->file($orig);

        $img = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($orig),
            'image/png'  => @imagecreatefrompng($orig),
            'image/webp' => @imagecreatefromwebp($orig),
            'image/gif'  => @imagecreatefromgif($orig),
            default      => false,
        };
        if (!$img) return;

        $srcW = imagesx($img);
        $srcH = imagesy($img);
        $w    = min($width, $srcW);
        $h    = (int) round($srcH * ($w / $srcW));

        $dst = imagecreatetruecolor($w, $h);
        if ($mime === 'image/png') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $w, $h, $srcW, $srcH);

        $outDir = self::baseDir() . '/public/assets/images/from-original';
        if (!is_dir($outDir)) @mkdir($outDir, 0755, true);

        $target = $outDir . '/' . $base . '-' . $width . '.' . $format;
        if ($format === 'webp') {
            @imagewebp($dst, $target, 82);
        } else {
            @imagejpeg($dst, $target, 82);
        }
        imagedestroy($dst);
        imagedestroy($img);
    }

    private static function ensureWebPathFromFilename(string $filename): string
    {
        if ($filename === '') return '';
        if (str_starts_with($filename, '/')) return $filename;
        // Bekannte Praefixe
        if (str_starts_with($filename, 'from-original/')
            || str_starts_with($filename, 'assets/')) {
            return '/' . ltrim($filename, '/');
        }
        // Klassischer Upload-Pfad (Datum-Hierarchie aus MediaController::upload)
        return '/uploads/' . ltrim($filename, '/');
    }

    private static function baseDir(): string
    {
        return $GLOBALS['config']['base_dir'] ?? dirname(__DIR__);
    }
}
