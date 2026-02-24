<?php
require_once '../config.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT r.*, d.nazwa AS dzial_nazwa
    FROM rekordy r
    LEFT JOIN dzialy d ON r.dzialy_id = d.id
    WHERE r.id = :id
");
$stmt->execute(['id' => $id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Nie znaleziono rekordu.");
}
?>
