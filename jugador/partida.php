<?php
session_start();
require_once '../config/database.php';

// Verificar autenticación y partida activa
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_partida'])) {
    header('Location: seleccion.php');
    exit;
}

try {
    $db = new Database();
    $conn = $db->conectar();
    
    // Obtener información de la partida y jugadores
    $query = "
        SELECT 
            p.id_partida,
            p.id_sala,
            pj.id_jugador,
            pj.vida,
            pj.estado,
            u.user_name,
            u.id_avatar,
            av.img_avatar as avatar_img,
            sa.id_arma,
            a.nom_arma,
            a.img_arma,
            ta.dano,
            m.nom_mundo,
            m.img_mundo
        FROM partidas p
        INNER JOIN partida_jugadores pj ON p.id_partida = pj.id_partida
        INNER JOIN usuario u ON pj.id_jugador = u.id_usuario
        LEFT JOIN avatar av ON u.id_avatar = av.id_avatar
        INNER JOIN sala s ON p.id_sala = s.id_sala
        INNER JOIN sala_armas sa ON s.id_sala = sa.id_sala AND pj.id_jugador = sa.id_jugador
        INNER JOIN arma a ON sa.id_arma = a.id_arma
        INNER JOIN tipo_arma ta ON a.id_tipo = ta.id_tipo
        INNER JOIN mundos m ON s.id_mundo = m.id_mundo
        WHERE p.id_partida = :id_partida";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_partida', $_SESSION['id_partida']);
    $stmt->execute();
    $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($jugadores)) {
        throw new Exception('No se encontraron jugadores en la partida');
    }

    // Obtener datos del mundo
    $mundo = [
        'nombre' => $jugadores[0]['nom_mundo'],
        'imagen' => $jugadores[0]['img_mundo']
    ];

    $id_jugador = $_SESSION['id_usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partida en Curso - <?php echo htmlspecialchars($mundo['nombre']); ?></title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .game-container {
            background: rgba(0, 0, 0, 0.85);
            border-radius: 20px;
            padding: 2.5rem;
            margin-top: 2rem;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        .player-card {
            background: linear-gradient(145deg, rgba(33, 37, 41, 0.95), rgba(23, 27, 31, 0.95));
            border: 2px solid #0d6efd;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .player-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .player-avatar {
            width: 80px;
            height: 135px;
            border-radius: 1%;
            object-fit: cover;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }
        .player-card.selected .player-avatar {
            border-color: #dc3545;
        }
        .player-card.dead .player-avatar {
            border-color: #6c757d;
            filter: grayscale(1);
        }
        .player-info {
            flex-grow: 1;
        }
        .health-bar {
            height: 25px;
            background: rgba(51, 51, 51, 0.8);
            border-radius: 12px;
            overflow: hidden;
            margin: 15px 0;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .health-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #34ce57);
            transition: width 0.5s ease, background 0.5s ease;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }
        .health-bar-fill.critical {
            background: linear-gradient(90deg, #dc3545, #ff4d4d);
            animation: pulse-critical 1s infinite;
        }
        @keyframes pulse-critical {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        .weapon-info {
            display: flex;
            align-items: center;
            margin-top: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .weapon-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 15px;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.5));
            transition: transform 0.3s ease;
        }
        .weapon-img:hover {
            transform: scale(1.1);
        }
        .weapon-info p {
            margin-bottom: 5px;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.9);
        }
        .attack-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 1rem 2.5rem;
            font-size: 1.3rem;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgba(220, 53, 69, 0.4);
            transition: all 0.3s ease;
        }
        .attack-btn:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.6);
        }
        .attack-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .vida-text {
            font-size: 1.1rem;
            font-weight: 500;
            color: #28a745;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        .damage-animation {
            animation: damage 0.5s ease-out;
        }
        @keyframes damage {
            0% { transform: scale(1); }
            50% { transform: scale(0.95); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="game-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Partida en Curso - <?php echo htmlspecialchars($mundo['nombre']); ?></h1>
                <div id="timer" class="h3 text-warning">
                    <i class="fas fa-clock me-2"></i>
                    <span>05:00</span>
                </div>
            </div>
            
            <div class="row">
                <?php foreach ($jugadores as $jugador): ?>
                    <div class="col-md-4 mb-3">
                        <div class="player-card <?php 
                            echo $jugador['estado'] === 'muerto' ? 'dead' : '';
                            echo $jugador['id_jugador'] !== $id_jugador ? ' selectable' : '';
                        ?>" data-jugador-id="<?php echo $jugador['id_jugador']; ?>">
                            <div class="player-header">
                                <img src="../<?php echo htmlspecialchars($jugador['avatar_img']); ?>" 
                                     class="player-avatar" 
                                     alt="Avatar de <?php echo htmlspecialchars($jugador['user_name']); ?>">
                                <div class="player-info">
                                    <h4><?php echo htmlspecialchars($jugador['user_name']); ?></h4>
                                    <div class="health-bar">
                                        <div class="health-bar-fill <?php echo $jugador['vida'] <= 20 ? 'critical' : ''; ?>" 
                                             style="width: <?php echo $jugador['vida']; ?>%">
                                        </div>
                                    </div>
                                    <p class="mb-0 vida-text">Vida: <?php echo htmlspecialchars($jugador['vida']); ?>/100</p>
                                </div>
                            </div>
                            <div class="weapon-info">
                                <img src="../<?php echo htmlspecialchars($jugador['img_arma']); ?>" 
                                     class="weapon-img" 
                                     alt="<?php echo htmlspecialchars($jugador['nom_arma']); ?>">
                                <div>
                                    <p class="mb-0">Arma: <?php echo htmlspecialchars($jugador['nom_arma']); ?></p>
                                    <p class="mb-0">Daño: <?php echo htmlspecialchars($jugador['dano']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <button class="btn btn-danger attack-btn" id="btnAtacar" disabled>
        Atacar <span id="selectedPlayerName"></span>
    </button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let selectedPlayer = null;
        const currentPlayerId = <?php echo $id_jugador; ?>;

        // Selección de jugador objetivo
        $('.player-card.selectable').click(function() {
            if ($(this).hasClass('dead')) return;
            
            $('.player-card').removeClass('selected');
            $(this).addClass('selected');
            selectedPlayer = $(this).data('jugador-id');
            
            const playerName = $(this).find('h4').text();
            $('#selectedPlayerName').text('a ' + playerName);
            $('#btnAtacar').prop('disabled', false);
        });

        // Realizar ataque
        $('#btnAtacar').click(function() {
            if (!selectedPlayer) return;

            // Deshabilitar botón durante el ataque
            $(this).prop('disabled', true);

            $.post('../include/realizar_ataque.php', {
                id_objetivo: selectedPlayer
            })
            .done(function(response) {
                if (response.success) {
                    // Actualizar vida del objetivo
                    const targetCard = $(`.player-card[data-jugador-id="${selectedPlayer}"]`);
                    const healthBar = targetCard.find('.health-bar-fill');
                    const healthText = targetCard.find('.vida-text');
                    
                    // Añadir animación de daño
                    targetCard.addClass('damage-animation');
                    setTimeout(() => targetCard.removeClass('damage-animation'), 500);
                    
                    // Actualizar barra de vida con animación
                    healthBar.css('width', response.nueva_vida + '%');
                    healthText.text('Vida: ' + response.nueva_vida + '/100');

                    // Si el jugador murió, aplicar efectos visuales
                    if (response.estado === 'muerto') {
                        targetCard.addClass('dead');
                        targetCard.removeClass('selected');
                        selectedPlayer = null;
                    }

                    // Quitar selección
                    $('.player-card').removeClass('selected');
                    $('#selectedPlayerName').text('');
                } else {
                    console.error('Error al realizar ataque:', response.error);
                    alert('Error al realizar el ataque: ' + response.error);
                }
            })
            .fail(function(error) {
                console.error('Error en la petición:', error);
                alert('Error al realizar el ataque');
            })
            .always(function() {
                // Re-habilitar botón después del ataque
                $('#btnAtacar').prop('disabled', selectedPlayer === null);
            });
        });

        // Agregar el temporizador de 5 minutos
        let timeLeft = 300; // 5 minutos en segundos
        const timerElement = document.querySelector('#timer span');
        
        const timer = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                finalizarPartida('timeout');
            }
        }, 1000);

        function finalizarPartida(motivo) {
            $.post('../include/finalizar_partida.php', {
                motivo: motivo
            })
            .done(function(response) {
                if (response.success) {
                    window.location.href = 'resultado.php';
                }
            });
        }

        // Modificar la función actualizarEstadoJuego para verificar jugadores muertos
        function actualizarEstadoJuego() {
            $.get('../include/get_estado_partida.php')
            .done(function(response) {
                if (response.success) {
                    let jugadorMuerto = false;
                    
                    response.jugadores.forEach(function(jugador) {
                        const playerCard = $(`.player-card[data-jugador-id="${jugador.id_jugador}"]`);
                        const healthBar = playerCard.find('.health-bar-fill');
                        const healthText = playerCard.find('.vida-text');
                        
                        healthBar.css('width', jugador.vida + '%');
                        healthText.text('Vida: ' + jugador.vida + '/100');
                        
                        if (jugador.vida <= 0) {
                            jugadorMuerto = true;
                        }
                        
                        if (jugador.vida <= 20) {
                            healthBar.addClass('critical');
                        } else {
                            healthBar.removeClass('critical');
                        }
                    });

                    // Si hay un jugador muerto, finalizar la partida
                    if (jugadorMuerto) {
                        finalizarPartida('muerte');
                    }
                }
            });
        }

        setInterval(actualizarEstadoJuego, 3000);
    </script>
</body>
</html>
<?php
} catch (Exception $e) {
    error_log("Error en partida.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    header('Location: seleccion.php');
    exit();
}
?>