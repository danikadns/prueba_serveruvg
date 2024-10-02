<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; 
use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

function getMessagesFromQueue($sqsClient, $queueUrl, $maxMessages = 10) {
    try {
        $result = $sqsClient->receiveMessage([
            'QueueUrl' => $queueUrl,
            'MaxNumberOfMessages' => $maxMessages, 
            'VisibilityTimeout' => 30, 
            'WaitTimeSeconds' => 0, 
            'MessageAttributeNames' => ['All'], 
            'AttributeNames' => ['All']
        ]);

        return $result->get('Messages') ?: [];
    } catch (AwsException $e) {
        echo "<div class='text-red-500'>Error al recibir mensajes: " . $e->getMessage() . "</div>";
        return [];
    }
}

// Configuración de SQS
$queueUrl = 'https://sqs.us-east-1.amazonaws.com/010526258440/uvgshop';
$sqsClient = new SqsClient([
    'region' => 'us-east-1',
    'version' => 'latest',
]);

$messages = getMessagesFromQueue($sqsClient, $queueUrl);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - Mensajes en SQS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> 
    <style>
        body {
            background-color: #f3f4f6;
        }
        .container {
            max-width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Encabezado -->
    <header class="bg-gradient-to-r from-red-700 to-red-700 p-6 shadow-md text-white flex justify-between items-center">
        <h1 class="text-4xl font-bold">Mensajes en SQS</h1>
        <a href="index.php" class="text-white hover:underline">
            <i class="fas fa-home"></i> Inicio
        </a>
    </header>

    <!-- Contenido principal -->
    <main class="container mx-auto py-10 px-6">
        <div class="bg-white shadow-md rounded-lg p-6">

            <?php if (empty($messages)): ?>
                <p class="text-center text-gray-500">No hay mensajes en la cola.</p>
            <?php else: ?>
                <!-- Tabla para mensajes de tipo Notification -->
                <h2 class="text-2xl font-bold mb-4">Mensajes de Notificación</h2>
                <div class="overflow-x-auto mb-8">
                    <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg">
                        <thead class="bg-blue-500 text-white">
                            <tr>
                                <th class="text-left py-3 px-4">ID del Mensaje</th>
                                <th class="text-left py-3 px-4">Cuerpo del Mensaje</th>
                                <th class="text-left py-3 px-4">Timestamp</th>
                                <th class="text-left py-3 px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php foreach ($messages as $message): ?>
                                <?php 
                                $body = json_decode($message['Body'], true);

                                // Verificar si es un mensaje de tipo "Notification"
                                if (isset($body['Type']) && $body['Type'] === 'Notification') {
                                    $messageId = htmlspecialchars($message['MessageId']);
                                    $messageContent = htmlspecialchars($body['Message']);
                                    $timestamp = htmlspecialchars($body['Timestamp']);
                                    ?>
                                    <tr class="border-b">
                                        <td class="py-3 px-4"><?= $messageId; ?></td>
                                        <td class="py-3 px-4"><?= $messageContent; ?></td>
                                        <td class="py-3 px-4"><?= $timestamp; ?></td>
                                        <td class="py-3 px-4">
                                            <form method="POST" action="eliminar_mensaje.php">
                                                <input type="hidden" name="receipt_handle" value="<?= htmlspecialchars($message['ReceiptHandle']); ?>">
                                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tabla para mensajes de tipo Direct -->
                <h2 class="text-2xl font-bold mb-4">Mensajes Directos</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg">
                        <thead class="bg-green-500 text-white">
                            <tr>
                                <th class="text-left py-3 px-4">Pedido ID</th>
                                <th class="text-left py-3 px-4">Cliente Username</th>
                                <th class="text-left py-3 px-4">Email Cliente</th>
                                <th class="text-left py-3 px-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php foreach ($messages as $message): ?>
                                <?php 
                                $body = json_decode($message['Body'], true);

                                // Verificar si es un mensaje de tipo "Direct"
                                if (isset($body['source']) && $body['source'] === 'direct') {
                                    $pedidoId = htmlspecialchars($body['pedido_id']);
                                    $clienteUsername = htmlspecialchars($body['cliente_username']);
                                    $emailCliente = htmlspecialchars($body['email_cliente']);
                                    ?>
                                    <tr class="border-b">
                                        <td class="py-3 px-4"><?= $pedidoId; ?></td>
                                        <td class="py-3 px-4"><?= $clienteUsername; ?></td>
                                        <td class="py-3 px-4"><?= $emailCliente; ?></td>
                                        <td class="py-3 px-4">
                                            <form method="POST" action="eliminar_mensaje.php">
                                                <input type="hidden" name="receipt_handle" value="<?= htmlspecialchars($message['ReceiptHandle']); ?>">
                                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
