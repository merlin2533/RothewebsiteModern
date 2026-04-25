<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class HomeController
{
    public function index(array $args): void
    {
        $page         = $GLOBALS['pageRepo']->findBySlug('home');
        $services     = $GLOBALS['serviceRepo']->allActive();
        $vehicles     = $GLOBALS['vehicleRepo']->allActive();

        echo View::render('home', [
            'page'           => $page,
            'currentSlug'    => 'home',
            'services'       => $services,
            'vehicles'       => array_slice($vehicles, 0, 2),
            'allVehicles'    => $vehicles,
        ]);
    }
}
