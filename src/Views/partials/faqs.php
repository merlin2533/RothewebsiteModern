<?php
/** @var array<int,array> $faqs */
declare(strict_types=1);
if (empty($faqs)) {
    return;
}
?>
<section class="section section--surface faq" id="faq" aria-labelledby="faq-h" data-reveal>
    <div class="container container--narrow">
        <header class="section__head">
            <p class="eyebrow">Häufige Fragen</p>
            <h2 id="faq-h">Antworten auf einen Blick</h2>
            <p>Was Anrufer:innen am häufigsten wissen wollen – kurz und sachlich.</p>
        </header>

        <div class="faq-list">
            <?php foreach ($faqs as $i => $faq): ?>
                <details class="faq-item" <?= $i === 0 ? 'open' : '' ?>>
                    <summary class="faq-item__q">
                        <span><?= e($faq['question']) ?></span>
                        <svg width="20" height="20" aria-hidden="true" class="faq-item__chev">
                            <use href="#icon-arrow-right"/>
                        </svg>
                    </summary>
                    <div class="faq-item__a prose">
                        <?= $faq['answer_html'] ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
