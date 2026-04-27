<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\AuditLogger;
use App\Core\Csrf;
use App\Core\View;

final class RedirectsController
{
    public function index(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('redirects_index', [
            'rows' => $GLOBALS['redirectRepo']->all(),
        ]);
    }

    public function create(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('redirects_edit', ['r' => null]);
    }

    public function store(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) redirect('/admin/redirects');
        $id = $GLOBALS['redirectRepo']->save($this->payload());
        AuditLogger::log('redirect.create', 'redirect', $id, [
            'from' => post('from_path'), 'to' => post('to_path'),
        ]);
        flash('success', 'Redirect angelegt.');
        redirect('/admin/redirects');
    }

    public function edit(array $args): void
    {
        Auth::requireLogin();
        $r = $GLOBALS['redirectRepo']->find((int) ($args['id'] ?? 0));
        if (!$r) redirect('/admin/redirects');
        echo View::admin('redirects_edit', ['r' => $r]);
    }

    public function update(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) redirect('/admin/redirects');
        $id = (int) ($args['id'] ?? 0);
        $data = $this->payload();
        $data['id'] = $id;
        $GLOBALS['redirectRepo']->save($data);
        AuditLogger::log('redirect.update', 'redirect', $id);
        flash('success', 'Redirect gespeichert.');
        redirect('/admin/redirects');
    }

    public function delete(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) redirect('/admin/redirects');
        $id = (int) ($args['id'] ?? 0);
        $GLOBALS['redirectRepo']->delete($id);
        AuditLogger::log('redirect.delete', 'redirect', $id);
        flash('success', 'Redirect gelöscht.');
        redirect('/admin/redirects');
    }

    private function payload(): array
    {
        return [
            'from_path'   => (string) post('from_path', ''),
            'to_path'     => (string) post('to_path', ''),
            'status_code' => (int) post('status_code', '301'),
            'is_active'   => post('is_active') !== null ? 1 : 0,
        ];
    }
}
