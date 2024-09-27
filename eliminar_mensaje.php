<?php

require 'vendor/autoload.php'; // Cargar el autoloader de AWS SDK

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

$sqsClient = new SqsClient([
    'region' => 'us-east-1',
    'version' => 'latest',
]);

$queueUrl = 'https://sqs.us-east-1.amazonaws.com/010526258440/uvgshop';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiptHandle = $_POST['receipt_handle'];

    try {
        $result = $sqsClient->deleteMessage([
            'QueueUrl' => $queueUrl,
            'ReceiptHandle' => $receiptHandle,
        ]);
        echo "Mensaje eliminado correctamente.";
    } catch (AwsException $e) {
        echo "Error al eliminar el mensaje: " . $e->getMessage();
    }

    // Redirigir de vuelta a la página de notificaciones
    header('Location: notificaciones.php');
    exit;
}

?>
