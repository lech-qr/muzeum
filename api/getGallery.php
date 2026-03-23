<?php
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

$id   = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'muzealny';

/* 🔒 Dozwolone katalogi (bezpieczeństwo!) */
$allowedTables = ['muzealny', 'archeologiczny'];

if (!in_array($type, $allowedTables)) {
    echo json_encode(['error' => 'Invalid type']);
    exit;
}

/* Aktualny rekord */
$stmt = $pdo->prepare("
    SELECT id, zdjecie_lokalne, przedmiot 
    FROM {$type}
    WHERE id = ?
");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode(['error' => 'Not found']);
    exit;
}

/* Poprzedni rekord */
$stmt = $pdo->prepare("
    SELECT id FROM {$type}
    WHERE id < ?
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([$id]);
$prev = $stmt->fetchColumn();

/* Następny rekord */
$stmt = $pdo->prepare("
    SELECT id FROM {$type}
    WHERE id > ?
    ORDER BY id ASC
    LIMIT 1
");
$stmt->execute([$id]);
$next = $stmt->fetchColumn();

/* Zdjęcie jako tablica */
$images = [];
if (!empty($item['zdjecie_lokalne'])) {
    $images[] = $item['zdjecie_lokalne'];
}

echo json_encode([
    'id'     => $item['id'],
    'images' => $images,
    'title'  => $item['przedmiot'],
    'prev'   => $prev ?: null,
    'next'   => $next ?: null
]);

exit;