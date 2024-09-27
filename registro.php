<?php
require 'session_handler.php';  // Incluye el archivo con la clase de sesiones

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
            header('Location: login.php'); // Redirigir al índice o a la página deseada
            exit;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario - UVG-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-white p-6 shadow-md text-center">
        <h1 class="text-3xl font-bold">UVG-Shop</h1>
    </header>

    <main class="container mx-auto py-10 text-center">
        <h2 class="text-xl font-semibold mb-6">Registrar Usuario</h2>
        <form method="POST" class="w-full max-w-sm mx-auto">
            <div class="mb-4">
                <input type="text" name="nombre" placeholder="Nombre" class="bg-gray-200 p-3 rounded w-full" required>
            </div>
            <div class="mb-4">
                <input type="text" name="username" placeholder="Nombre de Usuario" class="bg-gray-200 p-3 rounded w-full" required>
            </div>
            <div class="mb-4">
                <input type="password" name="password" placeholder="Contraseña" class="bg-gray-200 p-3 rounded w-full" required>
            </div>
            <div class="mb-4">
                <input type="email" name="email" placeholder="Correo Electrónico" class="bg-gray-200 p-3 rounded w-full" required>
            </div>
            <div class="mb-4">
                <input type="tel" name="telefono" placeholder="Número de Teléfono" class="bg-gray-200 p-3 rounded w-full" required>
            </div>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?= $error ?></p>
            <?php endif; ?>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Registrar</button>
        </form>
    </main>
</body>
</html>
