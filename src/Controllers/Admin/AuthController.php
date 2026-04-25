<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;

final class AuthController
{
    public function showLogin(array $args): void
    {
        if (Auth::check()) {
            redirect('/admin');
        }
        echo View::admin('login', [
            'error' => flash('error'),
        ]);
    }

    public function login(array $args): void
    {
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/login');
        }
        $username = (string) post('username', '');
        $password = (string) post('password', '');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if (Auth::attempt($username, $password, $ip)) {
            redirect('/admin');
        }
        flash('error', 'Login fehlgeschlagen oder zu viele Fehlversuche.');
        redirect('/admin/login');
    }

    public function logout(array $args): void
    {
        Auth::logout();
        redirect('/admin/login');
    }
}
