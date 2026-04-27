<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\AuditLogger;
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
            'company_name', 'owners', 'ceo_name',
            'address_street', 'address_zip', 'address_city', 'address_country',
            'phone', 'phone_e164', 'email',
            'founded_year', 'geo_lat', 'geo_lng', 'opening_hours', 'opening_hours_schema',
            'areas_served', 'vat_id',
            'meta_default_title', 'meta_default_description',
            'og_default_image_id', 'hero_image_id',
            'owner_quote', 'owner_quote_attribution',
            'twitter_handle', 'search_action_target',
            'gtm_container_id', 'plausible_domain', 'plausible_script_url',
            'plausible_events_api',
            'matomo_url', 'matomo_site_id',
            'ga4_measurement_id', 'ga4_api_secret',
            'meta_pixel_id', 'meta_capi_token',
        ];
        $data = [];
        foreach ($keys as $k) {
            $data[$k] = (string) post($k, '');
        }
        $GLOBALS['settingRepo']->setMany($data);
        AuditLogger::log('settings.update', 'settings', null, ['keys' => array_keys($data)]);
        flash('success', 'Einstellungen gespeichert.');
        redirect('/admin/settings');
    }
}
