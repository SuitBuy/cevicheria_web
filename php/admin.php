<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require 'conexion.php';

// --- VARIABLES DE NAVEGACIÓN ---
$view = isset($_GET['view']) ? $_GET['view'] : 'reservas'; // Por defecto reservas

// --- LÓGICA DE EXPIRACIÓN AUTOMÁTICA (Solo para reservas) ---
$conn->query("UPDATE reservas SET estado = 'Expirado' WHERE estado = 'Pendiente' AND fecha_registro < (NOW() - INTERVAL 30 MINUTE)");

// --- ACCIONES DE BOTONES ---
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'];

    // Acciones Reservas
    if ($accion == 'confirmar') $conn->query("UPDATE reservas SET estado = 'Confirmado' WHERE id = $id");
    if ($accion == 'rechazar') $conn->query("UPDATE reservas SET estado = 'Rechazado' WHERE id = $id");
    if ($accion == 'eliminar') $conn->query("DELETE FROM reservas WHERE id = $id");

    // Acciones Opiniones
    if ($accion == 'eliminar_opinion') {
        $conn->query("DELETE FROM opiniones WHERE id = $id");
        header("Location: admin.php?view=opiniones"); // Mantenerse en la vista opiniones
        exit;
    }

    // Redirección general (para reservas)
    if ($accion != 'eliminar_opinion') {
        header("Location: admin.php?view=reservas");
        exit;
    }
}

// --- BÚSQUEDA ---
$search_term = "";
$where_clause = "1=1";

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_term = $conn->real_escape_string($_GET['q']);
    if ($view == 'reservas') {
        $where_clause = "(nombres LIKE '%$search_term%' OR apellidos LIKE '%$search_term%' OR dni LIKE '%$search_term%' OR telefono LIKE '%$search_term%')";
    } else {
        $where_clause = "(nombres LIKE '%$search_term%' OR correo LIKE '%$search_term%' OR comentario LIKE '%$search_term%')";
    }
}

// --- CONSULTAS SEGÚN LA VISTA ---
if ($view == 'reservas') {
    $sql = "SELECT *, (fecha_registro < (NOW() - INTERVAL 30 MINUTE)) as vencido 
            FROM reservas 
            WHERE $where_clause 
            ORDER BY FIELD(estado, 'Pendiente', 'Confirmado', 'Rechazado', 'Expirado'), fecha DESC, hora ASC";
    $datos = $conn->query($sql);
} else {
    // Vista Opiniones
    $sql = "SELECT * FROM opiniones WHERE $where_clause ORDER BY id DESC";
    $datos = $conn->query($sql);
}

// --- DATOS SESIÓN ---
$rol_usuario = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'admin';
$nombre_empleado = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';

// --- ESTADÍSTICAS RÁPIDAS ---
$hoy = date('Y-m-d');
$stats_pendientes = $conn->query("SELECT COUNT(*) as c FROM reservas WHERE estado='Pendiente'")->fetch_assoc()['c'];
$stats_hoy = $conn->query("SELECT COUNT(*) as c FROM reservas WHERE fecha='$hoy' AND estado='Confirmado'")->fetch_assoc()['c'];
$stats_opiniones = $conn->query("SELECT COUNT(*) as c FROM opiniones")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Rinconcito Marino</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
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

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-left: 5px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card h3 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.2;
            color: var(--primary);
        }

        .search-bar-container {
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .search-form {
            display: flex;
            gap: 10px;
            flex: 1;
            min-width: 300px;
        }

        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        .search-input:focus {
            border-color: var(--primary);
        }

        .search-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-reset {
            text-decoration: none;
            color: var(--gray);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 10px;
        }

        .table-container {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        thead {
            background: var(--primary);
            color: var(--white);
        }

        th {
            padding: 15px 20px;
            text-align: left;
            font-weight: 500;
            font-size: 0.95rem;
        }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid #f3f4f6;
            color: var(--text);
            vertical-align: middle;
        }

        tr:hover {
            background: #f9fafb;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-pendiente {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .badge-confirmado {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #d1fae5;
        }

        .badge-rechazado {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }

        .badge-expirado {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-wsp {
            background: #dcfce7;
            color: #166534;
        }

        .btn-ok {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-no {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-del {
            background: #f3f4f6;
            color: #6b7280;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            filter: brightness(0.95);
        }

        /* Estilo para el comentario largo */
        .comment-box {
            max-width: 400px;
            font-size: 0.9rem;
            color: #4b5563;
            line-height: 1.4;
            background: #f9fafb;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
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
                <a href="admin.php?view=reservas" class="nav-link <?php echo $view == 'reservas' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> Reservas
                </a>
                <a href="admin.php?view=opiniones" class="nav-link <?php echo $view == 'opiniones' ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i> Opiniones
                </a>
                <?php if ($rol_usuario == 'admin'): ?>
                    <a href="usuarios.php" class="nav-link">
                        <i class="fas fa-users-cog"></i> Usuarios
                    </a>
                <?php endif; ?>
            </nav>

            <div class="user-info">
                <span><?php echo htmlspecialchars($nombre_empleado); ?></span>
                <a href="logout.php" class="logout-btn" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <div class="container">

        <div class="stats-grid">
            <div class="stat-card" style="border-color: var(--warning);">
                <div>
                    <h3><?php echo $stats_pendientes; ?></h3>
                    <p>Pendientes Pago</p>
                </div>
                <i class="fas fa-clock stat-icon" style="color: var(--warning);"></i>
            </div>

            <div class="stat-card" style="border-color: var(--success);">
                <div>
                    <h3><?php echo $stats_hoy; ?></h3>
                    <p>Mesas Hoy</p>
                </div>
                <i class="fas fa-check-circle stat-icon" style="color: var(--success);"></i>
            </div>

            <div class="stat-card" style="border-color: var(--primary);">
                <div>
                    <h3><?php echo $stats_opiniones; ?></h3>
                    <p>Opiniones Total</p>
                </div>
                <i class="fas fa-comment-dots stat-icon"></i>
            </div>
        </div>

        <div class="search-bar-container">
            <h3 style="color: var(--primary); min-width: 200px;">
                <?php echo $view == 'reservas' ? 'Gestión de Reservas' : 'Buzón de Opiniones'; ?>
            </h3>

            <form class="search-form" method="GET">
                <input type="hidden" name="view" value="<?php echo $view; ?>">
                <input type="text" name="q" class="search-input"
                    placeholder="<?php echo $view == 'reservas' ? 'Buscar cliente, DNI...' : 'Buscar en opiniones...'; ?>"
                    value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i> Buscar</button>
            </form>

            <?php if (!empty($search_term)): ?>
                <a href="admin.php?view=<?php echo $view; ?>" class="btn-reset"><i class="fas fa-times"></i> Limpiar</a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <?php if ($view == 'reservas'): ?>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Cliente</th>
                            <th>Contacto</th>
                            <th>Pax</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Correo</th>
                            <th>Opinión</th>
                            <th>Acciones</th>
                        </tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php if ($datos->num_rows > 0): ?>
                        <?php while ($row = $datos->fetch_assoc()): ?>

                            <?php if ($view == 'reservas'): ?>
                                <tr style="<?php echo ($row['estado'] == 'Expirado') ? 'opacity:0.5; background:#f9f9f9;' : ''; ?>">
                                    <td>
                                        <div style="font-weight: 600; color: var(--primary);">
                                            <?php echo date('d/m/Y', strtotime($row['fecha'])); ?>
                                        </div>
                                        <div style="font-size: 0.9rem; color: var(--gray);">
                                            <i class="far fa-clock"></i> <?php echo $row['hora']; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo $row['nombres'] . " " . $row['apellidos']; ?></div>
                                        <div style="font-size: 0.85rem; color: var(--gray);">DNI: <?php echo $row['dni']; ?></div>
                                    </td>
                                    <td>
                                        <div><i class="fas fa-phone-alt" style="font-size:0.7rem;"></i> <?php echo $row['telefono']; ?></div>
                                    </td>
                                    <td style="text-align:center; font-weight:bold; font-size:1.1rem;">
                                        <?php echo $row['personas']; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($row['estado']); ?>">
                                            <?php echo $row['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <?php
                                            $mensaje_wsp = "Hola, soy $nombre_empleado. Me contacto con usted para confirmar el método de pago sobre la reserva de nuestro restaurante Rinconcito Marino (S/ 20.00).";
                                            $link_wsp = "https://wa.me/51" . $row['telefono'] . "?text=" . urlencode($mensaje_wsp);
                                            ?>

                                            <a href="<?php echo $link_wsp; ?>" target="_blank" class="btn-icon btn-wsp" title="Contactar por WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>

                                            <?php if ($row['estado'] == 'Pendiente'): ?>
                                                <a href="admin.php?accion=confirmar&id=<?php echo $row['id']; ?>&view=reservas"
                                                    class="btn-icon btn-ok" title="Confirmar Pago"
                                                    onclick="return confirm('¿El cliente ya pagó los 20 soles?')">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="admin.php?accion=rechazar&id=<?php echo $row['id']; ?>&view=reservas"
                                                    class="btn-icon btn-no" title="Rechazar Reserva"
                                                    onclick="return confirm('¿Rechazar esta reserva?')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="admin.php?accion=eliminar&id=<?php echo $row['id']; ?>&view=reservas"
                                                    class="btn-icon btn-del" title="Eliminar Registro"
                                                    onclick="return confirm('¿Borrar permanentemente?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                            <?php else: ?>
                                <tr>
                                    <td style="color:var(--gray);">#<?php echo $row['id']; ?></td>
                                    <td style="font-weight:600;"><?php echo $row['nombres']; ?></td>
                                    <td style="color:var(--primary);"><?php echo $row['correo']; ?></td>
                                    <td>
                                        <div class="comment-box">
                                            <?php echo nl2br(htmlspecialchars($row['comentario'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="admin.php?accion=eliminar_opinion&id=<?php echo $row['id']; ?>&view=opiniones"
                                            class="btn-icon btn-del" title="Eliminar Opinión"
                                            onclick="return confirm('¿Borrar esta opinión?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 40px; color: var(--gray);">
                                <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 10px; opacity: 0.3;"></i>
                                <p>No hay <?php echo $view; ?> registradas.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>