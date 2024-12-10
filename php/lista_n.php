<?php
session_start();
include 'conexion.php'; // Incluir conexión a la base de datos

// Verificar si el usuario está logueado y tiene el rol de "jefe"
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'jefe') {
    header("Location: login.php"); // Redirigir si no está logueado como jefe
    exit();
}

// Manejar búsqueda de rifas
$buscar = '';
if (isset($_POST['buscar'])) {
    $buscar = $_POST['buscar'];
}

// Obtener la lista de compras, filtrada por número de rifa si se realizó la búsqueda
$sql = "SELECT * FROM compras WHERE numero_rifa LIKE ?";
$stmt = $conn->prepare($sql);
$buscar_param = "%$buscar%";
$stmt->bind_param("s", $buscar_param);
$stmt->execute();
$result = $stmt->get_result();

// Actualizar el estado del número de rifa si se envió el formulario
if (isset($_POST['editar_estado'])) {
    $id = $_POST['id'];
    $estado = $_POST['estado'];

    // Confirmación con JavaScript
    echo "<script>
        var confirmAction = confirm('¿Está seguro de cambiar el estado del número de rifa?');
        if (confirmAction) {
            window.location.href = 'lista_n.php?id=" . $id . "&estado=" . $estado . "';
        }
    </script>";
}

// Si se pasó el parámetro id y estado, actualizar la base de datos
if (isset($_GET['id']) && isset($_GET['estado'])) {
    $id = $_GET['id'];
    $estado = $_GET['estado'];

    // Obtener el número de rifa desde la tabla compras
    $sql = "SELECT numero_rifa FROM compras WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row) {
        $numero_rifa = $row['numero_rifa'];

        if ($estado == 'disponible') {
            // Eliminar el registro de la compra en la tabla 'compras'
            $sql = "DELETE FROM compras WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Actualizar el estado del número de rifa en la tabla 'rifas' a 'disponible'
            $sql = "UPDATE rifas SET estado = 'disponible' WHERE numero = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $numero_rifa);
            $stmt->execute();
        }

        if ($estado == 'comprado') {
            // Actualizar el estado del número de rifa en la tabla 'rifas' a 'comprado'
            $sql = "UPDATE rifas SET estado = 'comprado' WHERE numero = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $numero_rifa);
            $stmt->execute();
        }
    }

    // Redirigir para reflejar los cambios
    echo "<script>window.location.href = 'lista_n.php';</script>";
}
?>

<!-- El resto del código HTML sigue igual -->


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Compradores</title>
    <style>
        /* Estilos CSS mejorados */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f3f3f3;
            color: #333;
            text-align: center;
        }
        header {
            background: #2d3e50;
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            margin: 0;
            font-size: 28px;
        }
        .container {
            width: 80%;
            max-width: 1000px;
            margin: 0 auto;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-container input {
            padding: 10px;
            width: 300px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .table-container {
            margin-top: 20px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2d3e50;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        .form-container {
            margin-top: 10px;
            text-align: left;
        }
        .form-container select, .form-container button {
            padding: 8px 15px;
            font-size: 16px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn-accion {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-accion:hover {
            background-color: #2980b9;
        }
        .back-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .back-btn:hover {
            background-color: #c0392b;
        }
        footer {
            margin-top: 50px;
            font-size: 14px;
            color: #bbb;
        }
    </style>
</head>
<body>

<header>
    <h1>Lista de Compradores de Rifas</h1>
</header>

<div class="container">

    <!-- Buscador de rifas -->
    <div class="search-container">
        <form method="POST">
            <input type="text" name="buscar" placeholder="Buscar por número de rifa" value="<?php echo htmlspecialchars($buscar); ?>" />
            <button type="submit" class="btn-accion">Buscar</button>
        </form>
    </div>

    <!-- Tabla de compradores -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Número de Rifa</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Observaciones</th>
                    <th>Fecha de Compra</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['numero_rifa'] . "</td>";
                        echo "<td>" . $row['nombre'] . "</td>";
                        echo "<td>" . $row['apellido'] . "</td>";
                        echo "<td>" . $row['cedula'] . "</td>";
                        echo "<td>" . $row['telefono'] . "</td>";
                        echo "<td>" . $row['direccion'] . "</td>";
                        echo "<td>" . $row['observaciones'] . "</td>";
                        echo "<td>" . $row['fecha_venta'] . "</td>";
                        echo "<td>" . ucfirst($row['estado']) . "</td>";
                        echo "<td>
                                <form method='POST' class='form-container'>
                                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                                    <select name='estado'>
                                        <option value='disponible'" . ($row['estado'] == 'disponible' ? ' selected' : '') . ">Disponible</option>
                                        <option value='comprado'" . ($row['estado'] == 'comprado' ? ' selected' : '') . ">Comprado</option>
                                    </select>
                                    <button type='submit' name='editar_estado' class='btn-accion'>Actualizar</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No hay compras registradas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Botón para regresar -->
    <a href="dashboard.php">
        <button class="back-btn">Volver al panel de control</button>
    </a>

</div>

<footer>
    <p>&copy; 2024 MiRifa V2. Todos los derechos reservados.</p>
</footer>

</body>
</html>
