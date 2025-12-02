<?php
// 1. Configuraciones iniciales obligatorias
header('Content-Type: application/json; charset=utf-8');
require 'conexion.php';

// 2. Recibir el JSON enviado por JavaScript
$json_recibido = file_get_contents("php://input");
$data = json_decode($json_recibido, true);

// 3. Validar si llegaron datos
if (!$data) {
    echo json_encode(["success" => false, "message" => "No se recibieron datos o el JSON es inválido."]);
    exit;
}

// 4. Extraer variables para facilitar la lectura
// Usamos el operador '??' para evitar errores si falta algún campo opcional
$fecha = $data['fecha'] ?? '';
$hora = $data['hora'] ?? '';
$personas = isset($data['personas']) ? intval($data['personas']) : 0;

// Validar campos críticos
if (empty($fecha) || empty($hora) || $personas <= 0) {
    echo json_encode(["success" => false, "message" => "Faltan datos de la reserva (Fecha, Hora o Personas)."]);
    exit;
}

// --- LOGICA DE AFORO (Máximo 30 personas por turno) ---
$limite_aforo = 30;

// Consulta de verificación
$sql_aforo = "SELECT SUM(personas) as total FROM reservas WHERE fecha = ? AND hora = ? AND estado != 'Rechazado' AND estado != 'Expirado'";
$stmt_check = $conn->prepare($sql_aforo);

if ($stmt_check) {
    $stmt_check->bind_param("ss", $fecha, $hora);
    $stmt_check->execute();
    $resultado = $stmt_check->get_result();
    $fila = $resultado->fetch_assoc();
    $ocupados = $fila['total'] ? intval($fila['total']) : 0;
    $stmt_check->close();

    // Si supera el límite, detenemos y avisamos
    if (($ocupados + $personas) > $limite_aforo) {
        $disponibles = $limite_aforo - $ocupados;
        echo json_encode(["success" => false, "message" => "Lo sentimos, a las $hora solo quedan $disponibles lugares."]);
        exit;
    }
}

// --- GUARDAR EN BASE DE DATOS ---
// Extraemos los datos del cliente del objeto anidado
$cliente = $data['cliente'] ?? [];
$nombre = $cliente['nombre'] ?? '';
$apellido = $cliente['apellido'] ?? '';
$dni = $cliente['dni'] ?? '';
$edad = $cliente['edad'] ?? 0;
$email = $cliente['email'] ?? '';
$telefono = $cliente['telefono'] ?? '';
$codigo = $cliente['codigo_operacion'] ?? ''; // El código Yape

$sql_insertar = "INSERT INTO reservas (nombres, apellidos, dni, edad, email, telefono, codigo_operacion, personas, fecha, hora) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insertar);

if ($stmt) {
    $stmt->bind_param(
        "sssisssiss",
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
        echo json_encode(["success" => true, "message" => "¡Reserva recibida! Validaremos tu pago en breve."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar en BD: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Error preparando la consulta: " . $conn->error]);
}

$conn->close();
