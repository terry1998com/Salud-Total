<?php
session_start();
require 'db.php';

$mensaje = "";
$error = "";

// Si el form fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $clave = $_POST['clave'];

    if (empty($nombre) || empty($email) || empty($clave)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Verificar si el correo ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $existe = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existe) {
            $error = "El correo ya existe.";
        } else {
            // Registrar nuevo usuario
            $claveHash = password_hash($clave, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nombre, email, clave, rol)
                VALUES (:nom, :email, :clave, 'usuario')
            ");

            $registrado = $stmt->execute([
                ':nom'   => $nombre,
                ':email' => $email,
                ':clave' => $claveHash
            ]);

            if ($registrado) {
                $mensaje = "Usuario registrado correctamente.";
            } else {
                $error = "Error al registrar. Intenta de nuevo.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar Usuario</title>
<link rel="stylesheet" href="css/style.css">

<style>
    .wrapper {
        width: 100%;
        max-width: 450px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>
    
<div class="content wrapper">

    <h1>Registrar Usuario</h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Correo:</label>
        <input type="email" name="email" required>

        <label>Contraseña:</label>
        <input type="password" name="clave" required>

        <button type="submit" class="btn-primary">Registrar</button>
    </form>

    <br>
    <a href="login.php" class="btn-secondary">Volver al Login</a>

</div>

</body>
</html>
