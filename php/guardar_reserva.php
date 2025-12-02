<?php
header('Content-Type: application/json');
require 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $fecha = $data['fecha'];
    $hora = $data['hora'];
    $personas = $data['personas'];
    
    // --- LÓGICA DE AFORO ---
    // Limite: 30 personas por bloque horario
    $limite = 30; 
    
    $check = $conn->prepare("SELECT SUM(personas) as total FROM reservas WHERE fecha = ? AND hora = ? AND estado != 'Rechazado' AND estado != 'Expirado'");
    $check->bind_param("ss", $fecha, $hora);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();
    $ocupados = $row['total'] ? $row['total'] : 0;
    $check->close();

    if (($ocupados + $personas) > $limite) {
        echo json_encode(["success" => false, "message" => "Lo sentimos, para las $hora solo nos quedan " . ($limite - $ocupados) . " asientos disponibles."]);
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
        echo json_encode(["success" => true, "message" => "¡Reserva recibida! Estamos validando tu pago."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error SQL: " . $stmt->error]);
    }
    $stmt->close();
}
$conn->close();
?>