<?php
// 1. SILENCIAR ERRORES HTML DE PHP
// Esto es vital para que el "Warning" de PHP no rompa el JSON
error_reporting(0); 
ini_set('display_errors', 0);

// 2. INDICAR QUE LA RESPUESTA SIEMPRE ES JSON
header('Content-Type: application/json; charset=utf-8');

// Credenciales de Railway
$host = "shuttle.proxy.rlwy.net"; 
$usuario = "root";   
$password = "mypyjIjXfTNKjoaxvrxpNBtDebvMWjDb";       
$base_datos = "rinconcito_marino"; 
$puerto = 29841; 

// 3. INTENTAR CONECTAR CON CONTROL DE EXCEPCIONES
try {
    // Usamos '@' para suprimir warnings nativos de mysqli
    $conn = @new mysqli($host, $usuario, $password, $base_datos, $puerto);

    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");

} catch (Exception $e) {
    // Si algo falla, devolvemos un JSON válido con el error
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit; // Detenemos todo aquí
}
?>