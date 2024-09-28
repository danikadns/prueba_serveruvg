<?php
require 'session_handler.php';  // Incluye el archivo con la clase de sesiones
require 'vendor/autoload.php';

$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;

session_start();  // Inicia la sesión

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
                            'Data' => "Hola! Tu pedido con ID $id se ha eliminado.",
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
                'Message' => "Hola! el estado de tu pedido con ID $id se ha eliminado.",
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
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-white p-6 shadow-md text-center">
        <h1 class="text-3xl font-bold">Bienvenido, <?= htmlspecialchars($username) ?></h1>
    </header>

    <main class="container mx-auto py-10 text-center">
        <h2 class="text-xl font-semibold mb-6"><?= $role === 'cliente' ? 'Mis Pedidos 📦' : 'Gestión de Pedidos 📦' ?></h2>

        <?php if ($role === 'cliente' && isset($_SESSION['pedido_generado'])): ?>
            <p class="text-green-500 mb-4">¡Pedido generado exitosamente!</p>
            <?php unset($_SESSION['pedido_generado']); ?>
        <?php endif; ?>

        <?php if ($role === 'cliente'): ?>
            <form action="generar_pedido.php" method="POST" class="mb-6">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Generar Pedido</button>
            </form>
        <?php endif; ?>
        <?php if ($role === 'admin'): ?>
            <form action="notificaciones.php" method="POST" class="mb-6">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Notificaciones</button>
            </form>
        <?php endif; ?>

        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="py-2">ID</th>
                    <?php if ($role === 'admin') echo '<th class="py-2">Cliente</th>'; ?>
                    <th class="py-2">Estado</th>
                    <th class="py-2">Fecha</th>
                    <?php if ($role === 'admin') echo '<th class="py-2">Acciones</th>'; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($pedido = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-2 border-b"><?= $pedido['id'] ?></td>
                        <?php if ($role === 'admin'): ?>
                            <td class="py-2 border-b"><?= $pedido['cliente_username'] ?></td>
                        <?php endif; ?>
                        <td class="py-2 border-b"><?= $pedido['estado'] ?></td>
                        <td class="py-2 border-b"><?= $pedido['fecha'] ?></td>
                        <?php if ($role === 'admin'): ?>
                            <td class="py-2 border-b">
                                <?php if ($pedido['estado'] !== 'Cancelado'): ?>
                                    <form action="index.php" method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
                                        <select name="estado" class="mr-2">
                                            <?php
                                            $estados = ['En Proceso', 'En Camino', 'Entregado'];
                                            foreach ($estados as $opcion): ?>
                                                <option value="<?= $opcion ?>" <?= $opcion === $pedido['estado'] ? 'selected' : '' ?>>
                                                    <?= $opcion ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="action" value="actualizar" class="bg-blue-500 text-white px-2 py-1 rounded">Actualizar</button>
                                    </form>
                                    <form action="index.php" method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
                                        <button type="submit" name="action" value="cancelar" class="bg-yellow-500 text-white px-2 py-1 rounded ml-2">Cancelar</button>
                                    </form>
                                <?php endif; ?>
                                <form action="index.php" method="POST" class="inline">
                                    <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
                                    <button type="submit" name="action" value="eliminar" class="bg-red-500 text-white px-2 py-1 rounded <?= $pedido['estado'] !== 'Cancelado' ? 'ml-2' : '' ?>">Eliminar</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="mt-10">
            <a href="logout.php" class="text-blue-500">Cerrar Sesión</a>
        </div>
    </main>
</body>
</html>
