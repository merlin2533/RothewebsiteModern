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
        echo View::render('vehicle_detail', [
            'vehicle'     => $vehicle,
            'allVehicles' => $allVehicles,
            'currentSlug' => 'fahrzeuge',
            'page'        => [
                'title'            => $vehicle['name'],
                'meta_title'       => $vehicle['name'] . ' – Fuhrpark | Rothe-Transporte',
                'meta_description' => $vehicle['marketing_text']
                    ? mb_substr(strip_tags($vehicle['marketing_text']), 0, 155)
                    : '',
                'hero_headline'    => $vehicle['name'],
                'hero_sub'         => $vehicle['special_features'] ?? '',
            ],
        ]);
    }
}
