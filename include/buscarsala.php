<?php
session_start();
require_once '../config/database.php';

error_log("buscarsala.php - Inicio del script");

header('Content-Type: application/json');

function enviarRespuesta($success, $mensaje = '', $redirect_url = '') {
    echo json_encode([
        'success' => $success,
        'mensaje' => $mensaje,
        'redirect_url' => $redirect_url
    ]);
    exit;
}

// Verificar autenticación
if (!isset($_SESSION['id_usuario'])) {
    error_log("buscarsala.php - Usuario no autenticado");
    enviarRespuesta(false, 'Sesión no válida');
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("buscarsala.php - Método no permitido: " . $_SERVER['REQUEST_METHOD']);
    enviarRespuesta(false, 'Método no permitido');
}

// Verificar parámetros
if (!isset($_POST['id_mundo']) || !isset($_POST['id_arma'])) {
    error_log("buscarsala.php - Faltan parámetros");
    error_log("POST recibido: " . print_r($_POST, true));
    enviarRespuesta(false, 'Faltan parámetros');
}

$id_mundo = intval($_POST['id_mundo']);
$id_arma = intval($_POST['id_arma']);
$id_jugador = $_SESSION['id_usuario'];

error_log("buscarsala.php - Datos recibidos: mundo=$id_mundo, arma=$id_arma, jugador=$id_jugador");

try {
    $db = new Database();
    $conn = $db->conectar();
    
    error_log("buscarsala.php - Conexión a base de datos exitosa");
    $conn->beginTransaction();

    // 1. Eliminar al jugador de cualquier sala existente
    $stmt = $conn->prepare("DELETE FROM sala_armas WHERE id_jugador = ?");
    $stmt->execute([$id_jugador]);
    
    $stmt = $conn->prepare("DELETE FROM sala_jugadores WHERE id_jugador = ?");
    $stmt->execute([$id_jugador]);

    // 2. Buscar una sala disponible
    $stmt = $conn->prepare("SELECT id_sala FROM sala WHERE id_mundo = ? AND id_estado = 1 AND (SELECT COUNT(*) FROM sala_jugadores WHERE sala_jugadores.id_sala = sala.id_sala) < 5 LIMIT 1");
    $stmt->execute([$id_mundo]);
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sala) {
        $stmt = $conn->prepare("INSERT INTO sala (id_mundo, id_estado) VALUES (?, 1)");
        $stmt->execute([$id_mundo]);
        $id_sala = $conn->lastInsertId();
    } else {
        $id_sala = $sala['id_sala'];
    }

    // 3. Agregar jugador a la sala
    $stmt = $conn->prepare("INSERT INTO sala_jugadores (id_sala, id_jugador) VALUES (?, ?)");
    $stmt->execute([$id_sala, $id_jugador]);

    // 4. Asignar arma al jugador
    $stmt = $conn->prepare("INSERT INTO sala_armas (id_sala, id_jugador, id_arma) VALUES (?, ?, ?)");
    $stmt->execute([$id_sala, $id_jugador, $id_arma]);

    $conn->commit();

    // 5. Guardar id_sala en la sesión
    $_SESSION['id_sala'] = $id_sala;
    session_write_close();

    error_log("buscarsala.php - Proceso completado. ID sala: $id_sala");
    
    enviarRespuesta(true, 'Sala creada/unida exitosamente', 'sala.php');

} catch (Exception $e) {
    error_log("Error en buscarsala.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    if (isset($conn)) {
        $conn->rollBack();
    }
    
    enviarRespuesta(false, 'Error al procesar la solicitud');
}
?>
