<?php
require 'session_handler.php';  // Incluye el archivo con la clase de sesiones

$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

session_start();  // Inicia la sesión
require 'db.php'; 

$error = '';
//PARA INICIO DE SESION.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT * FROM usuarios WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['password'] === md5($password)) {
       
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
       
        $error = 'Usuario o contraseña incorrectos.';
    }
}


//PARA REGISTRARSE


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>FORMULARIO DE REGISTRO E INICIO SESIÓN</title>
</head>

<body>
    

    <div class="container-form ">
        <div class="information">
            <div class="info-childs">
                <h2>¡¡Bienvenido nuevamente!!</h2>
                <p>Para unirte a nuestra comunidad por favor Inicia Sesión con tus datos</p>
                <input class="register" type="button" value="Registrarse" id="sign-up" onclick="window.location.href='registro.php'">

            </div>
        </div>
        <div class="form-information">
            <div class="form-information-childs">
                <h2>Iniciar Sesión</h2>
                
                <p>o Iniciar Sesión con una cuenta</p>
                <form method="POST" class="form form-login"novalidate>
                    <div>
                        <label >
                            <i class='bx bx-envelope' ></i>
                            <input type="email" placeholder="Correo Electronico" name="username">
                        </label>
                    </div>
                    <div>
                        <label>
                            <i class='bx bx-lock-alt' ></i>
                            <input type="password" placeholder="Contraseña" name="password">
                        </label>
                    </div>
                    <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?= $error ?></p>
            <?php endif; ?>
                    <input type="submit" value="Iniciar Sesión">
                 
                </form>
            </div>
        </div>
    </div>
  
   
</body>
</html>


