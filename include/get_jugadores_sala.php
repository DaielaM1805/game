<?php
require_once(__DIR__ . "/../config/database.php");

session_start();
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

if (!isset($_GET['id_sala'])) {
    echo json_encode(["error" => "ID de sala no proporcionado"]);
    exit;
}

$db = new Database();
$conn = $db->conectar();
$id_sala = $_GET['id_sala'];

try {
    // Verificar si el usuario pertenece a la sala
    $query = "SELECT id_sala FROM sala_jugadores 
              WHERE id_sala = :id_sala 
              AND id_jugador = :id_usuario";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_sala", $id_sala);
    $stmt->bindParam(":id_usuario", $_SESSION["id_usuario"]);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        echo json_encode(["error" => "No tienes acceso a esta sala"]);
        exit;
    }

    // Obtener información de todos los jugadores en la sala
    $query = "SELECT sj.id_jugador, u.user_name, a.nom_arma, a.img_arma, ta.dano
              FROM sala_jugadores sj
              JOIN usuario u ON sj.id_jugador = u.id_usuario
              LEFT JOIN sala_armas sa ON sj.id_sala = sa.id_sala AND sj.id_jugador = sa.id_jugador
              LEFT JOIN arma a ON sa.id_arma = a.id_arma
              LEFT JOIN tipo_arma ta ON a.id_tipo = ta.id_tipo
              WHERE sj.id_sala = :id_sala";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_sala", $id_sala);
    $stmt->execute();
    
    $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verificar si hay suficientes jugadores para iniciar
    $iniciar_partida = count($jugadores) >= 2;
    
    // Si hay 2 o más jugadores, verificar si ya existe una partida
    if ($iniciar_partida) {
        $query = "SELECT id_partida FROM partidas 
                 WHERE id_sala = :id_sala 
                 AND id_estado = 5";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id_sala", $id_sala);
        $stmt->execute();
        $partida = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$partida) {
            $conn->beginTransaction();
            try {
                // Crear nueva partida
                $query = "INSERT INTO partidas (id_sala, id_estado) VALUES (:id_sala, 5)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(":id_sala", $id_sala);
                $stmt->execute();
                $id_partida = $conn->lastInsertId();

                // Agregar jugadores a la partida
                foreach ($jugadores as $jugador) {
                    $query = "INSERT INTO partida_jugadores (id_partida, id_jugador, vida, estado) 
                             VALUES (:id_partida, :id_jugador, 100, 'vivo')";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(":id_partida", $id_partida);
                    $stmt->bindParam(":id_jugador", $jugador['id_jugador']);
                    $stmt->execute();
                }

                $_SESSION['id_partida'] = $id_partida;
                $conn->commit();
                
                error_log("Partida creada con ID: " . $id_partida);
                error_log("Jugadores en partida: " . json_encode($jugadores));
                
                echo json_encode([
                    "jugadores" => $jugadores,
                    "iniciar_partida" => true,
                    "id_partida" => $id_partida
                ]);
                exit;
            } catch (Exception $e) {
                $conn->rollBack();
                error_log("Error al crear partida: " . $e->getMessage());
                throw $e;
            }
        } else {
            $_SESSION['id_partida'] = $partida['id_partida'];
            error_log("Partida existente encontrada con ID: " . $partida['id_partida']);
            echo json_encode([
                "jugadores" => $jugadores,
                "iniciar_partida" => true,
                "id_partida" => $partida['id_partida']
            ]);
            exit;
        }
    }
    
    echo json_encode([
        "jugadores" => $jugadores,
        "iniciar_partida" => false
    ]);

} catch (PDOException $e) {
    error_log("Error en get_jugadores_sala.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(["error" => "Error al obtener jugadores"]);
}
?>