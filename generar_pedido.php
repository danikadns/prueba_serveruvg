<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'session_handler.php';  
require 'vendor/autoload.php'; // Cargar el autoloader de AWS SDK

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;
use Aws\Sqs\SqsClient;

$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

session_start();  // Inicia la sesión

include 'db.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cliente') {
    header('Location: index.php'); 
    exit;
}

$username = $_SESSION['username'];

// Obtener el cliente_id y email del cliente desde la tabla usuarios
$sql = "SELECT id, email FROM usuarios WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cliente_id = $row['id'];
    $email_cliente = $row['email'];

    // Insertar el pedido en la tabla pedidos
    $sql = "INSERT INTO pedidos (cliente_id, cliente_username) VALUES ('$cliente_id', '$username')";

    // Verificar si la consulta fue exitosa
    if ($conn->query($sql) === TRUE) {
        $_SESSION['pedido_generado'] = true;

        // Enviar correo electrónico usando SES
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
                        'Data' => 'Confirmación de Pedido',
                        'Charset' => 'UTF-8',
                    ],
                    'Body' => [
                        'Text' => [
                            'Data' => "Hola $username, tu pedido se ha generado con éxito y está en proceso.",
                            'Charset' => 'UTF-8',
                        ],
                    ],
                ],
            ]);

            echo "Pedido generado y correo enviado.";
        } catch (AwsException $e) {
            echo "Error en el proceso de notificación: " . $e->getMessage();
            exit;
        }

        // Enviar notificación por SNS
        try {
            $snsClient = new SnsClient([
                'region' => 'us-east-1', 
                'version' => 'latest',
            ]);
            
            $result = $snsClient->publish([
                'Message' => "Tu pedido se ha generado con éxito.",
                'TopicArn' => 'arn:aws:sns:us-east-1:010526258440:uvgshopsns',
            ]);

            echo "Notificación SNS enviada.";
        } catch (AwsException $e) {
            echo "Error al enviar notificación por SNS: " . $e->getMessage();
            exit;
        }

        // Enviar mensaje a SQS
        try {
            $sqsClient = new SqsClient([
                'region' => 'us-east-1',
                'version' => 'latest',
            ]);
        
            $messageBody = json_encode([
                'source' => 'direct',  // Campo adicional para diferenciar el mensaje
                'pedido_id' => $conn->insert_id,  // ID del pedido recién creado
                'cliente_id' => $cliente_id,
                'email_cliente' => $email_cliente,
                'cliente_username' => $username
            ]);
        
            $result = $sqsClient->sendMessage([
                'QueueUrl' => 'https://sqs.us-east-1.amazonaws.com/010526258440/uvgshop',
                'MessageBody' => $messageBody,
            ]);
        
            echo "Mensaje enviado a SQS.";
        } catch (AwsException $e) {
            echo "Error al enviar mensaje a SQS: " . $e->getMessage();
            exit;
        }

        $mensaje = "Se ha generado un nuevo pedido con ID " . $conn->insert_id;
        $sql_notificacion = "INSERT INTO notificaciones (mensaje, pedido_id, tipo) VALUES (?, ?, 'Generado')";
        $stmt_notificacion = $conn->prepare($sql_notificacion);
        $stmt_notificacion->bind_param('si', $mensaje, $conn->insert_id);
        $stmt_notificacion->execute();
        $stmt_notificacion->close();

        // Redirigir al usuario de vuelta al índice solo si todo fue exitoso
        header('Location: index.php'); 
        exit;
    } else {
        // Manejar error en la inserción del pedido
        echo "Error al generar pedido: " . $conn->error;
        exit;
    }
} else {
    echo "Error: No se encontró el usuario.";
    exit;
}
?>
