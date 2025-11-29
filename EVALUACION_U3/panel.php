<?php
session_start();

// Si no hay sesión → al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Redirección inteligente por rol
if ($_SESSION['rol'] === 'admin') {
    header("Location: panel_admin.php");
    exit;
} else {
    header("Location: panel_usuario.php");
    exit;
}
