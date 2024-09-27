

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
require 'session_handler.php';  
require 'vendor/autoload.php';

use Aws\Sns\SnsClient;

$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

session_start();  // Inicia la sesión

include 'db.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cliente') {
    header('Location: index.php'); 
    exit;
}

$username = $_SESSION['username'];
$sql = "INSERT INTO pedidos (cliente_username) VALUES ('$username')";

// Verificar si la consulta fue exitosa
if ($conn->query($sql) === TRUE) {
    $_SESSION['pedido_generado'] = true;

    try {
        $snsClient = new SnsClient([
            'region' => 'us-east-1', 
            'version' => 'latest',
        ]);
        
        $result = $snsClient->publish([
            'Message' => "Tu pedido se ha generado con éxito.",
            'TopicArn' => 'arn:aws:sns:us-east-1:010526258440:uvgshopsns',
        ]);

        echo "Pedido actualizado y notificación enviada.";
    } catch (AwsException $e) {
        echo "Error al enviar notificación: " . $e->getMessage();
        exit;
    }

    // Redirigir al usuario de vuelta al índice solo si todo fue exitoso
    header('Location: index.php'); 
    exit;
} else {
    // Manejar error en la inserción del pedido
    echo "Error al generar pedido: " . $conn->error;
    exit;
}
?>

