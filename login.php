<?php
session_start();
include 'conexion.php'; // Asegúrate de que el archivo 'conexion.php' esté en la ubicación correcta

// Redirigir si el usuario ya está autenticado
if (isset($_SESSION['usuario_id'])) {
    header("Location: cartelera.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    try {
        // Preparar y ejecutar consulta
        $query = $pdo->prepare("SELECT id, nombre, rol, password FROM usuarios WHERE correo = ?");
        $query->execute([$correo]);
        $usuario = $query->fetch();

        // Verificar usuario y contraseña
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Regenerar ID de sesión
            session_regenerate_id();

            // Guardar datos en la sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirigir según el rol
            header("Location: cartelera.php");
            exit;
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $error = "Error en la conexión: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../PWA/CSS/style.css">
    <style>
        .menu-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .menu-button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <h1>Iniciar Sesión</h1>
    <form action="procesar_login.php" method="POST">
        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Iniciar Sesión</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <footer>
        <!-- Botón para volver al menú -->
        <a href="../PWA/index.html" class="menu-button">Volver al Menú</a>
    </footer>
</body>
</html>
