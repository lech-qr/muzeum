<?php

ini_set('session.save_path', __DIR__ . '/sessions');
ini_set('session.use_only_cookies', 1);
require_once 'config.php';
require_once 'auth.php';
requireLogin();

$tableName = $_GET['t'] ?? '';
$id        = $_GET['id'] ?? null;

if (!array_key_exists($tableName, TABLES) || $id === null) {
    header('Location: dashboard.php');
    exit;
}

$cfg = TABLES[$tableName];
$pk  = $cfg['primary_key'];

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("DELETE FROM `$tableName` WHERE `$pk` = ?");
    $stmt->execute([$id]);
} catch (Exception $e) {
    // loguj błąd jeśli potrzebujesz
}

header("Location: table.php?t=" . urlencode($tableName) . "&msg=deleted");
exit;
