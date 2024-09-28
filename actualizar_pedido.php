<?php
require 'session_handler.php';  
require 'vendor/autoload.php'; // Cargar el autoloader de AWS SDK

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;

$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

session_start();

include 'db.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); 
    exit;
}

$id = $_POST['id'];
$estado = $_POST['estado'];

// Obtener la información del cliente y su correo electrónico desde la tabla usuarios
$sql = "SELECT u.id u.email, u.username FROM usuarios u 
        JOIN pedidos p ON u.id = p.cliente_id 
        WHERE p.id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cliente_id = $row['id'];
    $email_cliente = $row['email'];
    $username = $row['username'];

    // Actualizar el estado del pedido en la base de datos
    $sql = "UPDATE pedidos SET estado = '$estado' WHERE id = $id";
    if ($conn->query($sql) === TRUE) {

        // Enviar notificación por correo electrónico usando SES
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
                            'Data' => "Hola $username, el estado de tu pedido con ID $id ha cambiado a: $estado.",
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

        // Enviar notificación por SNS
        try {
            $snsClient = new SnsClient([
                'region' => 'us-east-1', 
                'version' => 'latest',
            ]);

            $snsClient->publish([
                'Message' => "Hola $username, el estado de tu pedido con ID $id ha cambiado a: $estado.",
                'TopicArn' => 'arn:aws:sns:us-east-1:010526258440:uvgshopsns',
            ]);

            echo "Notificación SNS enviada.";
        } catch (AwsException $e) {
            echo "Error al enviar notificación SNS: " . $e->getAwsErrorMessage();
            exit;
        }

        // Redirigir al usuario de vuelta al índice solo si todo fue exitoso
        header('Location: index.php'); 
        exit;
    } else {
        echo "Error al actualizar el estado del pedido: " . $conn->error;
        exit;
    }
} else {
    echo "Error: No se encontró el pedido o el usuario.";
    exit;
}
?>
