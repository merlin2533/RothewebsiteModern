<?php
declare(strict_types=1);
$gtm = trim((string) setting('gtm_container_id', ''));
$cats = (string) ($_COOKIE['rt_consent_v2'] ?? '');
$hasMarketing = in_array('marketing', array_map('trim', explode(',', $cats)), true);
?>
<?php if ($hasMarketing && $gtm !== '' && preg_match('/^GTM-[A-Z0-9]+$/', $gtm)): ?>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=<?= e($gtm) ?>"
            height="0" width="0" style="display:none;visibility:hidden"
            title="Google Tag Manager"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<?php endif; ?>
