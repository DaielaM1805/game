<?php
session_start();
require_once '../config/database.php';

// Verificar autenticación
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_partida'])) {
    header('Location: seleccion.php');
    exit();
}

try {
    $db = new Database();
    $conn = $db->conectar();
    
    // Obtener información de la partida
    $query = "SELECT 
                p.id_estado,
                pj.id_jugador,
                pj.vida,
                pj.estado,
                u.user_name,
                m.nom_mundo,
                m.img_mundo
              FROM partidas p
              JOIN partida_jugadores pj ON p.id_partida = pj.id_partida
              JOIN usuario u ON pj.id_jugador = u.id_usuario
              JOIN sala s ON p.id_sala = s.id_sala
              JOIN mundos m ON s.id_mundo = m.id_mundo
              WHERE p.id_partida = :id_partida";
              
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_partida', $_SESSION['id_partida']);
    $stmt->execute();
    $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar que la partida realmente terminó
    if (empty($jugadores) || $jugadores[0]['id_estado'] != 6) {
        header('Location: partida.php');
        exit();
    }

    // Obtener ganador y perdedor
    $ganador = null;
    $perdedor = null;
    foreach ($jugadores as $jugador) {
        if ($jugador['estado'] === 'vivo') {
            $ganador = $jugador;
        } else {
            $perdedor = $jugador;
        }
    }

    $mundo = [
        'nombre' => $jugadores[0]['nom_mundo'],
        'imagen' => $jugadores[0]['img_mundo']
    ];
} catch (Exception $e) {
    error_log("Error en resultado.php: " . $e->getMessage());
    header('Location: seleccion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fin de la Partida - <?php echo htmlspecialchars($mundo['nombre']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #fff;
            min-height: 100vh;
            background-image: url('../<?php echo htmlspecialchars($mundo['imagen']); ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .result-container {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
            text-align: center;
        }
        .winner-card {
            background: rgba(40, 167, 69, 0.2);
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .loser-card {
            background: rgba(220, 53, 69, 0.2);
            border: 2px solid #dc3545;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            opacity: 0.8;
        }
        .player-name {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .vida-bar {
            height: 20px;
            background: #333;
            border-radius: 10px;
            overflow: hidden;
            margin: 0.5rem 0;
        }
        .vida-fill {
            height: 100%;
            background: #28a745;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-container">
            <h1 class="mb-4">¡Fin de la Partida!</h1>
            
            <?php if ($ganador): ?>
            <div class="winner-card">
                <h3>¡Ganador!</h3>
                <div class="player-name"><?php echo htmlspecialchars($ganador['user_name']); ?></div>
                <div class="vida-bar">
                    <div class="vida-fill" style="width: <?php echo $ganador['vida']; ?>%"></div>
                </div>
                <p>Vida restante: <?php echo $ganador['vida']; ?>/100</p>
            </div>
            <?php endif; ?>

            <?php if ($perdedor): ?>
            <div class="loser-card">
                <h3>Perdedor</h3>
                <div class="player-name"><?php echo htmlspecialchars($perdedor['user_name']); ?></div>
                <div class="vida-bar">
                    <div class="vida-fill" style="width: <?php echo $perdedor['vida']; ?>%"></div>
                </div>
                <p>Vida restante: <?php echo $perdedor['vida']; ?>/100</p>
            </div>
            <?php endif; ?>

            <div class="mt-4">
                <a href="seleccion.php" class="btn btn-primary btn-lg">Jugar Otra Vez</a>
            </div>
        </div>
    </div>
</body>
</html>
