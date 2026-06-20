<?php
define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}

// Konfiguracja tabel dostępnych w aplikacji
define('TABLES', [
    'archeologiczny' => [
        'label' => '🏺 Archeologiczny',
        'primary_key' => 'id',
        'search_columns' => ['nr_inwentarzowy', 'nazwa', 'przedmiot', 'miejscowosc'],
        'display_columns' => ['id', 'nr_inwentarzowy', 'przedmiot', 'szczegolowa_charakterystyka'],
    ],
    'biblioteczny' => [
        'label' => '📚 Biblioteczny',
        'primary_key' => 'id',
        'search_columns' => ['autor', 'tytul', 'nr_inwentarzowy'],
        'display_columns' => ['id', 'nr_inwentarzowy', 'autor', 'tytul'],
    ],
    'muzealny' => [
        'label' => '🏛️ Muzealny',
        'primary_key' => 'id',
        'search_columns' => ['nr_inwentarzowy', 'przedmiot', 'autor_szkola'],
        'display_columns' => ['id', 'nr_inwentarzowy', 'dzialy_id', 'przedmiot'],
    ],
]);
