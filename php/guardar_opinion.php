<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres = $_POST['nombres'];
    $correo = $_POST['correo'];
    $comentario = $_POST['comentario'];

    $stmt = $conn->prepare("INSERT INTO opiniones (nombres, correo, comentario) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombres, $correo, $comentario);

    if ($stmt->execute()) {
        echo "<script>
                alert('¡Gracias por tu opinión!'); 
                window.location.href='../index.html';
              </script>";
    } else {
        echo "<script>
                alert('Hubo un error al guardar tu opinión.'); 
                window.history.back();
              </script>";
    }
    $stmt->close();
}
$conn->close();
?>