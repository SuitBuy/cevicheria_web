<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';

function registrarLog($mensaje) {
    $fecha = date("Y-m-d H:i:s");
    file_put_contents("debug_log.txt", "[$fecha] $mensaje" . PHP_EOL, FILE_APPEND);
}

function alertaYRedirigir($mensaje, $url) {
    echo "<script>alert('" . addslashes($mensaje) . "'); window.location.href = '$url';</script>";
    exit;
}

function alertaYVolver($mensaje) {
    echo "<script>alert('" . addslashes($mensaje) . "'); window.history.back();</script>";
    exit;
}

registrarLog("--- NUEVA INTENTO DE RESERVA ---");

if (empty($_POST)) {
    registrarLog("Error: $_POST está vacío.");
    die("Error: No se recibieron datos.");
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

registrarLog("Datos recibidos:");
registrarLog(" - Cliente: $nombre $apellido");
registrarLog(" - DNI: $dni | Edad: $edad");
registrarLog(" - Contacto: $telefono | $email");
registrarLog(" - Reserva: $fecha a las $hora para $personas personas");

if (empty($fecha) || empty($hora) || $personas <= 0 || empty($nombre) || empty($telefono)) {
    registrarLog("Faltan datos obligatorios.");
    alertaYVolver("Faltan datos obligatorios.");
}


$sql_insertar = "INSERT INTO reservas (nombres, apellidos, dni, edad, email, telefono, personas, fecha, hora, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pendiente')";

$stmt = $conn->prepare($sql_insertar);

if (!$stmt) {
    registrarLog("Error CRÍTICO en prepare(): " . $conn->error);
    die("Error en la consulta SQL: " . $conn->error);
}


$stmt->bind_param("sssisssss", 
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
    registrarLog("¡EXITO! Reserva guardada correctamente en la BD.");
    alertaYRedirigir("¡Solicitud enviada! En breve te escribiremos al WhatsApp para coordinar el pago de S/ 20.00 y confirmar tu mesa.", "../index.html");
} else {
    registrarLog("Error al ejecutar execute(): " . $stmt->error);
    echo "<h1>Error al guardar:</h1><p>" . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>