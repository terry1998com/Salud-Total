<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['user_id'];

$sql = "SELECT 
            s.id,
            s.fecha,
            s.estado,
            COALESCE(SUM(si.cantidad * si.precio), 0) AS total
        FROM solicitudes s
        LEFT JOIN solicitud_items si ON si.solicitud_id = s.id
        WHERE s.usuario_id = :u
        GROUP BY s.id
        ORDER BY s.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':u' => $idUsuario]);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Solicitudes</title>
<link rel="stylesheet" href="css/style.css">

<style>
:root {
    --primary: #1E9A5D;
    --primary-dark: #157346;
}

/* TARJETAS */
.solicitud-card {
    background: white;
    padding: 20px;
    margin-bottom: 22px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.solicitud-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    font-size: 17px;
}

.estado {
    padding: 6px 14px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
}

.estado-pendiente { background: #fff3cd; color: #8a6d3b; }
.estado-aprobado { background: #d4edda; color: #155724; }
.estado-entregado { background: #cce5ff; color: #004085; }

.item { padding: 12px 0; border-bottom: 1px solid #eee; font-size: 15px; }
.item:last-child { border-bottom: none; }

.total { margin-top: 14px; font-size: 18px; font-weight: bold; color: var(--primary-dark); text-align: right; }

.empty { padding: 20px; text-align: center; color:#777; }

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

/* TITULO */
.header h1 {
    font-size: 26px;
    margin-bottom: 5px;
}
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<div class="container">

    <div class="header" style="display:flex; justify-content:space-between; align-items:center;">
        <h1>Mis Solicitudes</h1>
        <a href="panel_usuario.php" class="btn-secondary">Volver</a>
    </div>

    <?php if (empty($solicitudes)): ?>
        <div class="empty">Aún no has solicitado medicamentos.</div>
    <?php else: ?>

        <?php foreach ($solicitudes as $sol): ?>

        <?php
        $stmt2 = $pdo->prepare("
            SELECT si.*, m.nombre 
            FROM solicitud_items si
            LEFT JOIN medicamentos m ON m.id = si.medicamento_id
            WHERE si.solicitud_id = :id
        ");
        $stmt2->execute([':id' => $sol['id']]);
        $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="solicitud-card">

            <div class="solicitud-header">
                <strong>Solicitud #<?= $sol['id'] ?></strong>
                <span class="estado estado-<?= strtolower($sol['estado']) ?>">
                    <?= ucfirst($sol['estado']) ?>
                </span>
            </div>

            <div style="color:#666; margin-bottom:10px;">
                Fecha: <?= $sol['fecha'] ?>
            </div>

            <?php foreach ($items as $it): ?>
                <div class="item">
                    <strong><?= htmlspecialchars($it['nombre']) ?></strong><br>
                    Cantidad: <?= $it['cantidad'] ?> — 
                    $<?= number_format($it['precio'], 2) ?>
                </div>
            <?php endforeach; ?>

            <div class="total">
                Total: $<?= number_format($sol['total'], 2) ?>
            </div>

        </div>

        <?php endforeach; ?>
    <?php endif; ?>

</div>
</div>

</body>
</html>
