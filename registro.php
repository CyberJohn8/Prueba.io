<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inicializar variables del formulario
    $correo = isset($_POST['correo']) ? $_POST['correo'] : null;
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    $error = "";
    $success = "";

    if ($correo && $nombre && $password) {
        try {
            // Conexión a la base de datos
            $pdo = new PDO('mysql:host=localhost;dbname=asambleasvenezuela;charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Cifrar la contraseña
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Insertar datos en la base de datos
            $sql = "INSERT INTO usuarios (nombre, correo, password, rol) VALUES (:nombre, :correo, :password, 'usuario')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nombre' => $nombre,
                'correo' => $correo,
                'password' => $passwordHash
            ]);

            $success = "Registro exitoso. Bienvenido, $nombre.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Error: El correo ya está registrado.";
            } else {
                $error = "Error en la base de datos: " . $e->getMessage();
            }
        }
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

    <link rel="stylesheet" href="../PWA/CSS/style.css">
    <style>
        .menu-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .menu-button:hover {
            background-color: #1e7e34;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
        .success-message {
            color: green;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <h1>Registro</h1>
    <form action="procesar_registro.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Registrarse</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p class="success-message"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <footer>
        <a href="../PWA/index.html" class="menu-button">Volver al Menú</a>
    </footer>
</body>
</html>
