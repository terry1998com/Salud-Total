<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$qty = isset($_POST['qty']) ? max(1, (int) $_POST['qty']) : 1;

if ($id <= 0) {
    header("Location: panel.php");
    exit;
}

// Obtener info del medicamento
$stmt = $pdo->prepare("SELECT id, nombre, precio, cantidad FROM medicamentos WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$med = $stmt->fetch();

if (!$med) {
    header("Location: panel.php");
    exit;
}

// inicializar carrito
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// si ya existe, sumar cantidades (sin superar stock)
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $med['id']) {
        $item['qty'] = min($med['cantidad'], $item['qty'] + $qty);
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    $_SESSION['cart'][] = [
        'id' => $med['id'],
        'nombre' => $med['nombre'],
        'precio' => $med['precio'],
        'qty' => min($qty, $med['cantidad'])
    ];
}

header("Location: panel.php");
exit;
