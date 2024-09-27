<?php
require 'session_handler.php';  // Incluye el archivo con la clase de sesiones

$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);

session_start();  // Inicia la sesión
require 'db.php'; 

$error = '';

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - UVG-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-white p-6 shadow-md text-center">
        <h1 class="text-3xl font-bold">UVG-Shop</h1>
    </header>

    <main class="container mx-auto py-10 text-center">
        <h2 class="text-xl font-semibold mb-6">Iniciar Sesión</h2>
        <form method="POST" class="w-full max-w-sm mx-auto">
            <div class="mb-4">
                <input type="text" name="username" placeholder="Usuario" class="bg-gray-200 p-3 rounded w-full" required>
            </div>
            <div class="mb-4">
                <input type="password" name="password" placeholder="Contraseña" class="bg-gray-200 p-3 rounded w-full" required>
            </div>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?= $error ?></p>
            <?php endif; ?>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Iniciar Sesión</button>

        </form>
        <p class="mt-6">
            ¿No tienes una cuenta? 
            <a href="registro.php" class="text-blue-500 hover:underline">Regístrate aquí</a>.
        </p>
     
    </main>
    
</body>
</html>
