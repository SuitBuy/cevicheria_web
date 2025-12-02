<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
require 'conexion.php';

// --- LOGICA DE EXPIRACIÓN AUTOMÁTICA ---
// Si pasaron 30 min y sigue Pendiente -> Expirado
$conn->query("UPDATE reservas SET estado = 'Expirado' WHERE estado = 'Pendiente' AND fecha_registro < (NOW() - INTERVAL 30 MINUTE)");

// --- ACCIONES DE BOTONES ---
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'];
    
    if ($accion == 'confirmar') $conn->query("UPDATE reservas SET estado = 'Confirmado' WHERE id = $id");
    if ($accion == 'rechazar') $conn->query("UPDATE reservas SET estado = 'Rechazado' WHERE id = $id");
    
    header("Location: admin.php"); // Recargar limpio
    exit;
}

// Consultar reservas (Pendientes primero)
$sql = "SELECT *, (fecha_registro < (NOW() - INTERVAL 30 MINUTE)) as vencido FROM reservas ORDER BY FIELD(estado, 'Pendiente', 'Confirmado', 'Rechazado', 'Expirado'), fecha DESC, hora ASC";
$reservas = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Rinconcito Marino</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f4f6f8; padding-top: 100px; color: #333; }
        .container { max-width: 1300px; margin: 0 auto; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #0E3C5E; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; color: white; display: inline-block;}
        .bg-pendiente { background: #f39c12; }
        .bg-confirmado { background: #27ae60; }
        .bg-rechazado { background: #c0392b; }
        .bg-expirado { background: #7f8c8d; }

        .btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 5px; color: white; margin-right: 5px; text-decoration: none; transition: 0.2s; }
        .btn-wsp { background: #25D366; }
        .btn-ok { background: #0E3C5E; }
        .btn-no { background: #c0392b; }
        .btn-icon:hover { transform: scale(1.1); opacity: 0.9; }

        .logout-btn { float: right; background: #c0392b; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header class="header-fijo">
        <div class="logo"><img src="assets/logo.png" style="height: 60px;"></div>
        <nav>
            <ul style="display:flex; align-items:center;">
                <li style="color:white; margin-right:20px;">Hola, Admin</li>
                <li><a href="php/logout.php" class="logout-btn">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="card">
            <h2><i class="fas fa-clipboard-list"></i> Gestión de Reservas</h2>
            <p>Las reservas pendientes se expirarán automáticamente pasados los 30 minutos.</p>
            
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha / Hora</th>
                            <th>Cliente</th>
                            <th>Pax</th>
                            <th>Pago (Yape)</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $reservas->fetch_assoc()): ?>
                        <tr style="<?php echo ($row['estado']=='Expirado') ? 'opacity:0.6; background:#f9f9f9;' : ''; ?>">
                            <td>
                                <b><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></b><br>
                                <?php echo $row['hora']; ?>
                            </td>
                            <td>
                                <?php echo $row['nombres'] . " " . $row['apellidos']; ?><br>
                                <small>DNI: <?php echo $row['dni']; ?></small>
                            </td>
                            <td><?php echo $row['personas']; ?></td>
                            <td>
                                <code><?php echo $row['codigo_operacion']; ?></code>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo strtolower($row['estado']); ?>">
                                    <?php echo $row['estado']; ?>
                                </span>
                                <?php if($row['estado']=='Pendiente'): ?>
                                    <div style="font-size:10px; color:#666; margin-top:5px;">
                                        Reg: <?php echo date('H:i', strtotime($row['fecha_registro'])); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['estado'] == 'Pendiente'): ?>
                                    <a href="https://wa.me/51<?php echo $row['telefono']; ?>?text=Hola <?php echo $row['nombres']; ?>, verificando tu reserva en Rinconcito Marino. ¿Podrías enviarnos la captura de tu Yape?" target="_blank" class="btn-icon btn-wsp" title="Contactar"><i class="fab fa-whatsapp"></i></a>
                                    
                                    <a href="admin.php?accion=confirmar&id=<?php echo $row['id']; ?>" class="btn-icon btn-ok" title="Confirmar Pago" onclick="return confirm('¿Confirmas que recibiste el pago?')"><i class="fas fa-check"></i></a>
                                    
                                    <a href="admin.php?accion=rechazar&id=<?php echo $row['id']; ?>" class="btn-icon btn-no" title="Rechazar" onclick="return confirm('¿Rechazar reserva?')"><i class="fas fa-times"></i></a>
                                <?php else: ?>
                                    <span style="color:#ccc;">--</span>
                                <?php endif; ?>
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