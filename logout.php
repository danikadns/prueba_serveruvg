<?php
require 'session_handler.php';  // Incluye el archivo con la clase de sesiones

$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

session_start();  // Inicia la sesión

session_unset(); 
session_destroy(); 

header('Location: login.php'); 
exit;
?>
