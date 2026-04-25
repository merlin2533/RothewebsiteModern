<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;

final class PagesController
{
    public function index(array $args): void
    {
        Auth::requireLogin();
        $pages = $GLOBALS['pageRepo']->all();
        echo View::admin('pages_index', ['pages' => $pages]);
    }

    public function edit(array $args): void
    {
        Auth::requireLogin();
        $id = (int) ($args['id'] ?? 0);
        $page = $GLOBALS['pageRepo']->find($id);
        if (!$page) {
            flash('error', 'Seite nicht gefunden.');
            redirect('/admin/pages');
        }
        $media = $GLOBALS['mediaRepo']->all();
        echo View::admin('pages_edit', [
            'page'  => $page,
            'media' => $media,
        ]);
    }

    public function update(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/pages');
        }
        $id = (int) ($args['id'] ?? 0);
        $existing = $GLOBALS['pageRepo']->find($id);
        if (!$existing) {
            redirect('/admin/pages');
        }
        $data = [
            'id'               => $id,
            'slug'             => (string) post('slug', $existing['slug']),
            'title'            => (string) post('title', $existing['title']),
            'meta_title'       => post('meta_title'),
            'meta_description' => post('meta_description'),
            'hero_headline'    => post('hero_headline'),
            'hero_sub'         => post('hero_sub'),
            'hero_image_id'    => post('hero_image_id') ? (int) post('hero_image_id') : null,
            'og_image_id'      => post('og_image_id') ? (int) post('og_image_id') : null,
            'content_html'     => safe_html((string) post('content_html', '')),
            'is_published'     => post('is_published') !== null ? 1 : 0,
            'sort_order'       => (int) post('sort_order', '0'),
        ];
        $GLOBALS['pageRepo']->save($data);
        flash('success', 'Seite gespeichert.');
        redirect('/admin/pages/' . $id . '/edit');
    }
}
