<?php
session_start();
require 'config.php'; // Conecta tu base de datos

if (isset($_GET['email'])) {
    $email = urldecode($_GET['email']);

    // Verificar si el correo existe en la base de datos y no está verificado
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND email_verificado = 0");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // Actualizar el estado de verificación del correo
        $stmt = $pdo->prepare("UPDATE usuarios SET email_verificado = 1 WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        echo "¡Correo electrónico verificado exitosamente! Ahora puedes iniciar sesión.";
    } else {
        echo "El enlace de verificación no es válido o el correo ya ha sido verificado.";
    }
} else {
    echo "No se proporcionó un correo electrónico válido.";
}
