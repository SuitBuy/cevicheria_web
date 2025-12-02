<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
require 'conexion.php'; // Asegúrate de que este archivo exista en la misma carpeta

// --- LÓGICA DE EXPIRACIÓN AUTOMÁTICA ---
// Si pasaron 30 min y sigue Pendiente -> Expirado
$conn->query("UPDATE reservas SET estado = 'Expirado' WHERE estado = 'Pendiente' AND fecha_registro < (NOW() - INTERVAL 30 MINUTE)");

// --- ACCIONES DE BOTONES (Confirmar/Rechazar) ---
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'];
    
    if ($accion == 'confirmar') $conn->query("UPDATE reservas SET estado = 'Confirmado' WHERE id = $id");
    if ($accion == 'rechazar') $conn->query("UPDATE reservas SET estado = 'Rechazado' WHERE id = $id");
    if ($accion == 'eliminar') $conn->query("DELETE FROM reservas WHERE id = $id");
    
    // Redireccionar para limpiar la URL
    header("Location: admin.php"); 
    exit;
}

// --- LÓGICA DE BÚSQUEDA ---
$where_clause = "1=1"; // Por defecto trae todo
$search_term = "";

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_term = $conn->real_escape_string($_GET['q']);
    // Busca coincidencias en nombre, apellido, DNI, teléfono o código de operación
    $where_clause = "(nombres LIKE '%$search_term%' OR apellidos LIKE '%$search_term%' OR dni LIKE '%$search_term%' OR telefono LIKE '%$search_term%' OR codigo_operacion LIKE '%$search_term%')";
}

// --- CONSULTA PRINCIPAL ---
// Orden: Pendientes primero -> Luego por Fecha más reciente -> Luego por Hora
$sql = "SELECT *, (fecha_registro < (NOW() - INTERVAL 30 MINUTE)) as vencido 
        FROM reservas 
        WHERE $where_clause 
        ORDER BY FIELD(estado, 'Pendiente', 'Confirmado', 'Rechazado', 'Expirado'), fecha DESC, hora ASC";
$reservas = $conn->query($sql);

// --- ESTADÍSTICAS RÁPIDAS (Contadores) ---
$hoy = date('Y-m-d');
$stats_pendientes = $conn->query("SELECT COUNT(*) as c FROM reservas WHERE estado='Pendiente'")->fetch_assoc()['c'];
$stats_hoy = $conn->query("SELECT COUNT(*) as c FROM reservas WHERE fecha='$hoy' AND estado='Confirmado'")->fetch_assoc()['c'];
$stats_total_hoy = $conn->query("SELECT SUM(personas) as p FROM reservas WHERE fecha='$hoy' AND estado='Confirmado'")->fetch_assoc()['p'];
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
            --secondary: #d4a373;
            --bg: #f3f4f6;
            --white: #ffffff;
            --text: #1f2937;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray: #6b7280;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { background-color: var(--bg); color: var(--text); min-height: 100vh; }

        /* HEADER */
        header {
            background: var(--primary);
            color: var(--white);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .brand { display: flex; align-items: center; gap: 15px; font-weight: 600; font-size: 1.2rem; }
        .brand img { height: 40px; }
        
        .logout-btn {
            background: rgba(255,255,255,0.1);
            color: var(--white);
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .logout-btn:hover { background: var(--danger); }

        /* CONTENEDOR PRINCIPAL */
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }

        /* TARJETAS DE ESTADÍSTICAS */
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 5px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card h3 { font-size: 2rem; color: var(--primary); margin-bottom: 5px; }
        .stat-card p { color: var(--gray); font-size: 0.9rem; }
        .stat-icon { font-size: 2.5rem; opacity: 0.2; color: var(--primary); }

        /* BARRA DE BÚSQUEDA */
        .search-bar-container {
            background: var(--white);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .search-form { display: flex; gap: 10px; flex: 1; min-width: 300px; }
        
        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }
        .search-input:focus { border-color: var(--primary); }
        
        .search-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.3s;
        }
        .search-btn:hover { background: #0a2a42; }

        .btn-reset {
            text-decoration: none;
            color: var(--gray);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 10px;
        }
        .btn-reset:hover { color: var(--text); }

        /* TABLA */
        .table-container {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        
        thead { background: var(--primary); color: var(--white); }
        th { padding: 15px 20px; text-align: left; font-weight: 500; font-size: 0.95rem; }
        
        td { padding: 15px 20px; border-bottom: 1px solid #f3f4f6; color: var(--text); vertical-align: middle; }
        tr:hover { background: #f9fafb; }

        /* ETIQUETAS DE ESTADO */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-pendiente { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .badge-confirmado { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
        .badge-rechazado { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        .badge-expirado { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }

        /* BOTONES DE ACCIÓN */
        .actions { display: flex; gap: 8px; }
        .btn-icon {
            width: 32px; height: 32px;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none;
            transition: 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-wsp { background: #dcfce7; color: #166534; }
        .btn-ok { background: #dbeafe; color: #1e40af; }
        .btn-no { background: #fee2e2; color: #991b1b; }
        .btn-del { background: #f3f4f6; color: #6b7280; }

        .btn-icon:hover { transform: translateY(-2px); filter: brightness(0.95); }

        /* Responsive móvil */
        @media (max-width: 768px) {
            header { padding: 15px 20px; }
            .brand span { display: none; }
            .search-form { flex-direction: column; }
            .search-btn { padding: 12px; }
        }
    </style>
</head>
<body>

<header>
        <div class="brand">
            <img src="../assets/logo2.png" alt="Rinconcito Marino">
            <span>Panel <?php echo ucfirst($_SESSION['rol']); ?></span>
        </div>
        <div style="display:flex; gap:15px; align-items:center;">
            <nav style="display:flex; gap:10px;">
                <a href="admin.php" class="nav-link active">Reservas</a>
                
                <?php if($_SESSION['rol'] == 'admin'): ?>
                    <a href="usuarios.php" class="nav-link" style="background:rgba(255,255,255,0.2);">Usuarios</a>
                <?php endif; ?>
            </nav>

            <span style="border-left:1px solid rgba(255,255,255,0.3); padding-left:15px; font-size: 0.9rem;">
                <?php echo $_SESSION['user_name']; ?>
            </span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>

    <div class="container">
        
        <div class="stats-grid">
            <div class="stat-card" style="border-color: var(--warning);">
                <div>
                    <h3><?php echo $stats_pendientes; ?></h3>
                    <p>Pendientes de Revisión</p>
                </div>
                <i class="fas fa-clock stat-icon" style="color: var(--warning);"></i>
            </div>
            
            <div class="stat-card" style="border-color: var(--success);">
                <div>
                    <h3><?php echo $stats_hoy; ?></h3>
                    <p>Mesas Confirmadas Hoy</p>
                </div>
                <i class="fas fa-check-circle stat-icon" style="color: var(--success);"></i>
            </div>

            <div class="stat-card" style="border-color: var(--primary);">
                <div>
                    <h3><?php echo $stats_total_hoy ? $stats_total_hoy : 0; ?></h3>
                    <p>Total Personas Hoy</p>
                </div>
                <i class="fas fa-users stat-icon"></i>
            </div>
        </div>

        <div class="search-bar-container">
            <h3 style="color: var(--primary); min-width: 150px;">Gestión de Reservas</h3>
            
            <form class="search-form" method="GET">
                <input type="text" name="q" class="search-input" 
                       placeholder="Buscar por Nombre, DNI, Teléfono o Código..." 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i> Buscar</button>
            </form>

            <?php if(!empty($search_term)): ?>
                <a href="admin.php" class="btn-reset"><i class="fas fa-times"></i> Limpiar filtro</a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th>Pax</th>
                        <th>Pago (Yape/Plin)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reservas->num_rows > 0): ?>
                        <?php while($row = $reservas->fetch_assoc()): ?>
                        <tr style="<?php echo ($row['estado']=='Expirado') ? 'opacity:0.5; background:#f9f9f9;' : ''; ?>">
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
                                <div style="font-size:0.8rem; color: var(--gray);"><?php echo $row['email']; ?></div>
                            </td>
                            <td style="text-align:center; font-weight:bold; font-size:1.1rem;">
                                <?php echo $row['personas']; ?>
                            </td>
                            <td>
                                <div style="font-family: monospace; background: #eef2ff; padding: 4px 8px; border-radius: 4px; display: inline-block; border: 1px dashed #6366f1; color: #4338ca;">
                                    <?php echo $row['codigo_operacion']; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($row['estado']); ?>">
                                    <?php echo $row['estado']; ?>
                                </span>
                                <?php if($row['estado']=='Pendiente'): ?>
                                    <div style="font-size:0.7rem; margin-top:5px; color:#ef4444;">
                                        Registrado: <?php echo date('H:i', strtotime($row['fecha_registro'])); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="https://wa.me/51<?php echo $row['telefono']; ?>?text=Hola <?php echo $row['nombres']; ?>, te escribimos de Rinconcito Marino sobre tu reserva..." 
                                       target="_blank" class="btn-icon btn-wsp" title="Contactar por WhatsApp">
                                       <i class="fab fa-whatsapp"></i>
                                    </a>

                                    <?php if($row['estado'] == 'Pendiente'): ?>
                                        <a href="admin.php?accion=confirmar&id=<?php echo $row['id']; ?>" 
                                           class="btn-icon btn-ok" title="Confirmar Pago" 
                                           onclick="return confirm('¿Confirmar que el pago es real?')">
                                           <i class="fas fa-check"></i>
                                        </a>
                                        <a href="admin.php?accion=rechazar&id=<?php echo $row['id']; ?>" 
                                           class="btn-icon btn-no" title="Rechazar Reserva" 
                                           onclick="return confirm('¿Rechazar esta reserva?')">
                                           <i class="fas fa-times"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="admin.php?accion=eliminar&id=<?php echo $row['id']; ?>" 
                                           class="btn-icon btn-del" title="Eliminar Registro" 
                                           onclick="return confirm('¿Borrar permanentemente?')">
                                           <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 40px; color: var(--gray);">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 10px; opacity: 0.3;"></i>
                                <p>No se encontraron reservas con esos criterios.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>