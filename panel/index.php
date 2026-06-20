<?php

ob_start(); // buforowanie wyjścia - ignoruje BOM

ini_set('session.save_path', __DIR__ . '/sessions');
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $haslo = $_POST['haslo'] ?? '';

    if ($login === '' || $haslo === '') {
        $error = 'Wypełnij wszystkie pola.';
    } else {
        try {
            $pdo  = getDB();
            $stmt = $pdo->prepare("SELECT * FROM uzytkownicy WHERE login = ? LIMIT 1");
            $stmt->execute([$login]);
            $user = $stmt->fetch();

if ($user && password_verify($haslo, $user['haslo'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['login']   = $user['login'];
    $_SESSION['imie']    = $user['imie'];
    
    // DEBUG TYMCZASOWY
    // echo "Sesja ustawiona. user_id: " . $_SESSION['user_id'] . "<br>";
    // echo "Session ID: " . session_id() . "<br>";
    // echo "<a href='dashboard.php'>Przejdź do dashboard</a><br>";
    // echo "<br>Zawartość sesji: <pre>" . print_r($_SESSION, true) . "</pre>";
    // die();
    
    header('Location: dashboard.php');
    exit;
}
 else {
                $error = 'Nieprawidłowy login lub hasło.';
            }
        } catch (Exception $e) {
            $error = 'Błąd bazy danych: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Logowanie - System Muzealny</title>
<?php require_once 'assets/php/head.php'; ?>

</head>
<body class="index">
<div class="login-box">
    <h1>🏛️ System Muzealny</h1>
    <p class="subtitle">Zaloguj się, aby kontynuować</p>

    <?php if ($error): ?>
        <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="login">Login</label>
        <input type="text" id="login" name="login"
               value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
               autocomplete="username" autofocus>

        <label for="haslo">Hasło</label>
        <input type="password" id="haslo" name="haslo" autocomplete="current-password">

        <button type="submit">Zaloguj się</button>
    </form>
</div>
</body>
</html>
