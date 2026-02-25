<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT r.*, d.nazwa AS dzial_nazwa
    FROM muzealny r
    LEFT JOIN dzialy d ON r.dzialy_id = d.id
    WHERE r.id = :id
");
$stmt->execute(['id' => $id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Nie znaleziono rekordu.");
}
// Nawigacja między Kartami - Poprzednia / Następna
// Następny rekord
$stmtNext = $pdo->prepare("
    SELECT r.id, r.przedmiot
    FROM muzealny r
    WHERE r.id > :id
    ORDER BY r.id ASC
    LIMIT 1
");
$stmtNext->execute(['id' => $id]);
$next = $stmtNext->fetch(PDO::FETCH_ASSOC);
// Poprzedni rekord
$stmtPrev = $pdo->prepare("
    SELECT r.id, r.przedmiot
    FROM muzealny r
    WHERE r.id < :id
    ORDER BY r.id DESC
    LIMIT 1
");
$stmtPrev->execute(['id' => $id]);
$prev = $stmtPrev->fetch(PDO::FETCH_ASSOC);
// Slug (musi być taki jak w app….js)
function slugify($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = preg_replace('/(^-|-$)+/', '', $text);
    return $text;
}
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Karta przedmiotu - Katalog muzealny</title>
        <?php require_once '../assets/php/head.php'; ?>
    </head>
    <body class="card-page">
        <button type="button" class="btn btn-success btn-lg print-btn" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"></path>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"></path>
            </svg>    
        Drukuj</button>
        <button type="button" class="btn btn-primary btn-lg link-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link" viewBox="0 0 16 16">
                <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9q-.13 0-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"/>
                <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4 4 0 0 1-.82 1H12a3 3 0 1 0 0-6z"/>
            </svg>    
        Udostępnij</button>
        <!-- Przyciski Poprzedni / Następny -->
        <?php if ($prev): ?>
            <a class="nav-btn prev"
            href="/katalog-muzealny/<?= $prev['id'] ?>-<?= slugify($prev['przedmiot']) ?>">
            ← Poprzednia karta
            </a>
        <?php endif; ?>

        <?php if ($next): ?>
            <a class="nav-btn next"
            href="/katalog-muzealny/<?= $next['id'] ?>-<?= slugify($next['przedmiot']) ?>">
            Następna karta →
            </a>
        <?php endif; ?>

        <div class="a4">

            <header>
                <h3>Muzeum Zamojskie w Zamościu</h3>
                <p>Karta katalogowa eksponatu <strong>#<?= htmlspecialchars($item['id']) ?></strong> - nr. inw. <strong><?= htmlspecialchars($item['nr_inwentarzowy']) ?></strong> - Katalog Muzealny</p>
            </header>

            <h1>Karta obiektu</h1>

            <div class="field">
                <span class="label">Numer inwentarzowy:</span>
                <?= htmlspecialchars($item['nr_inwentarzowy']) ?>
            </div>

            <div class="field">
                <span class="label">Nazwa:</span>
                <?= htmlspecialchars($item['przedmiot']) ?>
            </div>

            <div class="field">
                <span class="label">Dział:</span>
                <?= htmlspecialchars($item['dzial_nazwa']) ?>
            </div>

            <div class="field">
                <span class="label">Opis:</span>
                <br>
                <?= nl2br(htmlspecialchars($item['opis'])) ?>
            </div>

            <?php if (!empty($item['zdjecie_lokalne'])): ?>
                <div style="margin-top:20px;">
                    <img src="../images/muzealny/<?= $item['zdjecie_lokalne'] ?>" style="max-width:100%;">
                </div>
            <?php endif; ?>

            <footer>
                <p>&copy; Copy right by <strong class="headerTXT">Muzeum Zamojskie w Zamościu</strong></p>
                <p><?php if (!empty($item['data_i_wypelniajacy'])): ?>
                    Wypełniono: <strong><?= htmlspecialchars($item['data_i_wypelniajacy']) ?></strong>&nbsp;
                <?php endif; ?>
                Wygenerowano <strong><?= date('d.m.Y') ?></strong></p>
            </footer>
            <!-- kontener na komunikat -->
            <div class="position-fixed top-0 start-0 p-3" style="z-index: 1080">
                <div id="copyToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2500" data-bs-autohide="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            Adres karty został skopiowany…
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/js/card.js"></script>
    </body>
</html>
