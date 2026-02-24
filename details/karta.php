<?php
require_once '../config.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM przedmioty WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    exit("Nie znaleziono rekordu.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Karta muzealna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2><?= htmlspecialchars($item['przedmiot']) ?></h2>

<hr>

<p><strong>Dział:</strong> <?= htmlspecialchars($item['dzial_nr']) ?></p>
<p><strong>Nr inwentarzowy:</strong> <?= htmlspecialchars($item['nr_inwentarzowy']) ?></p>
<p><strong>Opis:</strong><br><?= nl2br(htmlspecialchars($item['opis'])) ?></p>

<button onclick="window.print()" class="btn btn-secondary mt-3">🖨 Drukuj</button>

</body>
</html>
