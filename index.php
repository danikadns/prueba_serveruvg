<?php
require 'session_handler.php';
require 'vendor/autoload.php';

$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;

session_start();

include 'db.php'; 

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}


$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Manejar las acciones (actualizar, cancelar, eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = $_POST['id'];

    if ($_POST['action'] === 'actualizar') {
        $estado = $_POST['estado'];
        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $estado, $id);
        $stmt->execute();
        $stmt->close();

        try {
            $sesClient = new SesClient([
                'region' => 'us-east-1',
                'version' => 'latest',
            ]);

            $sesClient->sendEmail([
                'Source' => 'noreply@danikadonis.me',
                'Destination' => [
                    'ToAddresses' => [$email_cliente],
                ],
                'Message' => [
                    'Subject' => [
                        'Data' => 'Actualización de Pedido',
                        'Charset' => 'UTF-8',
                    ],
                    'Body' => [
                        'Text' => [
                            'Data' => "Hola! El estado de tu pedido con ID $id ha cambiado.",
                            'Charset' => 'UTF-8',
                        ],
                    ],
                ],
            ]);

            echo "Correo de actualización enviado.";
        } catch (AwsException $e) {
            echo "Error al enviar correo electrónico: " . $e->getAwsErrorMessage();
            exit;
        }
   
        try {
            $snsClient = new SnsClient([
                'region' => 'us-east-1', 
                'version' => 'latest',
            ]);

            $snsClient->publish([
                'Message' => "Hola! el estado de tu pedido con ID $id ha cambiado",
                'TopicArn' => 'arn:aws:sns:us-east-1:010526258440:uvgshopsns',
            ]);

            echo "Notificación SNS enviada.";
        } catch (AwsException $e) {
            echo "Error al enviar notificación SNS: " . $e->getAwsErrorMessage();
            exit;
        }

    } elseif ($_POST['action'] === 'cancelar') {
        $sql = "UPDATE pedidos SET estado = 'Cancelado' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        try {
            $sesClient = new SesClient([
                'region' => 'us-east-1',
                'version' => 'latest',
            ]);

            $sesClient->sendEmail([
                'Source' => 'noreply@danikadonis.me',
                'Destination' => [
                    'ToAddresses' => [$email_cliente],
                ],
                'Message' => [
                    'Subject' => [
                        'Data' => 'Actualización de Pedido',
                        'Charset' => 'UTF-8',
                    ],
                    'Body' => [
                        'Text' => [
                            'Data' => "Hola! El estado de tu pedido con ID $id ha cambiado a cancelado",
                            'Charset' => 'UTF-8',
                        ],
                    ],
                ],
            ]);

            echo "Correo de actualización enviado.";
        } catch (AwsException $e) {
            echo "Error al enviar correo electrónico: " . $e->getAwsErrorMessage();
            exit;
        }

        try {
            $snsClient = new SnsClient([
                'region' => 'us-east-1', 
                'version' => 'latest',
            ]);

            $snsClient->publish([
                'Message' => "Hola! el estado de tu pedido con ID $id ha cambiado a cancelado",
                'TopicArn' => 'arn:aws:sns:us-east-1:010526258440:uvgshopsns',
            ]);

            echo "Notificación SNS enviada.";
        } catch (AwsException $e) {
            echo "Error al enviar notificación SNS: " . $e->getAwsErrorMessage();
            exit;
        }


    } elseif ($_POST['action'] === 'eliminar') {
        $sql = "DELETE FROM pedidos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        
        try {
            $snsClient = new SnsClient([
                'region' => 'us-east-1', 
                'version' => 'latest',
            ]);

            $snsClient->publish([
                'Message' => "Hola! el estado de tu pedido con ID $id ha sido eliminado",
                'TopicArn' => 'arn:aws:sns:us-east-1:010526258440:uvgshopsns',
            ]);

            echo "Notificación SNS enviada.";
        } catch (AwsException $e) {
            echo "Error al enviar notificación SNS: " . $e->getAwsErrorMessage();
            exit;
        }

        try {
            $snsClient = new SnsClient([
                'region' => 'us-east-1', 
                'version' => 'latest',
            ]);

            $snsClient->publish([
                'Message' => "Hola! el estado de tu pedido con ID $id ha sido eliminado",
                'TopicArn' => 'arn:aws:sns:us-east-1:010526258440:uvgshopsns',
            ]);

            echo "Notificación SNS enviada.";
        } catch (AwsException $e) {
            echo "Error al enviar notificación SNS: " . $e->getAwsErrorMessage();
            exit;
        }
    }

    header('Location: index.php');
    exit;
}

$sql = $role === 'admin' ? "SELECT * FROM pedidos" : "SELECT * FROM pedidos WHERE cliente_username = '$username'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - UVG-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Para los iconos -->
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 shadow-md text-center text-white">
        <h1 class="text-4xl font-bold">Bienvenido, <?= htmlspecialchars($username) ?></h1>
    </header>

    <main class="container mx-auto py-10 text-center">
        <h2 class="text-2xl font-semibold mb-6 <?= $role === 'cliente' ? 'text-green-600' : 'text-indigo-600' ?>">
            <?= $role === 'cliente' ? 'Mis Pedidos 📦' : 'Gestión de Pedidos 📦' ?>
        </h2>

        <?php if ($role === 'cliente' && isset($_SESSION['pedido_generado'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                <p class="text-lg">¡Pedido generado exitosamente!</p>
            </div>
            <?php unset($_SESSION['pedido_generado']); ?>
        <?php endif; ?>

        <?php if ($role === 'cliente'): ?>
            <form action="generar_pedido.php" method="POST" class="mb-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out">
                    <i class="fas fa-plus mr-2"></i> Generar Pedido
                </button>
            </form>
        <?php endif; ?>
        <?php if ($role === 'admin'): ?>

           <div class="flex space-x-4 mb-6">
    <form action="notificaciones.php" method="POST">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Notificaciones</button>
    </form>
    <form action="exportar_pedidos.php" method="POST">
        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out">
            <i class="fas fa-file-export mr-2"></i> Exportar a PDF
        </button>
    </form>
</div>

        <?php endif; ?>

        <!-- Barra de búsqueda -->
        <div class="mb-6">
            <input type="text" id="buscarPedido" placeholder="Buscar pedido por ID o Estado..." class="bg-gray-200 border border-gray-300 p-2 rounded-lg mb-6" onkeyup="filtrarPedidos()">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gray-50 text-gray-700">
                        <th class="py-3 px-6">ID</th>
                        <?php if ($role === 'admin') echo '<th class="py-3 px-6">Cliente</th>'; ?>
                        <th class="py-3 px-6">Estado</th>
                        <th class="py-3 px-6">Fecha</th>
                        <?php if ($role === 'admin') echo '<th class="py-3 px-6">Acciones</th>'; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $result->fetch_assoc()): ?>
                        <tr class="border-t border-gray-200">
                            <td class="py-4 px-6"><?= $pedido['id'] ?></td>
                            <?php if ($role === 'admin'): ?>
                                <td class="py-4 px-6"><?= $pedido['cliente_username'] ?></td>
                            <?php endif; ?>
                            <td class="py-4 px-6">
                                <!-- Timeline de estado del pedido -->
                                <div class="flex justify-center items-center mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-8 h-8 flex justify-center items-center rounded-full <?= $pedido['estado'] === 'En Proceso' || $pedido['estado'] === 'En Camino' || $pedido['estado'] === 'Entregado' ? 'bg-green-500 text-white' : 'bg-gray-300' ?>">1</div>
                                        <span class="text-sm <?= $pedido['estado'] === 'En Proceso' ? 'font-bold' : 'text-gray-400' ?>">En Proceso</span>
                                    </div>
                                    <div class="w-20 h-1 bg-gray-300"></div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-8 h-8 flex justify-center items-center rounded-full <?= $pedido['estado'] === 'En Camino' || $pedido['estado'] === 'Entregado' ? 'bg-green-500 text-white' : 'bg-gray-300' ?>">2</div>
                                        <span class="text-sm <?= $pedido['estado'] === 'En Camino' ? 'font-bold' : 'text-gray-400' ?>">En Camino</span>
                                    </div>
                                    <div class="w-20 h-1 bg-gray-300"></div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-8 h-8 flex justify-center items-center rounded-full <?= $pedido['estado'] === 'Entregado' ? 'bg-green-500 text-white' : 'bg-gray-300' ?>">3</div>
                                        <span class="text-sm <?= $pedido['estado'] === 'Entregado' ? 'font-bold' : 'text-gray-400' ?>">Entregado</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6"><?= $pedido['fecha'] ?></td>
                            <?php if ($role === 'admin'): ?>
                                <td class="py-4 px-6">
                                    <?php if ($pedido['estado'] !== 'Cancelado'): ?>
                                        <form id="actualizarForm<?= $pedido['id'] ?>" action="index.php" method="POST" class="inline">
                                            <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
                                            <select name="estado" class="bg-gray-100 border border-gray-300 rounded-md p-2 mr-2">
                                                <?php
                                                $estados = ['En Proceso', 'En Camino', 'Entregado'];
                                                foreach ($estados as $opcion): ?>
                                                    <option value="<?= $opcion ?>" <?= $opcion === $pedido['estado'] ? 'selected' : '' ?>>
                                                        <?= $opcion ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" name="action" value="actualizar" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg transition duration-300 ease-in-out">
                                                <i class="fas fa-sync-alt"></i> Actualizar
                                            </button>
                                        </form>
                                        <button onclick="confirmarCancelar(<?= $pedido['id'] ?>)" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg ml-2 transition duration-300 ease-in-out">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="confirmarEliminar(<?= $pedido['id'] ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg <?= $pedido['estado'] !== 'Cancelado' ? 'ml-2' : '' ?>">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-10">
            <a href="logout.php" class="text-blue-600 hover:underline">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </main>

    <!-- Scripts para confirmación modal -->
    <script>
        function confirmarEliminar(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este pedido?')) {
                document.getElementById('eliminarForm' + id).submit();
            }
        }

        function confirmarCancelar(id) {
            if (confirm('¿Estás seguro de que quieres cancelar este pedido?')) {
                document.getElementById('cancelarForm' + id).submit();
            }
        }

        function filtrarPedidos() {
            const input = document.getElementById('buscarPedido');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const id = row.querySelector('td:nth-child(1)').textContent;
                const estado = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                if (id.includes(filter) || estado.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
