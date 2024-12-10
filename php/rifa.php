<?php
include 'conexion.php'; // Incluir conexión a la base de datos

// Procesar generación de rifas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['max_rifa'])) {
    $max_rifa = $_POST['max_rifa'];

    // Limpiar rifas existentes
    $conn->query("DELETE FROM rifas");

    // Insertar nuevos números de rifa
    for ($i = 1; $i <= $max_rifa; $i++) {
        $sql = "INSERT INTO rifas (numero, estado) VALUES ($i, 'disponible')";
        $conn->query($sql);
    }
    echo "<script>alert('Rifas generadas correctamente');</script>";
}

// Procesar venta o apartado de número
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numero']) && isset($_POST['estado'])) {
    $numero = $_POST['numero'];
    $estado = $_POST['estado'];

    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $observaciones = $_POST['observaciones'];
    $fecha_venta = $_POST['fecha_venta'];

    // Actualizar el estado del número en la tabla 'rifas'
    if ($estado == 'vendida') {
        $conn->query("UPDATE rifas SET estado = 'vendida' WHERE numero = $numero");
    }

    // Insertar la información de la compra en la tabla 'compras'
    $sql = "INSERT INTO compras (numero_rifa, nombre, apellido, cedula, telefono, direccion, observaciones, fecha_venta, estado)
            VALUES ($numero, '$nombre', '$apellido', '$cedula', '$telefono', '$direccion', '$observaciones', '$fecha_venta', '$estado')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Compra registrada correctamente');</script>";
    } else {
        echo "<script>alert('Error al registrar la compra: " . $conn->error . "');</script>";
    }

    // Redirigir a la misma página para recargar la tabla de rifas
    echo "<script>window.location.href = window.location.href;</script>";
}

// Obtener rifas
$rifas = $conn->query("SELECT * FROM rifas");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Rifas</title>
    <style>
        /* Estilos */
        body {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            background-color: rgb(163, 192, 195); /* Fondo claro */
            color: #333;
            text-align: center;
        }
        header {
            background-color: rgb(16, 111, 104);
            color: white;
            margin-bottom: 20px;
            height: 10vh;
            display: flex;
            flex-direction: column;
        }
        header h1 {
            justify-self: center;
            font-size: 28px;
        }
        .form-container {
            margin: 20px;
        }
        .form-container form {
            display: inline-flex;
            gap: 10px;
            align-items: center;
        }
        .form-container input, .form-container button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            outline: none;
        }
        .form-container input {
            width: 100px;
            font-size: 14px;
        }
        .form-container button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
        .board {
            display: grid;
            grid-template-columns: repeat(10, 1fr); /* 10 columnas */
            gap: 5px;
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .number {
            width: 100%;
            aspect-ratio: 1; /* Mantener bloques cuadrados */
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .number:hover {
            transform: scale(1.1);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.15);
        }
        .disponible {
            background-color: #28a745;
            color: white;
        }
        .vendida {
            background-color: #dc3545;
            color: white;
        }
        .disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
        /* Modal (ventana flotante) */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .formulario-compra input, .formulario-compra textarea {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .formulario-compra button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .formulario-compra button:hover {
            background-color: #45a049;
        }

        .volver-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .volver-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Gestión de Rifas</h1>
    </header>

    <!-- Formulario para generar rifas -->
    <div class="form-container">
        <form method="POST" action="">
            <label for="max_rifa">Núm. máximo:</label>
            <input type="number" name="max_rifa" id="max_rifa" required>
            <button type="submit">Generar</button>
        </form>
    </div>

    <!-- Visualización de rifas en un tablero -->
    <div class="board">
        <?php while ($row = $rifas->fetch_assoc()): ?>
            <div class="number <?= $row['estado'] ?> <?= in_array($row['estado'], ['apartada', 'vendida']) ? 'disabled' : '' ?>"
                 onclick="showForm(<?= $row['numero'] ?>, '<?= $row['estado'] ?>')">
                <?= $row['numero'] ?>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal para comprar un número -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h3>Compra Número</h3>
            <form method="POST" action="">
                <input type="hidden" name="numero" id="numero" value="">
                <input type="hidden" name="estado" id="estado" value="vendida">

                <div class="formulario-compra">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" required>

                    <label for="apellido">Apellido:</label>
                    <input type="text" name="apellido" required>

                    <label for="cedula">Cédula:</label>
                    <input type="text" name="cedula" required>

                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" required>

                    <label for="direccion">Dirección:</label>
                    <textarea name="direccion" required></textarea>

                    <label for="observaciones">Observaciones:</label>
                    <textarea name="observaciones"></textarea>

                    <label for="fecha_venta">Fecha de Venta:</label>
                    <input type="date" name="fecha_venta" required>

                    <button type="submit">Registrar Compra</button>
                </div>
            </form>
        </div>
    </div>

    

    <script>
        function showForm(numero, estado) {
            if (estado === 'disponible') {
                document.getElementById("modal").style.display = "flex";
                document.getElementById("numero").value = numero;
            }
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById("modal")) {
                document.getElementById("modal").style.display = "none";
            }
        }
    </script>
</body>
</html>
