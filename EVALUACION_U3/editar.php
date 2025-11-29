<?php
session_start();
require 'db.php';

// Validar rol admin
if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

// Validar ID recibido
if (!isset($_GET['id'])) {
    header("Location: panel_admin.php");
    exit;
}

$id = (int) $_GET['id'];
$mensaje = "";
$error = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $cantidad = (int)$_POST['cantidad'];
    $precio = (float)$_POST['precio'];
    $proveedor_id = (int)$_POST['proveedor_id'];

    if ($nombre === "" || $categoria === "" || $cantidad < 0 || $precio < 0) {
        $error = "Completa todos los campos correctamente.";
    } else {
        try {
            $sql = "UPDATE medicamentos 
                    SET nombre = :n, categoria = :c, cantidad = :cant, precio = :p, proveedor_id = :prov
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $ok = $stmt->execute([
                ":n" => $nombre,
                ":c" => $categoria,
                ":cant" => $cantidad,
                ":p" => $precio,
                ":prov" => $proveedor_id,
                ":id" => $id
            ]);

            if ($ok) {
                $mensaje = "Medicamento actualizado correctamente.";
            }
        } catch (PDOException $e) {
            $error = "Error al actualizar: " . $e->getMessage();
        }
    }
}

// Obtener datos actuales
$stmt = $pdo->prepare("SELECT * FROM medicamentos WHERE id = :id");
$stmt->execute([":id" => $id]);
$medicamento = $stmt->fetch();

if (!$medicamento) {
    die("El medicamento no existe.");
}

// Obtener proveedores
$proveedores = $pdo->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Medicamento</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<div class="container">

    <h1>Editar Medicamento</h1>
    <a href="panel_admin.php" class="btn btn-secondary">← Volver al Panel</a>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($medicamento['nombre']) ?>" required>

        <label>Categoría:</label>
        <input type="text" name="categoria" value="<?= htmlspecialchars($medicamento['categoria']) ?>" required>

        <label>Cantidad:</label>
        <input type="number" name="cantidad" value="<?= htmlspecialchars($medicamento['cantidad']) ?>" min="0" required>

        <label>Precio Unitario:</label>
        <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($medicamento['precio']) ?>" min="0" required>

        <label>Proveedor:</label>
        <select name="proveedor_id" required>
            <?php foreach ($proveedores as $p): ?>
                <option value="<?= $p['id'] ?>"
                    <?= ($p['id'] == $medicamento['proveedor_id']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($p['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>

</div>
</div>

</body>
</html>
