<?php
declare(strict_types=1);
$gtm = trim((string) setting('gtm_container_id', ''));
?>
<?php if ($gtm !== '' && preg_match('/^GTM-[A-Z0-9]+$/', $gtm)): ?>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=<?= e($gtm) ?>"
            height="0" width="0" style="display:none;visibility:hidden"
            title="Google Tag Manager"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<?php endif; ?>
