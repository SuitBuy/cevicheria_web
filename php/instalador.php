<?php
// instalador.php - Ejecutar una vez y borrar
require 'conexion.php';

// Configuración del nuevo admin
$usuario = "admin";
$clave_normal = "admin123";
$rol = "admin";

// 1. Encriptamos la contraseña
$password_hash = password_hash($clave_normal, PASSWORD_DEFAULT);

// 2. Preparamos la consulta para insertar
// Usamos INSERT IGNORE o comprobamos si existe para no duplicar
$check = $conn->query("SELECT id FROM usuarios_admin WHERE usuario = '$usuario'");

if ($check->num_rows > 0) {
    // Si ya existe, lo actualizamos para asegurarnos que tenga la clave correcta
    $sql = "UPDATE usuarios_admin SET password='$password_hash', rol='$rol' WHERE usuario='$usuario'";
    if ($conn->query($sql)) {
        echo "<h1>¡Usuario Actualizado!</h1>";
        echo "<p>El usuario <b>$usuario</b> ya existía. Se ha restablecido su contraseña a: <b>$clave_normal</b></p>";
    } else {
        echo "Error actualizando: " . $conn->error;
    }
} else {
    // Si no existe, lo creamos
    $sql = "INSERT INTO usuarios_admin (usuario, password, rol) VALUES ('$usuario', '$password_hash', '$rol')";
    if ($conn->query($sql)) {
        echo "<h1>¡Éxito! Usuario Creado</h1>";
        echo "<p>Se ha creado el administrador correctamente.</p>";
        echo "<ul>";
        echo "<li>Usuario: <b>$usuario</b></li>";
        echo "<li>Contraseña: <b>$clave_normal</b></li>";
        echo "</ul>";
        echo "<p><a href='login.php'>Ir al Login</a></p>";
    } else {
        echo "Error creando usuario: " . $conn->error;
    }
}

// Opcional: Crear tabla si no existe (por seguridad)
$sql_tabla = "CREATE TABLE IF NOT EXISTS usuarios_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','empleado') DEFAULT 'admin'
)";
$conn->query($sql_tabla);
?>