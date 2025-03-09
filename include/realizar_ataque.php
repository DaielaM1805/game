<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Verificar autenticación y datos necesarios
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_partida']) || !isset($_POST['id_objetivo'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$id_partida = $_SESSION['id_partida'];
$id_atacante = $_SESSION['id_usuario'];
$id_objetivo = $_POST['id_objetivo'];

try {
    $db = new Database();
    $conn = $db->conectar();
    
    // Iniciar transacción
    $conn->beginTransaction();

    // Verificar que tanto el atacante como el objetivo están vivos
    $query = "SELECT id_jugador, estado FROM partida_jugadores 
              WHERE id_partida = :id_partida 
              AND id_jugador IN (:id_atacante, :id_objetivo)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_partida', $id_partida);
    $stmt->bindParam(':id_atacante', $id_atacante);
    $stmt->bindParam(':id_objetivo', $id_objetivo);
    $stmt->execute();
    $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($jugadores) !== 2) {
        throw new Exception('Jugador no encontrado en la partida');
    }

    foreach ($jugadores as $jugador) {
        if ($jugador['estado'] === 'muerto') {
            throw new Exception('No se puede atacar a un jugador muerto');
        }
    }

    // Obtener el daño del arma del atacante
    $query = "SELECT ta.dano
              FROM partidas p
              JOIN sala s ON p.id_sala = s.id_sala
              JOIN sala_armas sa ON s.id_sala = sa.id_sala
              JOIN arma a ON sa.id_arma = a.id_arma
              JOIN tipo_arma ta ON a.id_tipo = ta.id_tipo
              WHERE p.id_partida = :id_partida 
              AND sa.id_jugador = :id_atacante";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_partida', $id_partida);
    $stmt->bindParam(':id_atacante', $id_atacante);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resultado) {
        throw new Exception('No se encontró el arma del atacante');
    }
    
    $dano = $resultado['dano'];

    // Obtener la vida actual del objetivo
    $query = "SELECT vida FROM partida_jugadores 
              WHERE id_partida = :id_partida 
              AND id_jugador = :id_objetivo";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_partida', $id_partida);
    $stmt->bindParam(':id_objetivo', $id_objetivo);
    $stmt->execute();
    $jugador = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calcular nueva vida
    $nueva_vida = max(0, $jugador['vida'] - $dano);
    $nuevo_estado = $nueva_vida <= 0 ? 'muerto' : 'vivo';

    // Actualizar la vida del objetivo
    $query = "UPDATE partida_jugadores 
              SET vida = :nueva_vida,
                  estado = :nuevo_estado
              WHERE id_partida = :id_partida 
              AND id_jugador = :id_objetivo";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':nueva_vida', $nueva_vida);
    $stmt->bindParam(':nuevo_estado', $nuevo_estado);
    $stmt->bindParam(':id_partida', $id_partida);
    $stmt->bindParam(':id_objetivo', $id_objetivo);
    $stmt->execute();

    // Confirmar transacción
    $conn->commit();

    echo json_encode([
        'success' => true,
        'dano' => $dano,
        'nueva_vida' => $nueva_vida,
        'estado' => $nuevo_estado
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    error_log("Error en realizar_ataque.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>