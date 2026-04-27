<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\AuditLogger;
use App\Core\Csrf;
use App\Core\View;

final class LandingPagesController
{
    public function index(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('landing_pages_index', [
            'rows' => $GLOBALS['landingRepo']->all(),
        ]);
    }

    public function create(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('landing_pages_edit', ['lp' => null]);
    }

    public function store(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/landing-pages');
        }
        $id = $GLOBALS['landingRepo']->save($this->payload());
        AuditLogger::log('landing_page.create', 'landing_page', $id);
        flash('success', 'Landingpage angelegt.');
        redirect('/admin/landing-pages/' . $id . '/edit');
    }

    public function edit(array $args): void
    {
        Auth::requireLogin();
        $lp = $GLOBALS['landingRepo']->find((int) ($args['id'] ?? 0));
        if (!$lp) {
            redirect('/admin/landing-pages');
        }
        echo View::admin('landing_pages_edit', ['lp' => $lp]);
    }

    public function update(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/landing-pages');
        }
        $id = (int) ($args['id'] ?? 0);
        $data = $this->payload();
        $data['id'] = $id;
        $GLOBALS['landingRepo']->save($data);
        AuditLogger::log('landing_page.update', 'landing_page', $id);
        flash('success', 'Landingpage gespeichert.');
        redirect('/admin/landing-pages/' . $id . '/edit');
    }

    public function delete(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/landing-pages');
        }
        $id = (int) ($args['id'] ?? 0);
        $GLOBALS['landingRepo']->delete($id);
        AuditLogger::log('landing_page.delete', 'landing_page', $id);
        flash('success', 'Landingpage gelöscht.');
        redirect('/admin/landing-pages');
    }

    private function payload(): array
    {
        return [
            'slug'             => (string) post('slug', ''),
            'kind'             => (string) post('kind', 'industry'),
            'title'            => (string) post('title', ''),
            'eyebrow'          => post('eyebrow'),
            'headline'         => post('headline'),
            'sub'              => post('sub'),
            'intro_html'       => safe_html((string) post('intro_html', '')),
            'body_html'        => safe_html((string) post('body_html', '')),
            'meta_title'       => post('meta_title'),
            'meta_description' => post('meta_description'),
            'cta_label'        => (string) post('cta_label', 'Jetzt anfragen'),
            'primary_phone'    => post('primary_phone'),
            'primary_email'    => post('primary_email'),
            'related_vehicles' => post('related_vehicles'),
            'related_services' => post('related_services'),
            'region_name'      => post('region_name'),
            'is_published'     => post('is_published') !== null ? 1 : 0,
            'sort_order'       => (int) post('sort_order', '0'),
        ];
    }
}
