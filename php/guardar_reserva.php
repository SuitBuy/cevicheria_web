<?php
// SIN HEADER CONTENT-TYPE JSON
require 'conexion.php'; 

// Función auxiliar para alertas y redirección
function alertaYRedirigir($mensaje, $url) {
    echo "<script>
            alert('" . addslashes($mensaje) . "');
            window.location.href = '$url';
          </script>";
    exit;
}

// Función auxiliar para alertas y volver atrás
function alertaYVolver($mensaje) {
    echo "<script>
            alert('" . addslashes($mensaje) . "');
            window.history.back();
          </script>";
    exit;
}

// 1. Validar que vengan datos
if (empty($_POST)) {
    alertaYVolver("Error: No se enviaron datos.");
}

// 2. Recolección de datos
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$personas = isset($_POST['personas']) ? intval($_POST['personas']) : 0;
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$dni = $_POST['dni'] ?? '';
$edad = $_POST['edad'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$codigo = $_POST['codigo_operacion'] ?? '';

// 3. Validación de campos obligatorios
if (empty($fecha) || empty($hora) || $personas <= 0 || empty($nombre) || empty($codigo)) {
    alertaYVolver("Faltan datos obligatorios. Verifica fecha, hora y código Yape.");
}

// 4. Lógica de Aforo
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
} else {
    alertaYVolver("Error verificando disponibilidad.");
}

// 5. Insertar Reserva
$sql_insertar = "INSERT INTO reservas (nombres, apellidos, dni, edad, email, telefono, codigo_operacion, personas, fecha, hora) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insertar);

if ($stmt) {
    $stmt->bind_param("sssisssiss", 
        $nombre, 
        $apellido, 
        $dni, 
        $edad, 
        $email, 
        $telefono,
        $codigo,
        $personas, 
        $fecha, 
        $hora
    );

    if ($stmt->execute()) {
        // ÉXITO: Mensaje y redirigir al inicio
        alertaYRedirigir("¡Reserva recibida con éxito! Validaremos tu pago en breve.", "../index.html");
    } else {
        alertaYVolver("Error al guardar en BD: " . $stmt->error);
    }
    $stmt->close();
} else {
    alertaYVolver("Error preparando la consulta SQL.");
}

$conn->close();
?>