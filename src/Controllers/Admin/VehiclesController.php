<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\AuditLogger;
use App\Core\Csrf;
use App\Core\View;

final class VehiclesController
{
    public function index(array $args): void
    {
        Auth::requireLogin();
        $vehicles = $GLOBALS['vehicleRepo']->all();
        echo View::admin('vehicles_index', ['vehicles' => $vehicles]);
    }

    public function create(array $args): void
    {
        Auth::requireLogin();
        $media = $GLOBALS['mediaRepo']->all();
        echo View::admin('vehicles_edit', [
            'vehicle' => null,
            'media'   => $media,
        ]);
    }

    public function store(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/vehicles');
        }
        $payload = $this->payload();
        $id = $GLOBALS['vehicleRepo']->save($payload);
        AuditLogger::log('vehicle.create', 'vehicle', $id, ['slug' => $payload['slug']]);
        flash('success', 'Fahrzeug angelegt.');
        redirect('/admin/vehicles/' . $id . '/edit');
    }

    public function edit(array $args): void
    {
        Auth::requireLogin();
        $id = (int) ($args['id'] ?? 0);
        $vehicle = $GLOBALS['vehicleRepo']->find($id);
        if (!$vehicle) {
            redirect('/admin/vehicles');
        }
        $media = $GLOBALS['mediaRepo']->all();
        echo View::admin('vehicles_edit', [
            'vehicle' => $vehicle,
            'media'   => $media,
        ]);
    }

    public function update(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/vehicles');
        }
        $id = (int) ($args['id'] ?? 0);
        $data = $this->payload();
        $data['id'] = $id;
        $GLOBALS['vehicleRepo']->save($data);
        AuditLogger::log('vehicle.update', 'vehicle', $id, ['slug' => $data['slug']]);
        flash('success', 'Fahrzeug gespeichert.');
        redirect('/admin/vehicles/' . $id . '/edit');
    }

    public function delete(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/vehicles');
        }
        $id = (int) ($args['id'] ?? 0);
        $GLOBALS['vehicleRepo']->delete($id);
        AuditLogger::log('vehicle.delete', 'vehicle', $id);
        flash('success', 'Fahrzeug gelöscht.');
        redirect('/admin/vehicles');
    }

    private function payload(): array
    {
        return [
            'slug'             => (string) post('slug', ''),
            'name'             => (string) post('name', ''),
            'marketing_text'   => post('marketing_text'),
            'length_m'         => post('length_m'),
            'width_m'          => post('width_m'),
            'height_m'         => post('height_m'),
            'payload_kg'       => post('payload_kg'),
            'euro_pallets'     => post('euro_pallets'),
            'axles'            => post('axles'),
            'special_features' => post('special_features'),
            'image_id'         => post('image_id'),
            'is_active'        => post('is_active') !== null ? 1 : 0,
            'sort_order'       => (int) post('sort_order', '0'),
        ];
    }
}
