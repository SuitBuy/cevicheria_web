<?php
session_start();
require 'conexion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $password = $_POST['password'];

    $sql = "SELECT id, usuario, password, rol FROM usuarios_admin WHERE usuario = '$usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = true; 
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['usuario'];
            $_SESSION['rol'] = $row['rol']; 

            header("Location: admin.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - Rinconcito Marino</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card img {
            height: 80px;
            margin-bottom: 20px;
        }

        h2 {
            color: #0E3C5E;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus {
            border-color: #0E3C5E;
            outline: none;
        }

        .btn-login {
            background: #0E3C5E;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-size: 1rem;
        }

        .btn-login:hover {
            background: #0a2a42;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 6px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: #0E3C5E;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <img src="../assets/logo.png" alt="Logo">
        <h2>Acceso Personal</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Usuario</label>
                <input type="text" name="usuario" placeholder="Ej. admin" required>
            </div>
            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
        <a href="../index.html" class="back-link">← Volver a la web</a>
    </div>
</body>

</html>