<?php
header('Content-Type: application/json');
require 'conexion.php';

// Si recibimos datos por POST normal (formulario)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres = $_POST['nombres'];
    $correo = $_POST['correo'];
    $comentario = $_POST['comentario'];

    $stmt = $conn->prepare("INSERT INTO opiniones (nombres, correo, comentario) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombres, $correo, $comentario);

    if ($stmt->execute()) {
        // Redirigir de vuelta al index con éxito
        echo "<script>alert('¡Gracias por tu opinión!'); window.location.href='../index.html';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>