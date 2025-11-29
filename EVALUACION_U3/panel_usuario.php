<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'usuario') {
    header("Location: login.php");
    exit;
}

$categoriaFiltro = $_GET['categoria'] ?? "";

$sql = "SELECT m.*, p.nombre AS prov_nombre
        FROM medicamentos m
        LEFT JOIN proveedores p ON m.proveedor_id = p.id";

$params = [];
if ($categoriaFiltro !== "") {
    $sql .= " WHERE m.categoria LIKE :cat";
    $params[':cat'] = '%' . $categoriaFiltro . '%';
}
$sql .= " ORDER BY m.nombre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$medicamentos = $stmt->fetchAll();

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$totalCarrito = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Panel Usuario - Medicamentos</title>
<link rel="stylesheet" href="css/style.css">

<style>
:root {
    --primary: #1E9A5D;
    --primary-dark: #157346;
    --btn-radius: 8px;
}

/* Header */
.header-right {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

/* Botones universales */
.btn {
    padding: 10px 18px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    border-radius: var(--btn-radius);
    font-size: 15px;
    font-weight: 600;
    text-decoration: none !important;
    cursor: pointer;
    border: none;
}

/* Botón primario */
.btn-primary {
    background: var(--primary);
    color: white !important;
}
.btn-primary:hover { background: var(--primary-dark); }

/* BOTÓN SECUNDARIO MODERNO */
.btn-secondary {
    display: inline-block;
    padding: 12px 25px;
    background: linear-gradient(145deg, #a9a9a9, #7d7d7d); 
    color: #ffffff !important;
    font-weight: bold;
    text-decoration: none !important;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: linear-gradient(145deg, #7d7d7d, #5a5a5a); 
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}

.btn-secondary:active {
    transform: translateY(1px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

/* Inputs y tabla */
.header input[type="text"] {
    height: 40px;
    padding: 0 14px;
    border-radius: var(--btn-radius);
    border: 1px solid #cbd5e1;
    font-size: 15px;
    width: 180px;
}

.btn-cart {
    padding: 8px 16px;
    border-radius: var(--btn-radius);
    background: var(--primary);
    color: white;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: .2s;
}
.btn-cart:hover { background: var(--primary-dark); }

.add-input {
    width: 60px;
    height: 38px;
    border-radius: var(--btn-radius);
    border: 1px solid #cbd5e1;
    text-align: center;
}
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="container">

        <div class="header" style="align-items:flex-start;">
            <div>
                <h1>Medicamentos Disponibles</h1>
                <div style="color:#666;font-size:14px;">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></div>
            </div>

            <div class="header-right">

                <form method="GET" style="display:flex; gap:12px; align-items:center;">
                    <input type="text" name="categoria" placeholder="Buscar categoría..." value="<?= htmlspecialchars($categoriaFiltro) ?>">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </form>

                <a href="cart.php" class="btn btn-primary">
                    Carrito
                    <?php if ($totalCarrito > 0): ?>
                        <span class="cart-badge"><?= $totalCarrito ?></span>
                    <?php endif; ?>
                </a>

                <a href="logout.php" class="btn-secondary">Cerrar Sesión</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Cantidad Disponible</th>
                    <th>Precio</th>
                    <th>Proveedor</th>
                    <th>Agregar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($medicamentos) === 0): ?>
                    <tr><td colspan="6" class="empty">No hay medicamentos.</td></tr>
                <?php endif; ?>

                <?php foreach ($medicamentos as $med): ?>
                <tr>
                    <td><?= htmlspecialchars($med['nombre']) ?></td>
                    <td><?= htmlspecialchars($med['categoria']) ?></td>
                    <td><?= (int)$med['cantidad'] ?></td>
                    <td>$<?= number_format($med['precio'], 2) ?></td>
                    <td><?= htmlspecialchars($med['prov_nombre'] ?? '-') ?></td>
                    <td>
                        <?php if ($med['cantidad'] > 0): ?>
                        <form action="add_to_cart.php" method="POST" style="display:flex; gap:12px; align-items:center;">
                            <input type="hidden" name="id" value="<?= $med['id'] ?>">
                            <input type="number" name="cantidad" min="1" max="<?= $med['cantidad'] ?>" value="1" class="add-input">
                            <button class="btn-cart" type="submit">Agregar</button>
                        </form>
                        <?php else: ?>
                            <span style="color:#d9534f;font-weight:bold;">Agotado</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
