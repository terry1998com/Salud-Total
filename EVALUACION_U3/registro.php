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

// Obtener proveedores
$proveedores = $pdo->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $cantidad = (int)$_POST['cantidad'];
    $precio = (float)$_POST['precio'];
    $proveedor_id = (int)$_POST['proveedor_id'];

    if ($nombre === "" || $categoria === "" || $cantidad < 0 || $precio < 0) {
        $error = "Completa todos los campos correctamente.";
    } else {
        $sql = "INSERT INTO medicamentos (nombre, categoria, cantidad, precio, proveedor_id)
                VALUES (:n, :c, :cant, :p, :prov)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([
            ":n" => $nombre,
            ":c" => $categoria,
            ":cant" => $cantidad,
            ":p" => $precio,
            ":prov" => $proveedor_id
        ])) {
            $mensaje = "Medicamento registrado correctamente.";
        } else {
            $error = "Error al registrar medicamento.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar Medicamento</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
<div class="container">

    <h1>Registrar Medicamento</h1>
    <a href="panel_admin.php" class="btn btn-secondary">← Volver al Panel</a>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Nombre del Medicamento:</label>
        <input type="text" name="nombre" required>

        <label>Categoría:</label>
        <input type="text" name="categoria" required>

        <label>Cantidad Disponible:</label>
        <input type="number" name="cantidad" min="0" required>

        <label>Precio Unitario:</label>
        <input type="number" step="0.01" min="0" name="precio" required>

        <label>Proveedor:</label>
        <select name="proveedor_id" required>
            <option value="">Selecciona un proveedor</option>
            <?php foreach ($proveedores as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Registrar Medicamento</button>
    </form>

</div>
</div>

</body>
</html>
