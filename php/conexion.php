<?php
error_reporting(0); 
ini_set('display_errors', 0);


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
    die("<script>alert('Error de conexión a la Base de Datos: " . addslashes($e->getMessage()) . "'); window.history.back();</script>");
}
?>