<?php
session_start();
session_destroy();

if (isset($_COOKIE['rememberme'])) {
    unset($_COOKIE['rememberme']);
    setcookie('remember', '', time() - 3600);
}

header('Location: index.php');
?>