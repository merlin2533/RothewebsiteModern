<?php
/**
 * Reusable image-picker form field.
 *
 * Required vars:
 *   $name       string  HTML name of the hidden input (e.g. "image_id")
 *   $value      ?int    Current media id
 *   $media      array   All media rows (for the modal grid)
 *   $label      string  Label text
 *
 * Optional:
 *   $allowEmpty bool    Show a "Kein Bild" option (default true)
 */
declare(strict_types=1);

/** @var string $name */
/** @var int|string|null $value */
/** @var array<int,array> $media */
/** @var string $label */
$allowEmpty = $allowEmpty ?? true;

$current = null;
$valueInt = (int) ($value ?? 0);
if ($valueInt > 0) {
    foreach ($media as $m) {
        if ((int) $m['id'] === $valueInt) { $current = $m; break; }
    }
}

$pickerId = 'mp-' . preg_replace('/[^a-z0-9]+/i', '-', $name) . '-' . substr(md5($name . $valueInt), 0, 6);
$thumb = $current ? \App\Core\MediaResolver::variantUrl($current, 800, 'jpg') : null;
?>
<div class="form-group media-picker" data-picker="<?= e($pickerId) ?>">
    <label class="form-label"><?= e($label) ?></label>

    <input type="hidden" name="<?= e($name) ?>" value="<?= e((string) $valueInt) ?>" data-picker-input>

    <div class="media-picker__current" data-picker-current>
        <?php if ($current): ?>
            <img src="<?= e((string) $thumb) ?>" alt="<?= e((string) ($current['alt_text'] ?? '')) ?>"
                 width="120" height="80" loading="lazy">
            <div class="media-picker__meta">
                <strong><?= e((string) ($current['original_name'] ?? $current['filename'])) ?></strong>
                <span class="muted">#<?= (int) $current['id'] ?> · <?= (int) ($current['width'] ?? 0) ?>×<?= (int) ($current['height'] ?? 0) ?></span>
            </div>
        <?php else: ?>
            <span class="muted">Kein Bild ausgewaehlt</span>
        <?php endif; ?>
    </div>

    <div class="media-picker__actions">
        <button type="button" class="btn-ghost btn-sm" data-picker-open="<?= e($pickerId) ?>">
            Bild aus Bibliothek waehlen
        </button>
        <?php if ($allowEmpty): ?>
            <button type="button" class="btn-ghost btn-sm" data-picker-clear="<?= e($pickerId) ?>">Entfernen</button>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div class="media-modal" id="<?= e($pickerId) ?>" hidden role="dialog" aria-modal="true" aria-labelledby="<?= e($pickerId) ?>-title">
        <div class="media-modal__backdrop" data-picker-close="<?= e($pickerId) ?>"></div>
        <div class="media-modal__panel">
            <header class="media-modal__head">
                <h2 id="<?= e($pickerId) ?>-title">Bild auswaehlen (<?= count($media) ?>)</h2>
                <input type="search" class="media-modal__search" placeholder="Suche nach Dateiname / Alt-Text…" data-picker-search>
                <button type="button" class="btn-ghost btn-sm" data-picker-close="<?= e($pickerId) ?>">Schliessen</button>
            </header>
            <div class="media-modal__grid" data-picker-grid>
                <?php foreach ($media as $m):
                    $thumb = \App\Core\MediaResolver::variantUrl($m, 800, 'jpg');
                    $w = (int) ($m['width'] ?? 0);
                    $h = (int) ($m['height'] ?? 0);
                ?>
                    <button type="button" class="media-tile"
                            data-picker-pick="<?= e($pickerId) ?>"
                            data-id="<?= (int) $m['id'] ?>"
                            data-thumb="<?= e((string) $thumb) ?>"
                            data-name="<?= e((string) ($m['original_name'] ?? $m['filename'])) ?>"
                            data-alt="<?= e((string) ($m['alt_text'] ?? '')) ?>"
                            data-w="<?= $w ?>"
                            data-h="<?= $h ?>"
                            data-search="<?= e(strtolower((string) ($m['original_name'] ?? '') . ' ' . ($m['alt_text'] ?? '') . ' ' . ($m['filename'] ?? ''))) ?>">
                        <img src="<?= e((string) $thumb) ?>" alt="" loading="lazy" width="200" height="140">
                        <span class="media-tile__name"><?= e((string) ($m['original_name'] ?? $m['filename'])) ?></span>
                        <small class="media-tile__meta">#<?= (int) $m['id'] ?><?= $w && $h ? ' · ' . $w . '×' . $h : '' ?></small>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
