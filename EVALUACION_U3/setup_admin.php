<?php
require 'db.php';

$email = 'admin@saludtotal.com';
$password_plana = 'admin123'; // La contraseña que usarás para entrar

// Encriptar la contraseña (Nivel Avanzado)
$password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

// Insertar o Actualizar el usuario admin
$sql = "INSERT INTO usuarios (nombre, email, clave, rol) 
        VALUES ('Administrador', :email, :clave, 'admin')
        ON DUPLICATE KEY UPDATE clave = :clave";

$stmt = $pdo->prepare($sql);
if ($stmt->execute([':email' => $email, ':clave' => $password_hash])) {
    echo "Usuario Admin configurado con éxito.<br>";
    echo "Email: $email<br>";
    echo "Clave: $password_plana<br>";
    echo "Hash generado: $password_hash";
} else {
    echo "Error al crear usuario.";
}
?>