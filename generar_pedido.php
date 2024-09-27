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

$_SESSION['pedido_generado'] = true; 

header('Location: index.php'); 
exit;
?>
