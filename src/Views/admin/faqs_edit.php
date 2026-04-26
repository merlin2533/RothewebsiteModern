<?php
/** @var array|null $faq */
declare(strict_types=1);
$isNew = $faq === null;
$action = $isNew ? '/admin/faqs' : '/admin/faqs/' . (int) $faq['id'];
?>
<div class="page-head">
    <h1><?= $isNew ? 'Neue FAQ' : 'FAQ bearbeiten' ?></h1>
    <a class="btn-ghost" href="/admin/faqs">← Zurück</a>
</div>

<form method="post" action="<?= e($action) ?>" novalidate>
    <?= \App\Core\Csrf::field() ?>

    <div class="form-group">
        <label class="form-label" for="question">Frage</label>
        <input class="form-input" type="text" id="question" name="question"
               required maxlength="180"
               value="<?= e($faq['question'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label class="form-label" for="answer_html">Antwort (HTML erlaubt: p, h2, h3, h4, ul, ol, li, strong, em, a, br, blockquote)</label>
        <textarea class="form-input" id="answer_html" name="answer_html" rows="8" required><?= e($faq['answer_html'] ?? '') ?></textarea>
    </div>

    <div class="input-row">
        <div class="form-group">
            <label class="form-label" for="page_slug">Seite</label>
            <select class="form-input" id="page_slug" name="page_slug">
                <?php foreach (['home' => 'Startseite', 'leistungen' => 'Leistungen', 'fahrzeuge' => 'Fahrzeuge', 'kontakt' => 'Kontakt', 'karriere' => 'Karriere'] as $slug => $label): ?>
                    <option value="<?= e($slug) ?>" <?= ($faq['page_slug'] ?? 'home') === $slug ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="sort_order">Sortierung</label>
            <input class="form-input" type="number" id="sort_order" name="sort_order"
                   value="<?= e((string) ($faq['sort_order'] ?? 0)) ?>">
        </div>

        <div class="form-group form-group--checkbox">
            <label class="form-label" for="is_active">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       <?= ($faq === null || (int) $faq['is_active'] === 1) ? 'checked' : '' ?>>
                Aktiv (sichtbar im Frontend)
            </label>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">Speichern</button>
    </div>
</form>
