<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;

final class ServicesController
{
    public function index(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('services_index', ['services' => $GLOBALS['serviceRepo']->all()]);
    }

    public function create(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('services_edit', ['service' => null]);
    }

    public function store(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/services');
        }
        $id = $GLOBALS['serviceRepo']->save($this->payload());
        flash('success', 'Leistung angelegt.');
        redirect('/admin/services/' . $id . '/edit');
    }

    public function edit(array $args): void
    {
        Auth::requireLogin();
        $service = $GLOBALS['serviceRepo']->find((int) ($args['id'] ?? 0));
        if (!$service) {
            redirect('/admin/services');
        }
        echo View::admin('services_edit', ['service' => $service]);
    }

    public function update(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/services');
        }
        $id = (int) ($args['id'] ?? 0);
        $data = $this->payload();
        $data['id'] = $id;
        $GLOBALS['serviceRepo']->save($data);
        flash('success', 'Leistung gespeichert.');
        redirect('/admin/services/' . $id . '/edit');
    }

    public function delete(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/services');
        }
        $GLOBALS['serviceRepo']->delete((int) ($args['id'] ?? 0));
        flash('success', 'Leistung gelöscht.');
        redirect('/admin/services');
    }

    private function payload(): array
    {
        return [
            'slug'       => (string) post('slug', ''),
            'title'      => (string) post('title', ''),
            'summary'    => post('summary'),
            'body_html'  => safe_html((string) post('body_html', '')),
            'icon_key'   => post('icon_key'),
            'is_active'  => post('is_active') !== null ? 1 : 0,
            'sort_order' => (int) post('sort_order', '0'),
        ];
    }
}
