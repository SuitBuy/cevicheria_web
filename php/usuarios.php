<?php
session_start();
require 'conexion.php';

// SEGURIDAD: Solo admin puede entrar aquí
if (!isset($_SESSION['admin']) || $_SESSION['rol'] != 'admin') {
    header("Location: admin.php");
    exit;
}

$mensaje = "";
$usuario_editar = null;

// --- LOGICA DE BASE DE DATOS (Igual que antes) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $rol = $_POST['rol'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($id) {
        if ($password) {
            $sql = "UPDATE usuarios_admin SET usuario='$usuario', password='$password', rol='$rol' WHERE id=$id";
        } else {
            $sql = "UPDATE usuarios_admin SET usuario='$usuario', rol='$rol' WHERE id=$id";
        }
        $conn->query($sql);
        $mensaje = "Usuario actualizado correctamente.";
    } else {
        if ($password) {
            $sql = "INSERT INTO usuarios_admin (usuario, password, rol) VALUES ('$usuario', '$password', '$rol')";
            if ($conn->query($sql)) $mensaje = "Usuario creado correctamente.";
            else $mensaje = "Error: El usuario ya existe.";
        } else {
            $mensaje = "La contraseña es obligatoria para nuevos usuarios.";
        }
    }
}

if (isset($_GET['borrar'])) {
    $id = intval($_GET['borrar']);
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM usuarios_admin WHERE id=$id");
        header("Location: usuarios.php");
        exit;
    } else {
        $mensaje = "No puedes eliminar tu propia cuenta.";
    }
}

if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $usuario_editar = $conn->query("SELECT * FROM usuarios_admin WHERE id=$id")->fetch_assoc();
}

$lista_usuarios = $conn->query("SELECT * FROM usuarios_admin ORDER BY id DESC");

// Variables para el header
$rol_usuario = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'admin';
$nombre_usuario = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión Usuarios - Rinconcito Marino</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- MISMOS ESTILOS QUE ADMIN.PHP --- */
        :root {
            --primary: #0E3C5E;
            --bg: #f3f4f6;
            --white: #ffffff;
            --text: #1f2937;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray: #6b7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* HEADER */
        header {
            background: var(--primary);
            color: var(--white);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .brand img {
            height: 40px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        nav {
            display: flex;
            gap: 10px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-info {
            border-left: 1px solid rgba(255, 255, 255, 0.2);
            padding-left: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.8);
            color: white;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #ef4444;
            transform: translateY(-2px);
        }

        /* LAYOUT ESPECÍFICO DE USUARIOS (Grid 2 columnas) */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .card {
            background: var(--white);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            color: var(--primary);
            margin-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 10px;
            font-size: 1.2rem;
        }

        /* FORMULARIOS ESTILIZADOS */
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text);
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        input:focus,
        select:focus {
            border-color: var(--primary);
        }

        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #0a2a42;
            transform: translateY(-2px);
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: var(--gray);
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .btn-cancel:hover {
            color: var(--danger);
        }

        /* TABLA ESTILIZADA */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--primary);
            color: var(--white);
        }

        th {
            text-align: left;
            padding: 15px 20px;
            font-weight: 500;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        tr:hover {
            background: #f9fafb;
        }

        /* BADGES */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .bg-admin {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .bg-empleado {
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        /* BOTONES DE ACCIÓN */
        .actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: 0.2s;
            border: none;
        }

        .edit {
            background: #eff6ff;
            color: #2563eb;
        }

        .edit:hover {
            background: #2563eb;
            color: white;
        }

        .del {
            background: #fef2f2;
            color: #dc2626;
        }

        .del:hover {
            background: #dc2626;
            color: white;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }

            header {
                flex-direction: column;
                gap: 15px;
                padding: 15px 20px;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .brand span {
                display: block;
            }

            .user-info {
                display: none;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="brand">
            <img src="../assets/logo2.png" alt="Rinconcito Marino">
            <span>Panel <?php echo ucfirst($rol_usuario); ?></span>
        </div>

        <div class="header-right">
            <nav>
                <a href="admin.php" class="nav-link">
                    <i class="fas fa-clipboard-list"></i> Reservas
                </a>
                <a href="usuarios.php" class="nav-link active">
                    <i class="fas fa-users-cog"></i> Usuarios
                </a>
            </nav>

            <div class="user-info">
                <span><?php echo htmlspecialchars($nombre_usuario); ?></span>
                <a href="logout.php" class="logout-btn" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <div class="container">

        <div class="card">
            <h3>
                <i class="fas <?php echo $usuario_editar ? 'fa-user-edit' : 'fa-user-plus'; ?>"></i>
                <?php echo $usuario_editar ? 'Editar Usuario' : 'Nuevo Usuario'; ?>
            </h3>

            <?php if ($mensaje): ?>
                <div style="background:#ecfdf5; color:#047857; padding:12px; border-radius:8px; margin-bottom:20px; font-size:0.9rem; border: 1px solid #d1fae5;">
                    <i class="fas fa-check-circle"></i> <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <?php if ($usuario_editar): ?>
                    <input type="hidden" name="id" value="<?php echo $usuario_editar['id']; ?>">
                <?php endif; ?>

                <label>Nombre de Usuario</label>
                <input type="text" name="usuario" required placeholder="Ej. juan.perez" value="<?php echo $usuario_editar ? $usuario_editar['usuario'] : ''; ?>">

                <label>Contraseña</label>
                <input type="password" name="password" placeholder="<?php echo $usuario_editar ? 'Dejar vacío para mantener actual' : 'Crear contraseña segura'; ?>" <?php echo $usuario_editar ? '' : 'required'; ?>>

                <label>Rol de Acceso</label>
                <select name="rol">
                    <option value="empleado" <?php echo ($usuario_editar && $usuario_editar['rol'] == 'empleado') ? 'selected' : ''; ?>>Empleado (Solo Reservas)</option>
                    <option value="admin" <?php echo ($usuario_editar && $usuario_editar['rol'] == 'admin') ? 'selected' : ''; ?>>Admin (Acceso Total)</option>
                </select>

                <button type="submit" class="btn-submit">
                    <?php echo $usuario_editar ? 'Guardar Cambios' : 'Crear Usuario'; ?>
                </button>

                <?php if ($usuario_editar): ?>
                    <a href="usuarios.php" class="btn-cancel">Cancelar edición</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h3><i class="fas fa-users"></i> Equipo Registrado</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol Asignado</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = $lista_usuarios->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight:600; color: var(--primary);">
                                    <i class="fas fa-user-circle" style="color: var(--gray); margin-right:8px;"></i>
                                    <?php echo $u['usuario']; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $u['rol'] == 'admin' ? 'bg-admin' : 'bg-empleado'; ?>">
                                        <?php echo ucfirst($u['rol']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions" style="justify-content: flex-end;">
                                        <a href="usuarios.php?editar=<?php echo $u['id']; ?>" class="action-btn edit" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <a href="usuarios.php?borrar=<?php echo $u['id']; ?>" class="action-btn del" onclick="return confirm('¿Estás seguro de eliminar a este usuario?')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>

</html>