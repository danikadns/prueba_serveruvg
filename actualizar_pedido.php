<?php
require 'session_handler.php';  // Incluye el archivo con la clase de sesiones

$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

session_start();  // Inicia la sesión

include 'db.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php'); 
    exit;
}

$id = $_POST['id'];
$estado = $_POST['estado'];
$sql = "UPDATE pedidos SET estado = '$estado' WHERE id = $id";
$conn->query($sql);

header('Location: index.php'); 
exit;
?>

Numeros de Telefono

+502 4543 5139   - Justin Milián
+502 5538 6057   - Mario Ramirez
+502 4853 1943   - Walter Chocoyo
+502 3105 6028   - Pablo Si
+502 

Personas Identificadas

Justin Milián
Mario Ramirez
Walter Chocoyo
Pablo S

Correos 

Correo Instagram - l*******8@gmail.com
