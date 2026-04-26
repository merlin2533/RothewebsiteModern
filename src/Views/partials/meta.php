<?php
/** @var array|null $page */
/** @var array|null $vehicle */
/** @var array<int,array>|null $structured_data */

declare(strict_types=1);

$cfg = $GLOBALS['config'] ?? [];
$siteUrl = rtrim((string) ($cfg['site_url'] ?? ''), '/');
$reqPath = strtok((string) ($_SERVER['REQUEST_URI'] ?? '/'), '?');
$canonical = $siteUrl . $reqPath;

$title = $page['meta_title'] ?? $page['title'] ?? setting('meta_default_title', 'Rothe-Transporte');
$description = $page['meta_description'] ?? setting('meta_default_description', '');
$noindex = !empty($page['noindex']);

$ogImage = $siteUrl . '/assets/images/placeholders/og-default.svg';
if (!empty($page['og_image_id'])) {
    $m = $GLOBALS['mediaRepo']->find((int) $page['og_image_id']);
    if ($m) {
        $ogImage = $siteUrl . '/uploads/' . ltrim((string) $m['filename'], '/');
    }
}

$companyName  = setting('company_name', 'Rothe-Transporte und Speditions GbR');
$phone        = setting('phone', '');
$phoneE164    = setting('phone_e164', '');
$email        = setting('email', '');
$street       = setting('address_street', '');
$zip          = setting('address_zip', '');
$city         = setting('address_city', '');
$country      = setting('address_country', 'Deutschland');
$lat          = setting('geo_lat', '');
$lng          = setting('geo_lng', '');
$opening      = setting('opening_hours_schema', 'Mo-Fr 07:00-17:00');
$founded      = setting('founded_year', '1978');
$areasServed  = array_map('trim', explode(',', (string) setting('areas_served', 'DE')));

$localBusiness = [
    '@context' => 'https://schema.org',
    '@type'    => 'MovingCompany',
    'name'     => $companyName,
    'url'      => $siteUrl,
    'image'    => $ogImage,
    'telephone'=> $phoneE164 ?: $phone,
    'email'    => $email,
    'foundingDate' => $founded,
    'address'  => [
        '@type' => 'PostalAddress',
        'streetAddress'   => $street,
        'postalCode'      => $zip,
        'addressLocality' => $city,
        'addressCountry'  => 'DE',
    ],
    'areaServed' => array_map(static fn($c) => ['@type' => 'Country', 'name' => $c], $areasServed),
    'openingHoursSpecification' => [[
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'opens' => '07:00',
        'closes' => '17:00',
    ]],
];
if ($lat !== '' && $lng !== '') {
    $localBusiness['geo'] = [
        '@type'    => 'GeoCoordinates',
        'latitude' => (float) $lat,
        'longitude'=> (float) $lng,
    ];
}

$organization = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => $companyName,
    'url'      => $siteUrl,
    'logo'     => $siteUrl . '/assets/images/logo.svg',
    'contactPoint' => [[
        '@type' => 'ContactPoint',
        'telephone' => $phoneE164 ?: $phone,
        'contactType' => 'customer service',
        'areaServed' => $areasServed,
        'availableLanguage' => ['de'],
    ]],
];

$schemas = [$localBusiness, $organization];
if (isset($structured_data) && is_array($structured_data)) {
    foreach ($structured_data as $s) {
        $schemas[] = $s;
    }
}
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<?php if ($noindex): ?>
<meta name="robots" content="noindex,follow">
<?php else: ?>
<meta name="robots" content="index,follow,max-image-preview:large">
<?php endif; ?>

<meta property="og:type" content="website">
<meta property="og:locale" content="de_DE">
<meta property="og:site_name" content="<?= e($companyName) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($title) ?>">
<meta name="twitter:description" content="<?= e($description) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">

<meta name="theme-color" content="#0B2545">
<link rel="icon" type="image/svg+xml" href="/assets/icons/favicon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
<link rel="alternate icon" href="/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/manifest.webmanifest">

<link rel="preload" as="font" type="font/woff2" href="/assets/fonts/Inter-Variable.woff2" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="/assets/fonts/FamiljenGrotesk-Variable.woff2" crossorigin>
<link rel="stylesheet" href="/assets/css/styles.css">

<?php foreach ($schemas as $schema): ?>
<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<?php endforeach; ?>
