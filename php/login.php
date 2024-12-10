<?php
session_start();
include('conexion.php'); // Conexión a la base de datos

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener las credenciales ingresadas
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Consultar la base de datos
    $sql = "SELECT * FROM usuarios WHERE usuario = ? AND contrasena = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $contrasena);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Verificar si existe el usuario
    if ($resultado->num_rows > 0) {
        // Obtener los datos del usuario
        $usuario_db = $resultado->fetch_assoc();

        // Verificar si el rol es 'jefe'
        if ($usuario_db['rol'] == 'jefe') {
            $_SESSION['usuario'] = $usuario_db['usuario']; // Guardar el nombre de usuario en la sesión
            $_SESSION['rol'] = $usuario_db['rol']; // Guardar el rol en la sesión
            header("Location: dashboard.php"); // Redirigir a la página del jefe
            exit();
        } else {
            $error = "No tienes permisos para acceder.";
        }
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style-nav.css">
    <link rel="stylesheet" href="../css/style-footer.css">
    <link rel="stylesheet" href="../css/style-login.css">
    <title>MiRifa</title>
</head>
<body>
    <nav>
        <ul>
            <li>
                <a href="../index.html">Inicio</a>
            </li>
            <li>
                <a href="rifa.php">Rifa</a>
            </li>                <li>
                <a href="../php/login.php">Ingresar</a>
            </li>
        </ul>
    </nav>
    <main>
    <div class="login-container">
            <form action="login.php" method="POST">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>

                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>

                <button type="submit">Iniciar sesión</button>
            </form>

            <?php
            // Mostrar errores si existen
            if (isset($error)) {
                echo "<p class='error'>$error</p>";
            }
            ?>
        </div>
    </main>
    <footer>
        <h2>Síguenos En Nuestras Redes Sociales</h2>
        <a href="">Facebook</a>
        <a href="">Instagram</a>
        <a href="">Tik Tok</a>
        <p>© 2024 MiRifa V2. Todos los derechos reservados</p>
    </footer>
</body>
</html>