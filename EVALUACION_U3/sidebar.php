<!-- sidebar.php -->
<link rel="stylesheet" href="css/style.css">

<script>
function toggleSidebar() {
    document.querySelector(".sidebar").classList.toggle("open");
}
</script>

<!-- Botón (solo móvil) -->
<div class="mobile-menu-btn" onclick="toggleSidebar()">☰</div>

<div class="sidebar">
    <div class="sidebar-title">Salud Total</div>

    <ul class="sidebar-menu">
        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <li><a href="panel_admin.php">Panel Admin</a></li>
            <li><a href="registro.php">Registrar Medicamento</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li><a href="panel_solicitudes_admin.php">Solicitudes</a></li>
        <?php else: ?>
            <li><a href="panel_usuario.php">Medicamentos</a></li>
            <li><a href="cart.php">Mi Carrito</a></li>
            <li><a href="mis_solicitudes.php">Mis Solicitudes</a></li>
        <?php endif; ?>

        <li><a class="logout" href="logout.php">Cerrar Sesión</a></li>
    </ul>
</div>
