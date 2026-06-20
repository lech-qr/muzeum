<?php

ini_set('session.save_path', __DIR__ . '/sessions');
ini_set('session.use_only_cookies', 1);
session_start();
session_destroy();
header('Location: index.php');
exit;
