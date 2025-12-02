<?php
// 1. SILENCIAR ERRORES HTML DE PHP (Opcional, pero recomendado en producción)
error_reporting(0); 
ini_set('display_errors', 0);

// ELIMINAMOS EL HEADER JSON para permitir respuestas HTML/JS
// header('Content-Type: application/json; charset=utf-8'); <--- ELIMINADO

// Credenciales de Railway
$host = "shuttle.proxy.rlwy.net"; 
$usuario = "root";   
$password = "mypyjIjXfTNKjoaxvrxpNBtDebvMWjDb";       
$base_datos = "rinconcito_marino"; 
$puerto = 29841; 

// 3. INTENTAR CONECTAR
try {
    $conn = @new mysqli($host, $usuario, $password, $base_datos, $puerto);

    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");

} catch (Exception $e) {
    // En lugar de JSON, mostramos un error visual y detenemos
    die("<script>alert('Error de conexión a la Base de Datos: " . addslashes($e->getMessage()) . "'); window.history.back();</script>");
}
?>