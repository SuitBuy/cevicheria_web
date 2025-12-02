<?php
session_start();
require 'conexion.php';

// SEGURIDAD: Solo admin puede entrar aquí
if (!isset($_SESSION['admin']) || $_SESSION['rol'] != 'admin') {
    header("Location: admin.php"); // Si es empleado, lo devuelve a reservas
    exit;
}

$mensaje = "";
$usuario_editar = null;

// --- CREAR O EDITAR USUARIO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $rol = $_POST['rol'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    
    // Si hay contraseña, la encriptamos. Si no, es null (para edición sin cambio de pass)
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($id) {
        // ACTUALIZAR
        if ($password) {
            $sql = "UPDATE usuarios_admin SET usuario='$usuario', password='$password', rol='$rol' WHERE id=$id";
        } else {
            $sql = "UPDATE usuarios_admin SET usuario='$usuario', rol='$rol' WHERE id=$id";
        }
        $conn->query($sql);
        $mensaje = "Usuario actualizado correctamente.";
    } else {
        // CREAR NUEVO
        if ($password) {
            $sql = "INSERT INTO usuarios_admin (usuario, password, rol) VALUES ('$usuario', '$password', '$rol')";
            if($conn->query($sql)) $mensaje = "Usuario creado correctamente.";
            else $mensaje = "Error: El usuario ya existe.";
        } else {
            $mensaje = "La contraseña es obligatoria para nuevos usuarios.";
        }
    }
}

// --- ELIMINAR USUARIO ---
if (isset($_GET['borrar'])) {
    $id = intval($_GET['borrar']);
    if ($id != $_SESSION['user_id']) { // Evitar auto-eliminarse
        $conn->query("DELETE FROM usuarios_admin WHERE id=$id");
        header("Location: usuarios.php");
        exit;
    } else {
        $mensaje = "No puedes eliminar tu propia cuenta.";
    }
}

// --- CARGAR DATOS PARA EDITAR ---
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $usuario_editar = $conn->query("SELECT * FROM usuarios_admin WHERE id=$id")->fetch_assoc();
}

// LISTAR USUARIOS
$lista_usuarios = $conn->query("SELECT * FROM usuarios_admin ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Usuarios - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0E3C5E; --bg: #f3f4f6; --white: #ffffff; --text: #1f2937; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg); color: var(--text); }
        
        /* HEADER (Igual al admin) */
        header { background: var(--primary); color: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .nav-link { color: white; text-decoration: none; padding: 8px 15px; border-radius: 6px; font-size: 0.9rem; opacity: 0.8; }
        .nav-link.active, .nav-link:hover { background: rgba(255,255,255,0.2); opacity: 1; }
        
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        
        .card { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        h3 { color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px; }
        
        /* FORMULARIO */
        label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 0.9rem; }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #d1d5db; border-radius: 6px; }
        .btn-submit { background: var(--primary); color: white; border: none; padding: 10px; border-radius: 6px; width: 100%; cursor: pointer; font-weight: 600; }
        .btn-submit:hover { background: #0a2a42; }
        .btn-cancel { display:block; text-align:center; margin-top:10px; text-decoration:none; color:#666; font-size:0.9rem; }

        /* TABLA */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; background: #f8fafc; color: #64748b; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .bg-admin { background: #dbeafe; color: #1e40af; }
        .bg-empleado { background: #f3f4f6; color: #4b5563; }
        
        .action-btn { text-decoration: none; padding: 5px 8px; border-radius: 4px; font-size: 0.9rem; }
        .edit { color: #2563eb; background: #eff6ff; }
        .del { color: #dc2626; background: #fef2f2; }
        
        @media (max-width: 768px) { .container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<header>
    <div style="font-weight:bold; font-size:1.2rem;">Panel Admin</div>
    <nav>
        <a href="admin.php" class="nav-link">Reservas</a>
        <a href="usuarios.php" class="nav-link active">Usuarios</a>
    </nav>
    <a href="logout.php" style="color:white;"><i class="fas fa-sign-out-alt"></i></a>
</header>

<div class="container">
    
    <div class="card">
        <h3><?php echo $usuario_editar ? 'Editar Usuario' : 'Nuevo Usuario'; ?></h3>
        
        <?php if($mensaje): ?>
            <div style="background:#ecfdf5; color:#065f46; padding:10px; border-radius:6px; margin-bottom:15px; font-size:0.9rem;">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <?php if($usuario_editar): ?>
                <input type="hidden" name="id" value="<?php echo $usuario_editar['id']; ?>">
            <?php endif; ?>

            <label>Nombre de Usuario</label>
            <input type="text" name="usuario" required value="<?php echo $usuario_editar ? $usuario_editar['usuario'] : ''; ?>">

            <label>Contraseña</label>
            <input type="password" name="password" placeholder="<?php echo $usuario_editar ? 'Dejar vacío para mantener actual' : 'Crear contraseña'; ?>" <?php echo $usuario_editar ? '' : 'required'; ?>>

            <label>Rol de Acceso</label>
            <select name="rol">
                <option value="empleado" <?php echo ($usuario_editar && $usuario_editar['rol']=='empleado') ? 'selected' : ''; ?>>Empleado (Solo Reservas)</option>
                <option value="admin" <?php echo ($usuario_editar && $usuario_editar['rol']=='admin') ? 'selected' : ''; ?>>Admin (Total)</option>
            </select>

            <button type="submit" class="btn-submit"><?php echo $usuario_editar ? 'Actualizar' : 'Crear Usuario'; ?></button>
            
            <?php if($usuario_editar): ?>
                <a href="usuarios.php" class="btn-cancel">Cancelar edición</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>Equipo Registrado</h3>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($u = $lista_usuarios->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight:500;"><?php echo $u['usuario']; ?></td>
                    <td>
                        <span class="badge <?php echo $u['rol']=='admin' ? 'bg-admin' : 'bg-empleado'; ?>">
                            <?php echo $u['rol']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="usuarios.php?editar=<?php echo $u['id']; ?>" class="action-btn edit" title="Editar"><i class="fas fa-pen"></i></a>
                        <?php if($u['id'] != $_SESSION['user_id']): // No borrarse a sí mismo ?>
                            <a href="usuarios.php?borrar=<?php echo $u['id']; ?>" class="action-btn del" onclick="return confirm('¿Eliminar a este usuario?')" title="Eliminar"><i class="fas fa-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>