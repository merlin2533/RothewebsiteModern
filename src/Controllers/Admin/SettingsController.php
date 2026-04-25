<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;

final class SettingsController
{
    public function edit(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('settings', [
            'settings' => $GLOBALS['settingRepo']->all(),
            'media'    => $GLOBALS['mediaRepo']->all(),
        ]);
    }

    public function update(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/settings');
        }
        $keys = [
            'company_name', 'owners',
            'address_street', 'address_zip', 'address_city', 'address_country',
            'phone', 'phone_e164', 'email',
            'founded_year', 'geo_lat', 'geo_lng', 'opening_hours', 'opening_hours_schema',
            'areas_served',
            'meta_default_title', 'meta_default_description',
            'og_default_image_id', 'hero_image_id',
            'owner_quote', 'owner_quote_attribution',
        ];
        $data = [];
        foreach ($keys as $k) {
            $data[$k] = (string) post($k, '');
        }
        $GLOBALS['settingRepo']->setMany($data);
        flash('success', 'Einstellungen gespeichert.');
        redirect('/admin/settings');
    }
}
