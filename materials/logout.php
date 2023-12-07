<?php
if (isset($_COOKIE['login'])) {
    setcookie('login', '', time() - 3600, '/');
}
if (isset($_COOKIE['password'])) {
    setcookie('password', '', time() - 3600, '/');
}
header('Location: index.php');
?>