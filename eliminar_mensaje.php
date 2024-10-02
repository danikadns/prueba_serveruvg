<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; // Cargar el autoloader de AWS SDK

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receipt_handle'])) {
    $receiptHandle = $_POST['receipt_handle'];

    // Configuración de SQS
    $queueUrl = 'https://sqs.us-east-1.amazonaws.com/010526258440/uvgshop';
    $sqsClient = new SqsClient([
        'region' => 'us-east-1',
        'version' => 'latest',
    ]);

    // Función para eliminar el mensaje
    try {
        $sqsClient->deleteMessage([
            'QueueUrl' => $queueUrl,
            'ReceiptHandle' => $receiptHandle
        ]);

        echo "Mensaje eliminado correctamente.";
    } catch (AwsException $e) {
        echo "Error al eliminar mensaje: " . $e->getMessage();
    }

    // Redirigir de vuelta a la página de notificaciones
    header('Location: notificaciones.php');
    exit;
} else {
    echo "Solicitud inválida.";
}

?>
