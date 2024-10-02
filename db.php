<?php
$servername = "54.166.21.47";
$username = "root"; 
$password = "root"; 
$database = "db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
