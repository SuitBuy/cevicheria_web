<?php
// Mantenemos la respuesta en JSON solo para que el JS sepa si mostrar la alerta de éxito o error
header('Content-Type: application/json; charset=utf-8');
require 'conexion.php'; 

// VALIDACIÓN 1: Verificar que lleguen datos por el método POST "normal"
if (empty($_POST)) {
    echo json_encode(["success" => false, "message" => "El servidor no recibió datos (POST vacío)."]);
    exit;
}

// RECOLECCIÓN DE DATOS (Directamente de $_POST, sin JSON)
// Usamos el operador ?? para evitar errores si falta algún campo
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

// VALIDACIÓN 2: Campos obligatorios
if (empty($fecha) || empty($hora) || $personas <= 0 || empty($nombre) || empty($codigo)) {
    echo json_encode(["success" => false, "message" => "Faltan datos obligatorios. Verifica fecha, hora y código Yape."]);
    exit;
}

// --- LOGICA DE AFORO (Máximo 30 personas por turno) ---
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
        echo json_encode(["success" => false, "message" => "Lo sentimos, a las $hora solo quedan $disponibles lugares."]);
        exit;
    }
}

// --- GUARDAR EN BASE DE DATOS ---
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
        echo json_encode(["success" => true, "message" => "¡Reserva recibida! Validaremos tu pago en breve."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar en BD: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Error preparando la consulta SQL: " . $conn->error]);
}

$conn->close();
?>