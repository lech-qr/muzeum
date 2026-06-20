<?php

ini_set('session.save_path', __DIR__ . '/sessions');
ini_set('session.use_only_cookies', 1);
require_once 'config.php';
require_once 'auth.php';
requireLogin();

$user      = getCurrentUser();
$tableName = $_GET['t'] ?? '';
$action    = $_GET['action'] ?? 'edit'; // 'edit' lub 'add'
$id        = $_GET['id'] ?? null;

if (!array_key_exists($tableName, TABLES)) {
    header('Location: dashboard.php');
    exit;
}

$cfg = TABLES[$tableName];
$pk  = $cfg['primary_key'];

// Pola tylko do odczytu (nie edytujemy klucza głównego)
$readonlyFields = [$pk];

$pdo     = getDB();
$columns = [];
$record  = [];
$dzialy  = [];
$message = '';
$msgType = '';

try {
    // Pobierz strukturę tabeli
    $stmt    = $pdo->query("DESCRIBE `$tableName`");
    $columns = $stmt->fetchAll();

    // Dla muzealny pobierz działy
    if ($tableName === 'muzealny') {
        $dStmt = $pdo->query("SELECT id, nazwa FROM dzialy ORDER BY nazwa");
        foreach ($dStmt->fetchAll() as $d) {
            $dzialy[$d['id']] = $d['nazwa'];
        }
    }

    // Edycja → pobierz rekord
    if ($action === 'edit' && $id !== null) {
        $stmt   = $pdo->prepare("SELECT * FROM `$tableName` WHERE `$pk` = ? LIMIT 1");
        $stmt->execute([$id]);
        $record = $stmt->fetch();
        if (!$record) {
            header("Location: table.php?t=" . urlencode($tableName) . "&err=notfound");
            exit;
        }
    }

    // Obsługa formularza
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data   = [];
        $setClauses = [];
        $values = [];

        foreach ($columns as $col) {
            $field = $col['Field'];
            if (in_array($field, $readonlyFields)) continue;
            $val = $_POST[$field] ?? null;
            // Zamień puste stringi na NULL
            $data[$field] = ($val === '') ? null : $val;
        }

        if ($action === 'add') {
            // Ustaw ID = MAX + 1
            $maxStmt = $pdo->query("SELECT MAX(`$pk`) FROM `$tableName`");
            $maxId   = (int)$maxStmt->fetchColumn();
            $data[$pk] = $maxId + 1;
            // INSERT
            $fields       = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            $sql = "INSERT INTO `$tableName` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $newId   = $pdo->lastInsertId();
            $message = "Rekord został dodany (ID: $newId).";
            $msgType = 'success';
            header("Location: edit.php?t=" . urlencode($tableName) . "&id=$newId&action=edit&msg=added");
            exit;
        } else {
            // UPDATE
            foreach ($data as $field => $val) {
                $setClauses[] = "`$field` = ?";
                $values[]     = $val;
            }
            $values[] = $id;
            $sql = "UPDATE `$tableName` SET " . implode(', ', $setClauses) . " WHERE `$pk` = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $message = 'Rekord został zaktualizowany.';
            $msgType = 'success';
            // Odśwież dane
            $stmt   = $pdo->prepare("SELECT * FROM `$tableName` WHERE `$pk` = ? LIMIT 1");
            $stmt->execute([$id]);
            $record = $stmt->fetch();
        }
    }

    // Komunikat z przekierowania
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] === 'added') { $message = 'Rekord został pomyślnie dodany.'; $msgType = 'success'; }
    }

} catch (Exception $e) {
    $message = 'Błąd: ' . $e->getMessage();
    $msgType = 'error';
}

$pageTitle = ($action === 'add') ? 'Nowy rekord' : 'Edycja rekordu #' . htmlspecialchars($id ?? '');

// Duże pola tekstowe
$textareaFields = ['szczegolowa_charakterystyka', 'dane_dodatkowe', 'blizsze_dane',
                   'zabiegi_konserwatorskie', 'publikowano', 'opis', 'stan_zachowania',
                   'fotografie', 'bibliografia', 'pochodzenie'];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - System Muzealny</title>
    <?php require_once 'assets/php/head.php'; ?>
</head>
<body class="edit">
<header>
  <h1>🏛️ <?= htmlspecialchars($cfg['label']) ?> – <?= $pageTitle ?></h1>
  <nav>
    <a href="table.php?t=<?= urlencode($tableName) ?>">← Lista rekordów</a>
    <a href="dashboard.php">Dashboard</a>
  </nav>
</header>

<div class="container">
  <div class="page-title"><?= $pageTitle ?></div>

  <?php if ($message): ?>
    <div class="message <?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="POST" class="form-card">

    <?php
    // Grupuj pola w sekcje
    $sections = getSections($tableName, $columns);
    foreach ($sections as $sectionName => $sectionFields):
    ?>
    <div class="form-section">
      <div class="section-title"><?= htmlspecialchars($sectionName) ?></div>
      <div class="form-grid">
        <?php foreach ($sectionFields as $col):
          $field     = $col['Field'];
          $isReadonly = in_array($field, $readonlyFields);
          $isTextarea = in_array($field, $textareaFields) || $col['Type'] === 'longtext';
          $value     = $record[$field] ?? ($_POST[$field] ?? '');
          $isFullWidth = $isTextarea || $field === 'dzialy_id';
        ?>
        <div class="form-group <?= $isFullWidth ? 'full-width' : '' ?>">
          <label><span class="field-name"><?= htmlspecialchars($field) ?></span></label>

          <?php if ($isReadonly): ?>
            <input type="text" class="readonly-field"
                   value="<?= htmlspecialchars((string)($value ?? ($action === 'add' ? 'auto' : ''))) ?>" readonly>

          <?php elseif ($tableName === 'muzealny' && $field === 'dzialy_id'): ?>
            <select name="dzialy_id">
              <option value="">-- wybierz dział --</option>
              <?php foreach ($dzialy as $dId => $dNazwa): ?>
                <option value="<?= $dId ?>" <?= ($value == $dId) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($dNazwa) ?>
                </option>
              <?php endforeach; ?>
            </select>

          <?php elseif ($isTextarea): ?>
            <textarea name="<?= htmlspecialchars($field) ?>"><?= htmlspecialchars((string)($value ?? '')) ?></textarea>

          <?php else: ?>
            <input type="text" name="<?= htmlspecialchars($field) ?>"
                   value="<?= htmlspecialchars((string)($value ?? '')) ?>">
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">
        <?= ($action === 'add') ? '+ Dodaj rekord' : '💾 Zapisz zmiany' ?>
      </button>
      <a href="table.php?t=<?= urlencode($tableName) ?>" class="btn btn-secondary">Anuluj</a>
    </div>
  </form>
</div>

<script>
// Auto-resize textarea
document.querySelectorAll('textarea').forEach(function(el) {
    el.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
</script>
</body>
</html>
<?php

// Funkcja grupowania pól w sekcje
function getSections(string $table, array $columns): array {
    $sections = ['Wszystkie pola' => $columns];

    if ($table === 'archeologiczny') {
        $groups = [
            'Identyfikacja'   => ['id','nr_inwentarzowy','nr_katalogowy','nr_inw_fot','nr_inw_rys'],
            'Opis obiektu'    => ['nazwa','przedmiot','szczegolowa_charakterystyka','dane_dodatkowe','blizsze_dane'],
            'Stan i opieka'   => ['stan_zachowania','zabiegi_konserwatorskie','publikowano'],
            'Materiał'        => ['surowiec','barwa','sposob_wykonania'],
            'Nabycie'         => ['rok_nabycia','sposob_nabycia','nr_ks_wplywu'],
            'Wymiary'         => ['wysokosc','dlugosc','szerokosc','srednica','obwod','grubosc','dno'],
            'Lokalizacja'     => ['wojewodztwo','miejscowosc','gmina','stanowisko','chronologia','kultura'],
            'Media'           => ['zdjecie_lokalne'],
            'Dokumentacja'    => ['sporzadzil','rysowal','fotografowal','data'],
        ];
        return buildSections($columns, $groups);
    }

    if ($table === 'muzealny') {
        $groups = [
            'Identyfikacja'   => ['id','dzialy_id','nr_inwentarzowy','nr_ks_wplywu','nr_inwent_negatywu'],
            'Opis'            => ['przedmiot','autor_szkola','opis','material_i_technika','czas_powstania'],
            'Wymiary i waga'  => ['waga','szerokosc','wysokosc','dlugosc','format'],
            'Nabycie'         => ['data_i_sposob_nabycia','wartosc','miejsce_przechowywania','kraj_miejscowosc_wytwornia','pochodzenie'],
            'Stan i opieka'   => ['stan_zachowania','zabiegi_konserwatorskie'],
            'Dokumentacja'    => ['fotografie','bibliografia','dane_dodatkowe','zdjecie_lokalne','data_i_wypelniajacy'],
        ];
        return buildSections($columns, $groups);
    }

    return $sections;
}

function buildSections(array $columns, array $groups): array {
    $colByField = [];
    foreach ($columns as $col) {
        $colByField[$col['Field']] = $col;
    }
    $result = [];
    $usedFields = [];
    foreach ($groups as $groupName => $fields) {
        $result[$groupName] = [];
        foreach ($fields as $f) {
            if (isset($colByField[$f])) {
                $result[$groupName][] = $colByField[$f];
                $usedFields[]         = $f;
            }
        }
    }
    // Pozostałe pola
    // $remaining = array_filter($columns, fn($c) => !in_array($c['Field'], $usedFields));
    // if ($remaining) {
    //     $result['Pozostałe'] = array_values($remaining);
    // }
    return $result;
}
