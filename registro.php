<?php
require 'session_handler.php';  // Incluye el archivo con la clase de sesiones
require 'vendor/autoload.php'; // Cargar el autoloader de AWS SDK

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;


$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

session_start();  // Inicia la sesión

require 'db.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    // Verificar si el username ya existe en la base de datos
    $stmt = $conn->prepare('SELECT * FROM usuarios WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $error = 'El nombre de usuario ya está en uso. Por favor, elige otro.';
    } else {
        // Registrar el nuevo usuario con rol de cliente
        $stmt = $conn->prepare('INSERT INTO usuarios (nombre, username, password, email, telefono, role) VALUES (?, ?, ?, ?, ?, ?)');
        $hashed_password = md5($password); // Encriptar la contraseña
        $role = 'cliente'; // Asignar el rol de cliente
        $stmt->bind_param('ssssss', $nombre, $username, $hashed_password, $email, $telefono, $role);

        if ($stmt->execute()) {

            // Registro exitoso
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role; // Asigna el rol de cliente a la sesión
            
            try {
                $sesClient = new SesClient([
                    'region' => 'us-east-1',
                    'version' => 'latest',
                ]);
                
                $result = $sesClient->verifyEmailIdentity([
                    'EmailAddress' => $email,
                ]);
                // Construir el enlace de verificación
                /*
                 $verificationLink = "https://danikadonis.me/verificar.php?email=" . urlencode($email);

                    $result = $sesClient->sendEmail([
                        'Source' => 'noreply@danikadonis.me',
                        'Destination' => [
                            'ToAddresses' => [$email],
                        ],
                        'Message' => [
                            'Subject' => [
                                'Data' => 'Confirma tu correo electrónico',
                                'Charset' => 'UTF-8',
                            ],
                            'Body' => [
                                'Html' => [
                                    'Data' => "Por favor, haz clic en el siguiente enlace para verificar tu cuenta: <a href='$verificationLink'>Verificar Correo</a>",
                                    'Charset' => 'UTF-8',
                                ],
                                'Text' => [
                                    'Data' => "Por favor, copia y pega el siguiente enlace en tu navegador para verificar tu cuenta: $verificationLink",
                                    'Charset' => 'UTF-8',
                                ],
                            ],
                        ],
                    ]);*/
                    //agregacion no se para que sirve
                   // var_dump($result);
                   header('Location: login.php'); // Redirigir al índice o a la página deseada
                   exit;
                //echo "Registro exitoso. Por favor, verifica tu correo electrónico.";
            } catch (AwsException $e) {
                echo "Error al enviar la solicitud de verificación de correo electrónico: " . $e->getMessage();
                exit;
            }


           
        } else {
            $error = 'Hubo un error al registrar el usuario. Por favor, intenta de nuevo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title> Registro</title>
</head>
<body >
<div class="container-form register">
        <div class="information">
            <div class="info-childs">
                <h2>Bienvenido</h2>
                <p>Para unirte a nuestra comunidad por favor Inicia Sesión con tus datos</p>
                <input class="register" type="button" value="Iniciar Sesion" id="sign-up" onclick="window.location.href='login.php'">
            </div>
        </div>
        <div class="form-information">
            <div class="form-information-childs">
                <h2>Crear una Cuenta</h2>
               
                <p>o usa tu email para registrarte</p>
                <form method="POST" class="form form-register" novalidate>
                    <div>
                        <label>
                            <i class='bx bx-user' ></i>
                            <input type="text" placeholder="Nombre " name="nombre" >
                        </label>
                    </div>
                    <div>
                        <label>
                            <i class='bx bx-user' ></i>
                            <input type="text" placeholder="Nombre Usuario" name="username" >
                        </label>
                    </div>
                    <div>
                        <label>
                            <i class='bx bx-lock-alt' ></i>
                            <input type="password" placeholder="Contraseña" name="password">
                        </label>
                    </div>
                    <div>
                        <label >
                            <i class='bx bx-envelope' ></i>
                            <input type="email" placeholder="Correo Electronico" name="email" >
                        </label>
                    </div>
                    <div>
                        <label >
                            <i class='bx bx-envelope' ></i>
                            <input type="tel" placeholder="telefono" name="telefono" >
                        </label>
                    </div>
                    <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?= $error ?></p>
            <?php endif; ?>
                   
            <input type="submit" value="Registrarse">
                    
                  
                </form>
            </div>
        </div>
    </div>


</body>
</html>
