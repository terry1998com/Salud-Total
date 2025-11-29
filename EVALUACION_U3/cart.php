<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;

foreach ($cart as $it) {
    $total += $it['precio'] * $it['qty'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Carrito</title>
<link rel="stylesheet" href="css/style.css">

<style>
/* Mejoras visuales específicas para carrito */
.cart-container {
    max-width: 900px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.cart-item {
    display: flex;
    justify-content: space-between;
    padding: 15px 10px;
    border-bottom: 1px solid #eee;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item strong {
    font-size: 18px;
    color: #1f3b73;
}

.cart-actions button {
    margin-left: 10px;
}

/* Total */
.total-box {
    text-align: right;
    margin-top: 20px;
    font-size: 20px;
    font-weight: bold;
}

/* Carrito vacío */
.empty-cart {
    padding: 25px;
    text-align: center;
    background: #fafafa;
    border-radius: 10px;
    color: #777;
}

/* Botones */
.btn-secondary {
    display: inline-block;
    padding: 12px 25px;
    background: linear-gradient(145deg, #a9a9a9, #7d7d7d); /* degradado gris */
    color: #ffffff;
    font-weight: bold;
    text-decoration: none;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: linear-gradient(145deg, #7d7d7d, #5a5a5a); /* degradado más oscuro al hover */
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}

.btn-secondary:active {
    transform: translateY(1px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

/* Botón principal (Solicitar Medicamentos) */
.btn-primary {
    display: inline-block;
    padding: 12px 25px;
    background-color: #1f3b73;
    color: #fff;
    font-weight: bold;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #152a56;
    transform: translateY(-2px);
}
</style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<div class="cart-container">

    <div class="header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1>Mi Carrito</h1>
        <a href="panel_usuario.php" class="btn-secondary">Seguir Comprando</a>
    </div>

    <?php if (empty($cart)): ?>
        <div class="empty-cart">Tu carrito está vacío.</div>

    <?php else: ?>

        <ul class="cart-list">
            <?php foreach ($cart as $idx => $it): ?>
            <li class="cart-item">
                <div>
                    <strong><?= htmlspecialchars($it['nombre']) ?></strong>
                    <div style="font-size:14px;color:#555;">
                        Cantidad: <?= (int)$it['qty'] ?> × $<?= number_format($it['precio'], 2) ?>
                    </div>
                </div>

                <div class="cart-actions">
                    <form method="POST" action="remove_from_cart.php" style="display:inline;">
                        <input type="hidden" name="index" value="<?= $idx ?>">
                        <button class="btn-outline">Quitar</button>
                    </form>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

        <div class="total-box">
            Total: $<?= number_format($total, 2) ?>
        </div>

        <form method="POST" action="solicitar.php" style="margin-top:20px;">
            <button class="btn-primary" type="submit">Solicitar Medicamentos</button>
        </form>

    <?php endif; ?>

</div>
</div>

</body>
</html>
