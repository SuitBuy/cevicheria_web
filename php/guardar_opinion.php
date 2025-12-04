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

    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Procesando...</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>';

    if ($stmt->execute()) {
        // Modal de Éxito
        echo "<script>
            Swal.fire({
                title: '¡Gracias por tu opinión!',
                text: 'Hemos recibido tu comentario correctamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../index.html';
                }
            });
        </script>";
    } else {
        // Modal de Error
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'Hubo un error al guardar tu opinión.',
                icon: 'error',
                confirmButtonText: 'Intentar de nuevo'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
    
    echo '</body></html>';

    $stmt->close();
}
$conn->close();
?>