<?php
// IMPORTANTE: require debe ser la primera instrucción lógica
require 'conexion.php'; 

// Obtener el JSON enviado por JS
$json = file_get_contents("php://input");
$data = json_decode($json, true);

if ($data) {
    // Validar que existan los campos mínimos
    if (!isset($data['fecha']) || !isset($data['hora']) || !isset($data['personas'])) {
        echo json_encode(["success" => false, "message" => "Datos incompletos."]);
        exit;
    }

    $fecha = $data['fecha'];
    $hora = $data['hora'];
    $personas = $data['personas'];
    
    // --- LÓGICA DE AFORO ---
    $limite = 30; // Capacidad máxima por turno
    
    // Consulta preparada para evitar inyecciones SQL
    $check = $conn->prepare("SELECT SUM(personas) as total FROM reservas WHERE fecha = ? AND hora = ? AND estado != 'Rechazado' AND estado != 'Expirado'");
    $check->bind_param("ss", $fecha, $hora);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();
    
    // Si es null (primera reserva), ponemos 0
    $ocupados = $row['total'] ? intval($row['total']) : 0;
    $check->close();

    // Validar capacidad
    if (($ocupados + $personas) > $limite) {
        $disponibles = $limite - $ocupados;
        echo json_encode(["success" => false, "message" => "Lo sentimos, a las $hora solo quedan $disponibles cupos disponibles."]);
        exit;
    }

    // --- GUARDAR RESERVA ---
    $stmt = $conn->prepare("INSERT INTO reservas (nombres, apellidos, dni, edad, email, telefono, codigo_operacion, personas, fecha, hora) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssisssiss", 
        $data['cliente']['nombre'], 
        $data['cliente']['apellido'], 
        $data['cliente']['dni'], 
        $data['cliente']['edad'], 
        $data['cliente']['email'], 
        $data['cliente']['telefono'],
        $data['cliente']['codigo_operacion'],
        $data['personas'], 
        $data['fecha'], 
        $data['hora']
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "¡Reserva recibida! Validaremos tu pago en breve."]);
    } else {
        // Enviar error SQL (útil para debug, en prod podrías poner un mensaje genérico)
        echo json_encode(["success" => false, "message" => "Error al guardar: " . $stmt->error]);
    }
    $stmt->close();

} else {
    // CRÍTICO: Si $data es null (el JSON llegó mal o vacío), responder JSON de error
    // Esto evita el "Unexpected end of JSON input" en el JS
    echo json_encode(["success" => false, "message" => "No se recibieron datos válidos."]);
}

$conn->close();
?>