<?php
session_start();
require 'db.php';

// Solo admins
if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$error = "";

// ========== AGREGAR PROVEEDOR ==========
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nuevo_proveedor'])) {
    $nombre = trim($_POST["nombre"]);

    if ($nombre === "") {
        $error = "Debes ingresar un nombre para el proveedor.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO proveedores (nombre) VALUES (:n)");
        if ($stmt->execute([":n" => $nombre])) {
            $mensaje = "Proveedor registrado correctamente.";
        } else {
            $error = "Error al registrar proveedor.";
        }
    }
}

// ========== ELIMINAR PROVEEDOR ==========
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];

    // Evitar borrar proveedores en uso
    $check = $pdo->prepare("SELECT COUNT(*) FROM medicamentos WHERE proveedor_id = :id");
    $check->execute([":id" => $delId]);
    $enUso = $check->fetchColumn();

    if ($enUso > 0) {
        $error = "No puedes eliminar este proveedor porque está asignado a medicamentos.";
    } else {
        $del = $pdo->prepare("DELETE FROM proveedores WHERE id = :id");
        if ($del->execute([":id" => $delId])) {
            $mensaje = "Proveedor eliminado correctamente.";
        } else {
            $error = "Error al eliminar proveedor.";
        }
    }
}

// Obtener proveedores
$proveedores = $pdo->query("SELECT * FROM proveedores ORDER BY nombre ASC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proveedores</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<div class="container">

    <h1>Gestión de Proveedores</h1>
    <a href="panel_admin.php" class="btn btn-secondary">← Volver al Panel</a>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <!-- ====================== FORMULARIO AGREGAR ====================== -->
    <h2>Agregar Proveedor</h2>

    <form method="POST">
        <input type="hidden" name="nuevo_proveedor" value="1">

        <label>Nombre del proveedor:</label>
        <input type="text" name="nombre" required>

        <button class="btn btn-primary" type="submit">Registrar Proveedor</button>
    </form>

    <hr style="margin: 35px 0;">

    <!-- ====================== LISTADO ====================== -->
    <h2>Lista de Proveedores</h2>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th style="width:140px;">Acciones</th>
            </tr>
        </thead>
        <tbody>

            <?php if (count($proveedores) === 0): ?>
                <tr><td colspan="2" class="empty">No hay proveedores registrados.</td></tr>
            <?php endif; ?>

            <?php foreach ($proveedores as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td>
                        <a href="proveedores.php?delete=<?= $p['id'] ?>" 
                           class="btn btn-sm btn-delete"
                           onclick="return confirm('¿Eliminar este proveedor?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

</div>
</div>

</body>
</html>