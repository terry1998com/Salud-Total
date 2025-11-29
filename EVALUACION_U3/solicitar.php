<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

$total = 0;
foreach ($cart as $it) $total += $it['precio'] * $it['qty'];

// Guardar en tabla solicitudes (datos en JSON)
$data = json_encode(['items' => $cart, 'total' => $total], JSON_UNESCAPED_UNICODE);

$stmt = $pdo->prepare("INSERT INTO solicitudes (usuario_id, datos) VALUES (:uid, :datos)");
$stmt->execute([
    ':uid' => $_SESSION['user_id'],
    ':datos' => $data
]);

// opcional: vaciar carrito
unset($_SESSION['cart']);

header("Location: panel.php?msg=solicitado");
exit;
