<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\AuditLogger;
use App\Core\Csrf;
use App\Core\View;

final class TimelineController
{
    public function index(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('timeline_index', ['events' => $GLOBALS['timelineRepo']->all()]);
    }

    public function create(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('timeline_edit', ['event' => null]);
    }

    public function store(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/timeline');
        }
        $payload = $this->payload();
        $id = $GLOBALS['timelineRepo']->save($payload);
        AuditLogger::log('timeline.create', 'timeline', $id);
        flash('success', 'Eintrag angelegt.');
        redirect('/admin/timeline/' . $id . '/edit');
    }

    public function edit(array $args): void
    {
        Auth::requireLogin();
        $event = $GLOBALS['timelineRepo']->find((int) ($args['id'] ?? 0));
        if (!$event) {
            redirect('/admin/timeline');
        }
        echo View::admin('timeline_edit', ['event' => $event]);
    }

    public function update(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/timeline');
        }
        $id = (int) ($args['id'] ?? 0);
        $data = $this->payload();
        $data['id'] = $id;
        $GLOBALS['timelineRepo']->save($data);
        AuditLogger::log('timeline.update', 'timeline', $id);
        flash('success', 'Eintrag gespeichert.');
        redirect('/admin/timeline/' . $id . '/edit');
    }

    public function delete(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/timeline');
        }
        $tid = (int) ($args['id'] ?? 0);
        $GLOBALS['timelineRepo']->delete($tid);
        AuditLogger::log('timeline.delete', 'timeline', $tid);
        flash('success', 'Eintrag gelöscht.');
        redirect('/admin/timeline');
    }

    private function payload(): array
    {
        return [
            'year'        => (int) post('year', '0'),
            'title'       => (string) post('title', ''),
            'description' => post('description'),
            'is_active'   => post('is_active') !== null ? 1 : 0,
            'sort_order'  => (int) post('sort_order', '0'),
        ];
    }
}
