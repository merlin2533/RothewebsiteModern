<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\AuditLogger;
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
            AuditLogger::log('auth.login', 'user', null, ['username' => $username]);
            redirect('/admin');
        }
        AuditLogger::log('auth.login_failed', 'user', null, ['username' => $username]);
        flash('error', 'Login fehlgeschlagen oder zu viele Fehlversuche.');
        redirect('/admin/login');
    }

    public function logout(array $args): void
    {
        AuditLogger::log('auth.logout');
        Auth::logout();
        redirect('/admin/login');
    }

    public function showAccount(array $args): void
    {
        Auth::requireLogin();
        echo View::admin('account', [
            'user' => Auth::user(),
        ]);
    }

    public function changePassword(array $args): void
    {
        Auth::requireLogin();
        if (!Csrf::verify(post('_token'))) {
            flash('error', 'Ungültiges Sicherheits-Token.');
            redirect('/admin/account');
        }

        $current = (string) post('current_password', '');
        $new     = (string) post('new_password', '');
        $confirm = (string) post('confirm_password', '');

        if ($new !== $confirm) {
            flash('error', 'Die beiden neuen Passwörter stimmen nicht überein.');
            redirect('/admin/account');
        }
        if (strlen($new) < 12) {
            flash('error', 'Das neue Passwort muss mindestens 12 Zeichen haben.');
            redirect('/admin/account');
        }
        if (!preg_match('/[A-Z]/', $new) || !preg_match('/[a-z]/', $new)
            || !preg_match('/\d/', $new)) {
            flash('error', 'Das neue Passwort muss Groß- und Kleinbuchstaben sowie eine Ziffer enthalten.');
            redirect('/admin/account');
        }

        $user = Auth::user();
        if (!$user) {
            redirect('/admin/login');
        }

        $pdo = $GLOBALS['pdo'];
        $row = $pdo->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
        $row->execute([$user['id']]);
        $hash = (string) ($row->fetch()['password_hash'] ?? '');

        if (!password_verify($current, $hash)) {
            flash('error', 'Das aktuelle Passwort stimmt nicht.');
            redirect('/admin/account');
        }

        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([$newHash, $user['id']]);

        AuditLogger::log('auth.password_changed', 'user', (int) $user['id']);
        flash('success', 'Passwort geändert.');
        redirect('/admin/account');
    }
}
