<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

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
            <a class="nav-btn prev" title="Poprzednia karta"
            href="/katalog-muzealny/<?= $prev['id'] ?>-<?= slugify($prev['przedmiot']) ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-square" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm11.5 5.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </a>
        <?php endif; ?>

        <?php if ($next): ?>
            <a class="nav-btn next" title="Następna karta"
            href="/katalog-muzealny/<?= $next['id'] ?>-<?= slugify($next['przedmiot']) ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-square" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm4.5 5.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/>
            </svg>
            </a>
        <?php endif; ?>

        <div class="a4">

            <header>
                <h3>Muzeum Zamojskie w Zamościu</h3>
                <p>Karta katalogowa eksponatu <strong>#<?= htmlspecialchars($item['id']) ?></strong> 
                <?php if (!empty($item['nr_inwentarzowy'])): ?>
                    - nr. inw. <strong><?= htmlspecialchars($item['nr_inwentarzowy']) ?></strong>
                <?php endif; ?>
                - Katalog Muzealny</p>
            </header>
            <article>
                <?php if (!empty($item['przedmiot'])): ?>
                    <h1><?= htmlspecialchars($item['przedmiot']) ?>
                        <?php if (!empty($item['dzial_nazwa'])): ?>
                            <span>(<?= htmlspecialchars($item['dzial_nazwa']) ?>)</span>
                        <?php endif; ?>    
                    </h1>
                <?php endif; ?>
                <div class="description">
                    <?php if (
                        !empty($item['autor_szkola']) ||
                        !empty($item['czas_powstania']) ||
                        !empty($item['material_i_technika'])
                    ): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <?php if (!empty($item['autor_szkola'])): ?><th>Autor lub szkoła:</th><?php endif; ?>
                                        <?php if (!empty($item['czas_powstania'])): ?><th>Czas powstania:</th><?php endif; ?>
                                        <?php if (!empty($item['material_i_technika'])): ?><th>Material i technika:</th><?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php if (!empty($item['autor_szkola'])): ?><td><?= htmlspecialchars($item['autor_szkola']) ?>;</td><?php endif; ?>
                                        <?php if (!empty($item['czas_powstania'])): ?><td><?= htmlspecialchars($item['czas_powstania']) ?></td><?php endif; ?>
                                        <?php if (!empty($item['material_i_technika'])): ?><td><?= htmlspecialchars($item['material_i_technika']) ?></td><?php endif; ?>
                                    </tr>
                                </tbody>
                            </table>
                    <?php endif; ?>
                    <?php if (!empty($item['zdjecie_lokalne'])): ?>
                        <picture>
                            <img src="../images/muzealny/<?= $item['zdjecie_lokalne'] ?>">
                        </picture>
                    <?php endif; ?>
                    <?php if (!empty($item['opis'])): ?>
                        <p><strong>Opis: </strong>
                        <?= nl2br(htmlspecialchars($item['opis'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($item['zabiegi_konserwatorskie'])): ?>
                        <p><strong>Zabiegi konserwatorskie: </strong>
                        <?= nl2br(htmlspecialchars($item['zabiegi_konserwatorskie'])) ?></p>
                    <?php endif; ?> 
                    <?php if (!empty($item['stan_zachowania'])): ?>
                        <p><strong>Stan zachowania: </strong>
                        <?= nl2br(htmlspecialchars($item['stan_zachowania'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($item['kraj_miejscowosc_wytwornia'])): ?>
                        <p><strong>Kraj, miejscowość, wytwórnia: </strong>
                        <?= nl2br(htmlspecialchars($item['kraj_miejscowosc_wytwornia'])) ?></p>
                    <?php endif; ?>                    
                    <?php if (!empty($item['pochodzenie'])): ?>
                        <p><strong>Pochodzenie (historia przedmiotu), udzial w wystawach: </strong>
                        <?= nl2br(htmlspecialchars($item['pochodzenie'])) ?></p>
                    <?php endif; ?> 
                    <?php if (!empty($item['dane_dodatkowe'])): ?>
                        <p><strong>Dane dodatkowe: </strong>
                        <?= nl2br(htmlspecialchars($item['dane_dodatkowe'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($item['bibliografia'])): ?>
                        <p><strong>Bibliografia: </strong>
                        <?= nl2br(htmlspecialchars($item['bibliografia'])) ?></p>
                    <?php endif; ?>                     
                </div>
                <div class="card">
                    <div class="card-body">
                        <dl class="row">
                            <?php if (!empty($item['nr_ks_wplywu'])): ?>
                                <dt class="col-sm-4">Nr ks. wpływu:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($item['nr_ks_wplywu']) ?></dd>
                            <?php endif; ?>
                            <?php if (!empty($item['nr_inwent_negatywu'])): ?>
                                <dt class="col-sm-4">Nr inw. negatywu:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($item['nr_inwent_negatywu']) ?></dd>
                            <?php endif; ?>
                            <?php if (!empty($item['waga'])): ?>
                                <dt class="col-sm-4">Waga:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($item['waga']) ?></dd>
                            <?php endif; ?>

                            <?php
                            $wymiary = [];

                            if (!empty($item['wysokosc'])) {
                                $wymiary[] = htmlspecialchars($item['wysokosc']) . ' (wys)';
                            }
                            if (!empty($item['dlugosc'])) {
                                $wymiary[] = htmlspecialchars($item['dlugosc']) . ' (dł)';
                            }
                            if (!empty($item['szerokosc'])) {
                                $wymiary[] = htmlspecialchars($item['szerokosc']) . ' (szer)';
                            }
                            ?>
                            <?php if (!empty($wymiary)): ?>
                                <dt class="col-sm-4">Wymiary:</dt>
                                <dd class="col-sm-8">
                                    <?= implode(' x ', $wymiary) ?>
                                </dd>
                            <?php endif; ?>


                            <?php if (!empty($item['format'])): ?>
                                <dt class="col-sm-4">Format</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($item['format']) ?></dd>
                            <?php endif; ?> 
                            <?php if (!empty($item['data_i_sposob_nabycia'])): ?>    
                                <dt class="col-sm-4">Data i sposób nabycia:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($item['data_i_sposob_nabycia']) ?></dd>
                            <?php endif; ?>
                            <?php if (!empty($item['wartosc'])): ?> 
                                <dt class="col-sm-4">Wartość:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($item['wartosc']) ?></dd>  
                            <?php endif; ?>
                            <?php if (!empty($item['miejsce_przechowywania'])): ?> 
                                <dt class="col-sm-4">Miejsce przechowywania:</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($item['miejsce_przechowywania']) ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
            </article>
            <footer>
                <p>&copy; Copyright by <strong class="headerTXT">Muzeum Zamojskie w Zamościu</strong></p>
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
