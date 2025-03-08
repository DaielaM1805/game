<?php
session_start();
require_once '../config/database.php';

// Verificar autenticación
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit();
}

// Verificar si hay una sala asignada
if (!isset($_SESSION['id_sala'])) {
    header('Location: seleccion.php');
    exit();
}

try {
    $db = new Database();
    $conn = $db->conectar();
    
    // Verificar si hay una partida activa para esta sala
    $query = "SELECT p.id_partida 
             FROM partidas p 
             WHERE p.id_sala = :id_sala 
             AND p.id_estado = 5";
             
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_sala", $_SESSION['id_sala']);
    $stmt->execute();
    $partida = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($partida) {
        $_SESSION['id_partida'] = $partida['id_partida'];
        header('Location: partida.php');
        exit();
    }
    
    // Obtener información de la sala y el mundo
    $query = "SELECT m.nom_mundo, m.img_mundo 
             FROM sala s 
             JOIN mundos m ON s.id_mundo = m.id_mundo 
             WHERE s.id_sala = :id_sala";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_sala", $_SESSION['id_sala']);
    $stmt->execute();
    $mundo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mundo) {
        header('Location: seleccion.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala de Espera - <?php echo htmlspecialchars($mundo['nom_mundo']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            min-height: 100vh;
            color: #fff;
            background-image: url('../<?php echo htmlspecialchars($mundo['img_mundo']); ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .sala-container {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 0 20px rgba(0,123,255,0.3);
        }
        .jugadores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .jugador-card {
            background: rgba(33, 37, 41, 0.9);
            border: 2px solid #0d6efd;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .jugador-card:hover {
            transform: translateY(-5px);
        }
        .jugador-card.vacio {
            border-style: dashed;
            border-color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
        }
        .arma-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin: 1rem 0;
        }
        .countdown {
            font-size: 2rem;
            color: #0d6efd;
            text-align: center;
            margin: 1rem 0;
            font-weight: bold;
            display: none;
        }
        .btn-salir {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 50px;
            box-shadow: 0 0 15px rgba(220,53,69,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sala-container">
            <h1 class="text-center mb-4">Sala de Espera - <?php echo htmlspecialchars($mundo['nom_mundo']); ?></h1>
            
            <div class="text-center mb-4">
                <h4>ID de Sala: <?php echo htmlspecialchars($_SESSION['id_sala']); ?></h4>
                <p class="lead" id="jugadores-count">Jugadores conectados: 0/5</p>
            </div>

            <div id="countdown" class="countdown">
                La partida comenzará en <span id="timer">10</span> segundos
            </div>

            <div id="jugadores-grid" class="jugadores-grid">
                <!-- Los jugadores se agregarán dinámicamente aquí -->
            </div>
        </div>
    </div>

    <button class="btn btn-danger btn-salir" onclick="abandonarSala()">
        <i class="fas fa-sign-out-alt me-2"></i>Abandonar Sala
    </button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let countdownStarted = false;
        let redirecting = false;
        let actualizadorJugadores;

        function actualizarJugadores() {
            if (redirecting) {
                console.log('Redirección en proceso, ignorando actualización');
                return;
            }

            $.get('../include/get_jugadores_sala.php', { id_sala: <?php echo $_SESSION['id_sala']; ?> })
            .done(function(response) {
                try {
                    console.log('Respuesta recibida:', response);
                    const data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }

                    if (data.id_partida) {
                        console.log('ID de partida detectado:', data.id_partida);
                        if (!redirecting) {
                            console.log('Iniciando redirección a partida.php');
                            redirecting = true;
                            if (actualizadorJugadores) {
                                console.log('Limpiando intervalo de actualización');
                                clearInterval(actualizadorJugadores);
                            }
                            window.location.href = 'partida.php';
                        } else {
                            console.log('Redirección ya en proceso, ignorando');
                        }
                        return;
                    }

                    const numJugadores = data.jugadores.length;
                    console.log('Número de jugadores:', numJugadores);
                    $('#jugadores-count').text(`Jugadores conectados: ${numJugadores}/5`);

                    const grid = $('#jugadores-grid');
                    grid.empty();

                    // Agregar jugadores conectados
                    data.jugadores.forEach(function(jugador) {
                        grid.append(`
                            <div class="jugador-card">
                                <h4>${jugador.user_name || 'Jugador'}</h4>
                                <img src="../${jugador.img_arma || 'img/armas/default.png'}" alt="${jugador.nom_arma || 'Arma'}" class="arma-img">
                                <div class="mt-3">
                                    <p class="mb-1">Arma: ${jugador.nom_arma || 'No seleccionada'}</p>
                                    <p class="mb-0">Daño: ${jugador.dano || '0'}</p>
                                </div>
                            </div>
                        `);
                    });

                    // Agregar espacios vacíos
                    for (let i = numJugadores; i < 5; i++) {
                        grid.append(`
                            <div class="jugador-card vacio">
                                <div>
                                    <i class="fas fa-user-plus fa-3x mb-3"></i>
                                    <p class="mb-0">Esperando jugador...</p>
                                </div>
                            </div>
                        `);
                    }

                    // Iniciar cuenta regresiva si hay 2 o más jugadores
                    if (numJugadores >= 2 && !countdownStarted) {
                        console.log('Iniciando cuenta regresiva');
                        countdownStarted = true;
                        $('#countdown').show();
                        let seconds = 10;
                        const timer = setInterval(() => {
                            seconds--;
                            $('#timer').text(seconds);
                            if (seconds <= 0) {
                                clearInterval(timer);
                                $('#countdown').hide();
                            }
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Error al procesar respuesta:', error);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error en la petición:', textStatus, errorThrown);
            });
        }

        // Iniciar el actualizador
        $(document).ready(function() {
            console.log('Iniciando actualizador de jugadores');
            actualizarJugadores(); // Primera actualización inmediata
            actualizadorJugadores = setInterval(actualizarJugadores, 3000);
        });

        function abandonarSala() {
            if (redirecting) {
                console.log('Redirección en proceso, ignorando abandono de sala');
                return;
            }
            window.location.href = '../include/abandonar_sala.php';
        }
    </script>
</body>
</html>
<?php
} catch (Exception $e) {
    error_log("Error en sala.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    header('Location: seleccion.php');
    exit();
}
?>