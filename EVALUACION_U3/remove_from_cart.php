<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$index = isset($_POST['index']) ? (int) $_POST['index'] : -1;
if ($index >= 0 && isset($_SESSION['cart'][$index])) {
    array_splice($_SESSION['cart'], $index, 1);
}

header("Location: cart.php");
exit;
