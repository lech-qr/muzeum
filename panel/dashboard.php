<?php

ini_set('session.save_path', __DIR__ . '/sessions');
ini_set('session.use_only_cookies', 1);
session_start();
require_once 'config.php';
require_once 'auth.php';
requireLogin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <title>Dashboard - System Muzealny</title>
  <?php require_once 'assets/php/head.php'; ?>

</head>
<body class="dashboard">
<header>
  <h1>🏛️ System Muzealny</h1>
  <div>
    <span class="user-info">Zalogowany: <strong><?= htmlspecialchars($user['imie'] ?: $user['login']) ?></strong></span>
    <a href="logout.php">Wyloguj</a>
  </div>
</header>

<div class="container">
  <h2>Wybierz zbiór do edycji</h2>

  <div class="cards">
    <?php foreach (TABLES as $table => $cfg): ?>
    <a class="card" href="table.php?t=<?= urlencode($table) ?>">
      <div class="icon"><?= explode(' ', $cfg['label'])[0] ?></div>
      <h3><?= htmlspecialchars(implode(' ', array_slice(explode(' ', $cfg['label']), 1))) ?></h3>
      <p>Zarządzaj rekordami</p>
    </a>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
