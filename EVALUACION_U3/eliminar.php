<?php
session_start();
require 'db.php';

// Solo admins pueden eliminar
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: panel.php");
    exit;
}

// Validar ID recibido
if (!isset($_GET['id'])) {
    header("Location: panel.php");
    exit;
}

$id = (int) $_GET['id'];

// Obtener datos del medicamento para mostrar nombre
$stmt = $pdo->prepare("SELECT nombre FROM medicamentos WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicamento) {
    die("El medicamento no existe.");
}

// Si el usuario confirma eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['confirmar'])) {

        $stmt = $pdo->prepare("DELETE FROM medicamentos WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: panel.php?msg=deleted");
        exit;
    } else {
        header("Location: panel.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Eliminar Medicamento</title>
<link rel="stylesheet" href="css/style.css">

<style>
    body {
        background: #f2f6fb;
        font-family: "Segoe UI", sans-serif;
    }

    .container {
        max-width: 600px;
        margin: 80px auto;
        background: white;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.12);
        text-align: center;
    }

    h1 {
        font-size: 28px;
        color: #d32f2f;
        margin-bottom: 15px;
    }

    p {
        font-size: 17px;
        color: #333;
    }

    .warning-box {
        background: #fff5f5;
        border-left: 5px solid #d32f2f;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 16px;
        color: #b71c1c;
    }

    .btn-danger {
        background: #d32f2f;
        color: white;
        padding: 12px 18px;
        border-radius: 8px;
        border: none;
        font-size: 16px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
        width: 48%;
    }

    .btn-danger:hover {
        background: #b71c1c;
    }

    .btn-secondary {
        background: #e5eaf1;
        padding: 12px 18px;
        border-radius: 8px;
        color: #1f3b73;
        font-weight: bold;
        text-decoration: none;
        border: none;
        cursor: pointer;
        width: 48%;
        transition: 0.3s;
    }

    .btn-secondary:hover {
        background: #d6dce6;
    }

    .btns {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
    }
</style>

</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content container">

    <h1>Eliminar Medicamento</h1>

    <div class="warning-box">
        Estás a punto de eliminar el medicamento:
        <br><b><?= htmlspecialchars($medicamento['nombre']) ?></b>
        <br><br>Esta acción no se puede deshacer.
    </div>

    <form method="POST">
        <div class="btns">
            <button type="submit" name="confirmar" class="btn-danger">Eliminar</button>
            <a href="panel.php" class="btn-secondary">Cancelar</a>
        </div>
    </form>

</div>

</body>
</html>
