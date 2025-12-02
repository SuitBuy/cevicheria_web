<?php
session_start();

// CREDENCIALES DE ACCESO (Cámbialas si deseas)
$usuario_correcto = "admin";
$password_correcto = "admin123";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['usuario'];
    $pass = $_POST['password'];

    if ($user === $usuario_correcto && $pass === $password_correcto) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Rinconcito Marino</title>
    <link rel="stylesheet" href="../css/style.css"> 
    <style>
        body { background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .login-card img { width: 150px; margin-bottom: 20px; }
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group label { display: block; margin-bottom: 5px; color: #333; font-weight: bold; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn-login { background: #0E3C5E; color: white; border: none; padding: 10px 20px; border-radius: 5px; width: 100%; cursor: pointer; font-size: 1rem; }
        .btn-login:hover { background: #0a2a42; }
        .error { color: red; margin-bottom: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <img src="../assets/logo2.png" alt="Logo">
        <h2>Panel Administrativo</h2>
        <?php if($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <label>Usuario</label>
                <input type="text" name="usuario" required>
            </div>
            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
        <br>
        <a href="../index.html" style="color: #666; text-decoration: none; font-size: 0.9rem;">← Volver al inicio</a>
    </div>
</body>
</html>