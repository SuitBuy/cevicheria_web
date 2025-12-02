<?php
require 'conexion.php'; 

function alertaYRedirigir($mensaje, $url) {
    echo "<script>
            alert('" . addslashes($mensaje) . "');
            window.location.href = '$url';
          </script>";
    exit;
}

function alertaYVolver($mensaje) {
    echo "<script>
            alert('" . addslashes($mensaje) . "');
            window.history.back();
          </script>";
    exit;
}

if (empty($_POST)) {
    alertaYVolver("Error: No se enviaron datos.");
}

$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$personas = isset($_POST['personas']) ? intval($_POST['personas']) : 0;
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$dni = $_POST['dni'] ?? '';
$edad = $_POST['edad'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';

// Validación (Sin código de operación)
if (empty($fecha) || empty($hora) || $personas <= 0 || empty($nombre) || empty($telefono)) {
    alertaYVolver("Faltan datos obligatorios. Verifica fecha, hora y teléfono.");
}

// Lógica de Aforo
$limite_aforo = 30; 
$sql_aforo = "SELECT SUM(personas) as total FROM reservas WHERE fecha = ? AND hora = ? AND estado != 'Rechazado' AND estado != 'Expirado'";
$stmt_check = $conn->prepare($sql_aforo);

if ($stmt_check) {
    $stmt_check->bind_param("ss", $fecha, $hora);
    $stmt_check->execute();
    $resultado = $stmt_check->get_result();
    $fila = $resultado->fetch_assoc();
    $ocupados = $fila['total'] ? intval($fila['total']) : 0;
    $stmt_check->close();

    if (($ocupados + $personas) > $limite_aforo) {
        $disponibles = $limite_aforo - $ocupados;
        alertaYVolver("Lo sentimos, a las $hora solo quedan $disponibles lugares disponibles.");
    }
}

// GUARDAR EN BD (Sin columna codigo_operacion)
$sql_insertar = "INSERT INTO reservas (nombres, apellidos, dni, edad, email, telefono, personas, fecha, hora, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente')";
$stmt = $conn->prepare($sql_insertar);

if ($stmt) {
    // 9 parámetros
    $stmt->bind_param("sssisssis", 
        $nombre, 
        $apellido, 
        $dni, 
        $edad, 
        $email, 
        $telefono,
        $personas, 
        $fecha, 
        $hora
    );

    if ($stmt->execute()) {
        alertaYRedirigir("¡Solicitud enviada! En breve te escribiremos al WhatsApp para coordinar el pago de S/ 20.00 y confirmar tu mesa.", "../index.html");
    } else {
        alertaYVolver("Error al guardar en BD: " . $stmt->error);
    }
    $stmt->close();
} else {
    alertaYVolver("Error preparando la consulta SQL.");
}

$conn->close();
?>