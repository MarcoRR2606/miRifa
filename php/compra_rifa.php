<?php
include 'conexion.php'; // Incluir la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numero_rifa'])) {
    // Depuración: Mostrar los datos que se reciben
    var_dump($_POST);
    
    // Validar que todos los campos estén completos
    if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['cedula']) ||
        empty($_POST['telefono']) || empty($_POST['direccion'])) {
        echo "Todos los campos son obligatorios.";
    } else {
        // Recibir los datos del formulario
        $numero_rifa = $_POST['numero_rifa'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $cedula = $_POST['cedula'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $observaciones = $_POST['observaciones'];
        $fecha_venta = date('Y-m-d');

        // Actualizar el estado de la rifa a "vendida" y guardar los datos del cliente
        $sql = "UPDATE rifas SET estado='vendida', nombre_cliente='$nombre', apellido_cliente='$apellido', 
                cedula_cliente='$cedula', telefono_cliente='$telefono', direccion_cliente='$direccion', 
                observaciones='$observaciones', fecha_venta='$fecha_venta' WHERE numero=$numero_rifa";
        
        if ($conn->query($sql) === TRUE) {
            echo "Registro actualizado correctamente."; // Mensaje de éxito
            // Redirigir a WhatsApp para cobrar
            $mensaje = "¡Hola! Te han comprado el número $numero_rifa para la rifa. \nNombre: $nombre $apellido \nCédula: $cedula";
            $whatsapp_url = "https://wa.me/04145325220?text=" . urlencode($mensaje);
            header("Location: $whatsapp_url");
            exit();
        } else {
            echo "Error: " . $conn->error; // Mostrar error de consulta
        }
    }
}
?>
