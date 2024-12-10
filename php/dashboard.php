<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de "jefe"
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'jefe') {
    header("Location: login.php"); // Redirigir si no está logueado como jefe
    exit();
}

// Cerrar sesión si el botón es presionado
if (isset($_POST['logout'])) {
    session_unset(); // Elimina todas las variables de sesión
    session_destroy(); // Destruye la sesión
    header("Location: ../index.html"); // Redirige al index.html en el directorio raíz
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style-nav.css">
    <link rel="stylesheet" href="../css/style-footer.css">
    <link rel="stylesheet" href="../css/style-dashboard.css">
    <title>MiRifa</title>
</head>
<body>

<header>
<h1>Panel de Control</h1>
    <nav>
        <ul>
            <li>
                <a href="lista_n.php">Clientes</a>
            </li>
            <li>
                <a href="rifa.php">Rifa</a>
            </li>                
            <li>
                <a href="../index.html">Lorem</a>
            </li>
            <li>
                <a href="../index.html">Lorem</a>
            </li>
            <li>
                <a href="../index.html">Lorem</a>
            </li>
        </ul>
    </nav>

</header>

<div class="container">
    <h2 class="welcome-msg">Bienvenido, <?php echo $_SESSION['usuario']; ?>.</h2>

    <p>Como jefe, tienes acceso a toda la información de tus clientes y la rifa en curso

    <!-- Botón para acceder a la lista de compradores -->
    
    <!-- Botón de cerrar sesión -->
    <form method="post" class="button-container">
        <button type="submit" name="logout" class="logout-btn">Cerrar sesión</button>
    </form>
</div>

<footer>
        <h2>Síguenos En Nuestras Redes Sociales</h2>
        <a href="">Facebook</a>
        <a href="">Instagram</a>
        <a href="">Tik Tok</a>
        <p>© 2024 MiRifa V2. Todos los derechos reservados</p>
    </footer>
</body>
</html>
