<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class VehiclesController
{
    public function index(array $args): void
    {
        $page = $GLOBALS['pageRepo']->findBySlug('fahrzeuge');
        $vehicles = $GLOBALS['vehicleRepo']->allActive();

        echo View::render('vehicles', [
            'page'        => $page,
            'currentSlug' => 'fahrzeuge',
            'vehicles'    => $vehicles,
        ]);
    }

    public function show(array $args): void
    {
        $vehicle = $GLOBALS['vehicleRepo']->findBySlug((string) ($args['slug'] ?? ''));
        if (!$vehicle) {
            (new LegalController())->notFound([]);
            return;
        }
        $allVehicles = $GLOBALS['vehicleRepo']->allActive();

        $cfg = $GLOBALS['config'];
        $base = rtrim((string) $cfg['site_url'], '/');
        // Dynamic per-vehicle OG image (1200x630 PNG, file-cached)
        $ogUrl = $base . '/og/vehicle/' . $vehicle['slug'] . '.png';
        $imageUrl = $ogUrl;
        if (!empty($vehicle['image_filename'])) {
            $imageUrl = $base . '/uploads/' . ltrim((string) $vehicle['image_filename'], '/');
        }

        $additional = [];
        if (!empty($vehicle['length_m']))     $additional[] = ['@type' => 'PropertyValue', 'name' => 'Länge',         'value' => number_format((float) $vehicle['length_m'], 2, ',', '.') . ' m'];
        if (!empty($vehicle['width_m']))      $additional[] = ['@type' => 'PropertyValue', 'name' => 'Breite',        'value' => number_format((float) $vehicle['width_m'], 2, ',', '.') . ' m'];
        if (!empty($vehicle['height_m']))     $additional[] = ['@type' => 'PropertyValue', 'name' => 'Höhe',          'value' => number_format((float) $vehicle['height_m'], 2, ',', '.') . ' m'];
        if (!empty($vehicle['payload_kg']))   $additional[] = ['@type' => 'PropertyValue', 'name' => 'Nutzlast',      'value' => ($vehicle['payload_kg'] / 1000) . ' t'];
        if (!empty($vehicle['euro_pallets'])) $additional[] = ['@type' => 'PropertyValue', 'name' => 'Europaletten',  'value' => (string) $vehicle['euro_pallets']];
        if (!empty($vehicle['axles']))        $additional[] = ['@type' => 'PropertyValue', 'name' => 'Achsen',        'value' => (string) $vehicle['axles']];
        if (!empty($vehicle['special_features'])) $additional[] = ['@type' => 'PropertyValue', 'name' => 'Besonderheit', 'value' => (string) $vehicle['special_features']];

        $vehicleSchema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $vehicle['name'],
            'description' => $vehicle['marketing_text']
                ? mb_substr(strip_tags((string) $vehicle['marketing_text']), 0, 250)
                : (string) ($vehicle['special_features'] ?? ''),
            'category'    => 'Transport-Equipment',
            'image'       => $imageUrl,
            'url'         => $base . '/fahrzeuge/' . $vehicle['slug'],
            'brand'       => ['@type' => 'Brand', 'name' => setting('company_name')],
            'additionalProperty' => $additional,
        ];

        echo View::render('vehicle_detail', [
            'vehicle'         => $vehicle,
            'allVehicles'     => $allVehicles,
            'currentSlug'     => 'fahrzeuge',
            'structured_data' => [$vehicleSchema],
            'page'            => [
                'title'            => $vehicle['name'],
                'meta_title'       => $vehicle['name'] . ' – Fuhrpark | Rothe-Transporte',
                'meta_description' => $vehicle['marketing_text']
                    ? mb_substr(strip_tags($vehicle['marketing_text']), 0, 155)
                    : '',
                'hero_headline'    => $vehicle['name'],
                'hero_sub'         => $vehicle['special_features'] ?? '',
                'og_image_url'     => $ogUrl,
            ],
        ]);
    }
}
