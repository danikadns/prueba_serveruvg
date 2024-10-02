<?php
require 'session_handler.php';
session_start();

include 'db.php';

// Manejar la eliminación de notificaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_notificacion'])) {
    $notificacion_id = $_POST['notificacion_id'];
    $sql_eliminar = "DELETE FROM notificaciones WHERE id = ?";
    $stmt_eliminar = $conn->prepare($sql_eliminar);
    $stmt_eliminar->bind_param('i', $notificacion_id);
    $stmt_eliminar->execute();
    $stmt_eliminar->close();

    // Redirigir para evitar el reenvío del formulario
    header('Location: notisdb.php');
    exit;
}

// Obtener las notificaciones
$sql = "SELECT * FROM notificaciones ORDER BY fecha DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - UVG-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> 
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-gradient-to-r from-red-700 to-red-700 p-6 shadow-md text-white flex justify-between items-center">
        <h1 class="text-4xl font-bold">Notificaciones</h1>
        <a href="index.php" class="text-white hover:underline">
            <i class="fas fa-home"></i> Inicio
        </a>
    </header>

    <main class="container mx-auto py-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Historial de Notificaciones</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg">
                    <thead>
                        <tr class="bg-gray-50 text-gray-700">
                            <th class="py-3 px-6">Mensaje</th>
                            <th class="py-3 px-6">Fecha</th>
                            <th class="py-3 px-6">Tipo</th>
                            <th class="py-3 px-6">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($notificacion = $result->fetch_assoc()): ?>
                        <tr class="border-t border-gray-200">
                            <td class="py-4 px-6"><?= htmlspecialchars($notificacion['mensaje']) ?></td>
                            <td class="py-4 px-6"><?= htmlspecialchars($notificacion['fecha']) ?></td>
                            <td class="py-4 px-6"><?= htmlspecialchars($notificacion['tipo']) ?></td>
                            <td class="py-4 px-6">
                                <!-- Formulario para eliminar la notificación -->
                                <form action="notisdb.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta notificación?');">
                                    <input type="hidden" name="notificacion_id" value="<?= $notificacion['id'] ?>">
                                    <button type="submit" name="eliminar_notificacion" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow-md">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
