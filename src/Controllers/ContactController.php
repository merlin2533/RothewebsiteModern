<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class ContactController
{
    public function index(array $args): void
    {
        $page = $GLOBALS['pageRepo']->findBySlug('kontakt');
        echo View::render('contact', [
            'page'        => $page,
            'currentSlug' => 'kontakt',
        ]);
    }
}
