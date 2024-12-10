<?php
include 'conexion.php'; // Incluir la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['max_rifa'])) {
    $max_rifa = $_POST['max_rifa']; // Número máximo de la rifa

    for ($i = 1; $i <= $max_rifa; $i++) {
        $sql = "INSERT INTO rifas (numero, estado) VALUES ($i, 'disponible')";
        $conn->query($sql);
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Rifa</title>
</head>
<body>
    <h1>Generar Rifas</h1>
    <form method="POST" action="">
        <label for="max_rifa">Número máximo de la rifa:</label>
        <input type="number" name="max_rifa" id="max_rifa" required>
        <button type="submit">Generar</button>
    </form>

    <h2>Números de la rifa</h2>
    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM rifas");
            while ($row = $result->fetch_assoc()) {
                $color = 'green'; // Por defecto color verde (disponible)
                if ($row['estado'] == 'apartada') {
                    $color = 'yellow';
                } elseif ($row['estado'] == 'vendida') {
                    $color = 'red';
                }

                echo "<tr style='background-color: $color;'>
                        <td>" . $row['numero'] . "</td>
                        <td>" . $row['estado'] . "</td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
