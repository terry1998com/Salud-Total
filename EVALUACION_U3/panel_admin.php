<?php
session_start();
require 'db.php';

// Solo admin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
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

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$medicamentos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Ajustes específicos */
        .table-actions {
            display: flex;
            gap: 10px;
        }

        .header h1 {
            font-size: 24px;
        }

        /* Botón Buscar y Crear */
        .btn-small-primary {
            padding: 10px 18px;
            background: #007bff;
            color: white;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }

        .btn-small-primary:hover {
            background: #0056b3;
        }

        /* Botón cerrar sesión */
        .btn-logout {
            background: #d9534f;
            color: white !important;
            padding: 10px 18px;
            border-radius: 6px;
        }

        .btn-logout:hover {
            background: #c9302c;
        }
    </style>

</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">

        <div class="container">

            <div class="header">
                <h1>Panel Administrador — Bienvenido <?= htmlspecialchars($_SESSION['nombre']) ?></h1>

                <!-- Botón cerrar sesión corregido -->
                <a href="logout.php" class="btn btn-logout">Cerrar Sesión</a>
            </div>

            <div class="search-bar">
                <form method="GET" style="display:flex; gap:10px;">
                    <input type="text"
                        name="categoria"
                        placeholder="Buscar por categoría..."
                        value="<?= htmlspecialchars($categoriaFiltro) ?>">

                    <button class="btn-small-primary">Buscar</button>
                </form>

                <a href="registro.php" class="btn-small-primary" style="text-decoration:none;">
                    + Nuevo Medicamento
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($medicamentos as $med): ?>
                        <tr>
                            <td><?= htmlspecialchars($med['nombre']) ?></td>
                            <td><?= htmlspecialchars($med['categoria']) ?></td>
                            <td><?= $med['cantidad'] ?></td>
                            <td>$<?= number_format($med['precio'], 2) ?></td>
                            <td><?= htmlspecialchars($med['prov_nombre']) ?></td>

                            <td class="table-actions">
                                <a class="btn btn-sm btn-edit" href="editar.php?id=<?= $med['id'] ?>">
                                    Editar
                                </a>
                                <a class="btn btn-sm btn-delete"
                                    href="eliminar.php?id=<?= $med['id'] ?>"
                                    onclick="return confirm('¿Eliminar medicamento?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        </div>

    </div>

</body>

</html>