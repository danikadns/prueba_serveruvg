<?php
require 'db.php';

if (isset($_GET['email'])) {
    $email = urldecode($_GET['email']);

    // Actualizar el estado de verificación del usuario
    $stmt = $conn->prepare('UPDATE usuarios SET verificado = 1 WHERE email = ?');
    $stmt->bind_param('s', $email);

    if ($stmt->execute()) {
        echo "Tu correo ha sido verificado exitosamente. Ya puedes iniciar sesión.";
    } else {
        echo "Hubo un error al verificar tu correo. Por favor, intenta nuevamente.";
    }
}


? >