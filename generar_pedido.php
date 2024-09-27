<?php
require 'session_handler.php';  // Incluye el archivo con la clase de sesiones

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
$conn->query($sql);

try {
    $snsClient = new SnsClient([
        'region' => 'us-east-1', // Cambia esto por tu región de AWS
        'version' => 'latest',
        'credentials' => [
            'key'    => 'AKIAQE43J6UEEIEDDHZP',
            'secret' => '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',
        ],
    ]);
    
    $result = $snsClient->publish([
            'Message' => "Tu pedido se ha generado con éxito.",
            'TopicArn' => 'arn:aws:sns:us-east-1:010526258440:uvgshopsns',
        ]);

    echo "Pedido actualizado y notificación enviada.";
    } catch (AwsException $e) {
        echo "Error al enviar notificación: " . $e->getMessage();
    }

$_SESSION['pedido_generado'] = true; 

header('Location: index.php'); 
exit;
?>
