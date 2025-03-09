<?php
session_start();
require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->conectar();

    if (isset($_SESSION['id_usuario'])) {
        // Obtener la sala actual del jugador
        $stmt = $conn->prepare("SELECT id_sala FROM sala_jugadores WHERE id_jugador = :id_jugador");
        $stmt->bindParam(':id_jugador', $_SESSION['id_usuario']);
        $stmt->execute();
        $sala = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sala) {
            // Eliminar al jugador de sala_jugadores
            $stmt = $conn->prepare("DELETE FROM sala_jugadores WHERE id_jugador = :id_jugador");
            $stmt->bindParam(':id_jugador', $_SESSION['id_usuario']);
            $stmt->execute();

            // Eliminar armas del jugador
            $stmt = $conn->prepare("DELETE FROM sala_armas WHERE id_jugador = :id_jugador");
            $stmt->bindParam(':id_jugador', $_SESSION['id_usuario']);
            $stmt->execute();

            // Verificar si la sala quedó vacía
            $stmt = $conn->prepare("SELECT COUNT(*) as jugadores FROM sala_jugadores WHERE id_sala = :id_sala");
            $stmt->bindParam(':id_sala', $sala['id_sala']);
            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($count['jugadores'] == 0) {
                // Si la sala está vacía, eliminarla
                $stmt = $conn->prepare("DELETE FROM sala WHERE id_sala = :id_sala");
                $stmt->bindParam(':id_sala', $sala['id_sala']);
                $stmt->execute();
            }
        }
    }

    // Limpiar la sesión
    unset($_SESSION['id_sala']);
    
    // Si es una petición AJAX, devolver JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    // Si no es AJAX, redirigir
    header('Location: ../jugador/seleccion.php');
    exit;

} catch (Exception $e) {
    error_log("Error en abandonar_sala.php: " . $e->getMessage());
    
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }

    header('Location: ../jugador/seleccion.php');
    exit;
}
?> 