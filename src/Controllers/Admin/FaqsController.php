<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;

final class FaqsController
{
    public function index(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('faqs_index', [
            'faqs' => $GLOBALS['faqRepo']->all(),
        ]);
    }

    public function create(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('faqs_edit', ['faq' => null]);
    }

    public function store(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/faqs');
        }
        $id = $GLOBALS['faqRepo']->save($this->payload());
        flash('success', 'FAQ angelegt.');
        redirect('/admin/faqs/' . $id . '/edit');
    }

    public function edit(array $args): void
    {
        Auth::requireLogin();
        $faq = $GLOBALS['faqRepo']->find((int) ($args['id'] ?? 0));
        if (!$faq) {
            redirect('/admin/faqs');
        }
        echo View::admin('faqs_edit', ['faq' => $faq]);
    }

    public function update(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/faqs');
        }
        $id = (int) ($args['id'] ?? 0);
        $data = $this->payload();
        $data['id'] = $id;
        $GLOBALS['faqRepo']->save($data);
        flash('success', 'FAQ gespeichert.');
        redirect('/admin/faqs/' . $id . '/edit');
    }

    public function delete(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            redirect('/admin/faqs');
        }
        $GLOBALS['faqRepo']->delete((int) ($args['id'] ?? 0));
        flash('success', 'FAQ gelöscht.');
        redirect('/admin/faqs');
    }

    private function payload(): array
    {
        return [
            'question'    => (string) post('question', ''),
            'answer_html' => safe_html((string) post('answer_html', '')),
            'page_slug'   => (string) post('page_slug', 'home'),
            'is_active'   => post('is_active') !== null ? 1 : 0,
            'sort_order'  => (int) post('sort_order', '0'),
        ];
    }
}
