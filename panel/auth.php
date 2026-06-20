<?php
function requireLogin(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.save_path', __DIR__ . '/sessions');
        ini_set('session.use_only_cookies', 1);
        session_start();
    }
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

function getCurrentUser(): array {
    return [
        'user_id' => $_SESSION['user_id'],
        'login'   => $_SESSION['login'],
        'imie'    => $_SESSION['imie'],
    ];
}
