<?php

ini_set('session.save_path', __DIR__ . '/sessions');
ini_set('session.use_only_cookies', 1);
require_once 'config.php';
require_once 'auth.php';
requireLogin();

$user      = getCurrentUser();
$tableName = $_GET['t'] ?? '';

if (!array_key_exists($tableName, TABLES)) {
    header('Location: dashboard.php');
    exit;
}

$cfg    = TABLES[$tableName];
$pk     = $cfg['primary_key'];
$search = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 30;
$offset  = ($page - 1) * $perPage;

// sortowanie
$allowedCols = $cfg['display_columns'] ?? [];
$sortCol  = $_GET['sort'] ?? $pk;
$sortDir  = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
if (!in_array($sortCol, $allowedCols)) {
    $sortCol = $pk;
}
$nextDir = $sortDir === 'asc' ? 'desc' : 'asc';

try {
    $pdo = getDB();

    // Pobierz kolumny tabeli
    $stmt = $pdo->query("DESCRIBE `$tableName`");
    $columns = $stmt->fetchAll();

    // Buduj zapytanie z wyszukiwaniem
    $where  = '';
    $params = [];
    if ($search && !empty($cfg['search_columns'])) {
        $conditions = [];
        foreach ($cfg['search_columns'] as $col) {
            $conditions[] = "`$col` LIKE ?";
            $params[] = "%$search%";
        }
        $where = 'WHERE ' . implode(' OR ', $conditions);
    }

    // Liczba wszystkich rekordów
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `$tableName` $where");
    $countStmt->execute($params);
    $total      = (int)$countStmt->fetchColumn();
    $totalPages = max(1, (int)ceil($total / $perPage));

    // Rekordy
    $stmt = $pdo->prepare("SELECT * FROM `$tableName` $where ORDER BY `$sortCol` $sortDir LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    // Dla muzealny - pobierz działy
    $dzialy = [];
    if ($tableName === 'muzealny') {
        $dStmt = $pdo->query("SELECT id, nazwa FROM dzialy ORDER BY nazwa");
        foreach ($dStmt->fetchAll() as $d) {
            $dzialy[$d['id']] = $d['nazwa'];
        }
    }

} catch (Exception $e) {
    die('Błąd: ' . htmlspecialchars($e->getMessage()));
}


// Kolumny do wyświetlenia w tabeli (skrócona lista)
$displayCols = $cfg['display_columns'] ?? array_slice(array_column($columns, 'Field'), 0, 6);

?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <title><?= htmlspecialchars($cfg['label']) ?> - System Muzealny</title>
  <?php require_once 'assets/php/head.php'; ?>
</head>
<body class="table">
<header>
  <h1>🏛️ <?= htmlspecialchars($cfg['label']) ?></h1>
  <nav>
    <a href="dashboard.php">← Dashboard</a>
    <a href="logout.php" class="danger">Wyloguj</a>
  </nav>
</header>

<div class="container">
  <div class="toolbar">
    <h2>
      Lista rekordów
      <span class="badge"><?= $total ?></span>
    </h2>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <form class="search-form" method="GET">
        <input type="hidden" name="t" value="<?= htmlspecialchars($tableName) ?>">
        <input type="text" name="q" placeholder="Szukaj..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Szukaj</button>
        <?php if ($search): ?>
          <a href="table.php?t=<?= urlencode($tableName) ?>" class="btn btn-secondary">✕ Wyczyść</a>
        <?php endif; ?>
      </form>
      <a href="edit.php?t=<?= urlencode($tableName) ?>&action=add" class="btn btn-success">+ Nowy rekord</a>
    </div>
  </div>

  <div class="card">
    <?php if ($search): ?>
    <div class="info-bar">
      <span>Wyniki dla: <strong><?= htmlspecialchars($search) ?></strong></span>
      <span>Znaleziono: <?= $total ?></span>
    </div>
    <?php endif; ?>

    <div class="table-wrap">
      <?php if (empty($rows)): ?>
        <div class="empty">
          <p style="font-size:3rem;">📭</p>
          <p>Brak rekordów<?= $search ? ' dla podanego wyszukiwania' : '' ?>.</p>
        </div>
      <?php else: ?>
      <table>
        <?php
        // helper do budowania URL z zachowaniem parametrów
        function tableUrl(array $override = []): string {
            $params = array_merge($_GET, $override);
            return 'table.php?' . http_build_query($params);
        }
        ?>
        <thead>
          <tr>
            <?php foreach ($displayCols as $col):
              $isActive = ($col === $sortCol);
              $dir      = $isActive ? $nextDir : 'asc';
              $arrow    = $isActive ? ($sortDir === 'asc' ? ' ▲' : ' ▼') : '';
            ?>
              <th>
                <a href="<?= tableUrl(['sort' => $col, 'dir' => $dir, 'page' => 1]) ?>"
                  class="sort-link<?= $isActive ? ' sort-active' : '' ?>">
                  <?= htmlspecialchars($col) ?><?= $arrow ?>
                </a>
              </th>
            <?php endforeach; ?>
            <th class="akcje">Akcje</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
          <tr>
            <?php foreach ($displayCols as $col): ?>
              <td title="<?= htmlspecialchars((string)($row[$col] ?? '')) ?>">
                <?php
                  $val = $row[$col] ?? '';
                  if ($tableName === 'muzealny' && $col === 'dzialy_id') {
                      echo htmlspecialchars($dzialy[$val] ?? "Dział #$val");
                  } else {
                      echo htmlspecialchars(mb_strimwidth((string)$val, 0, 40, '…'));
                  }
                ?>
              </td>
            <?php endforeach; ?>
            <td class="actions">
              <a href="edit.php?t=<?= urlencode($tableName) ?>&id=<?= urlencode($row[$pk]) ?>&action=edit"
                 class="btn btn-warning btn-sm">✏️ Edytuj</a>
              <a href="delete.php?t=<?= urlencode($tableName) ?>&id=<?= urlencode($row[$pk]) ?>"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Czy na pewno usunąć rekord #<?= $row[$pk] ?>?')">🗑️ Usuń</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

<?php if ($totalPages > 1): ?>
<div class="pagination">

  <?php if ($page > 1): ?>
    <a href="<?= tableUrl(['page' => $page - 1]) ?>">← Poprzednia</a>
  <?php else: ?>
    <span class="disabled">← Poprzednia</span>
  <?php endif; ?>

  <?php
  // Zawsze pokazuj stronę 1
  if ($page > 3): ?>
    <a href="<?= tableUrl(['page' => 1]) ?>">1</a>
    <?php if ($page > 4): ?>
      <span>…</span>
    <?php endif; ?>
  <?php endif; ?>

  <?php
  $start = max(1, $page - 2);
  $end   = min($totalPages, $page + 2);
  for ($i = $start; $i <= $end; $i++): ?>
    <?php if ($i === $page): ?>
      <span class="active"><?= $i ?></span>
    <?php else: ?>
      <a href="<?= tableUrl(['page' => $i]) ?>"><?= $i ?></a>
    <?php endif; ?>
  <?php endfor; ?>

  <?php
  // Zawsze pokazuj ostatnią stronę
  if ($page < $totalPages - 2): ?>
    <?php if ($page < $totalPages - 3): ?>
      <span>…</span>
    <?php endif; ?>
    <a href="<?= tableUrl(['page' => $totalPages]) ?>"><?= $totalPages ?></a>
  <?php endif; ?>

  <?php if ($page < $totalPages): ?>
    <a href="<?= tableUrl(['page' => $page + 1]) ?>">Następna →</a>
  <?php else: ?>
    <span class="disabled">Następna →</span>
  <?php endif; ?>

</div>
<?php endif; ?>

  </div>
</div>
</body>
</html>
