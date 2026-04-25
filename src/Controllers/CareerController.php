<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class CareerController
{
    public function index(array $args): void
    {
        $page = $GLOBALS['pageRepo']->findBySlug('karriere');
        echo View::render('career', [
            'page'        => $page,
            'currentSlug' => 'karriere',
        ]);
    }
}
