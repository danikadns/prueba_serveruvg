<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; // Cargar el autoloader de AWS SDK

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

function getMessagesFromQueue($sqsClient, $queueUrl, $maxMessages = 10) {
    try {
        $result = $sqsClient->receiveMessage([
            'QueueUrl' => $queueUrl,
            'MaxNumberOfMessages' => $maxMessages, // Máximo número de mensajes a recibir
            'VisibilityTimeout' => 0, // Tiempo que el mensaje estará oculto antes de volver a estar disponible
            'WaitTimeSeconds' => 0 // Tiempo de espera para recibir mensajes
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
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Notificaciones - Mensajes en SQS</h1>

        <?php if (empty($messages)): ?>
            <p class="text-center text-gray-500">No hay mensajes en la cola.</p>
        <?php else: ?>
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="text-left py-3 px-4">ID del Mensaje</th>
                        <th class="text-left py-3 px-4">Cuerpo del Mensaje</th>
                        <th class="text-left py-3 px-4">Fecha de Recepción</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php foreach ($messages as $message): ?>
                        <?php 
                        $body = json_decode($message['Body'], true);
                        if (isset($body['Type']) && $body['Type'] === 'Notification') {
                            // Este es un mensaje de SNS
                            $displayBody = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                        } else {
                            // Este es un mensaje directo
                            $displayBody = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                        }
                        ?>
                        <tr class="border-b">
                            <td class="py-3 px-4"><?= htmlspecialchars($message['MessageId']); ?></td>
                            <td class="py-3 px-4">
                                <pre class="bg-gray-100 p-2 rounded"><?= $displayBody; ?></pre>
                            </td>
                            <td class="py-3 px-4"><?= date('Y-m-d H:i:s'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
