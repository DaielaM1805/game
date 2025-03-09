<?php
require_once(__DIR__ . "/../config/database.php");

session_start();
if (!isset($_SESSION["id_usuario"])) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

try {
    header('Content-Type: application/json');
    
    $db = new Database();
    $conn = $db->conectar();
    
    // Obtener el nivel del usuario
    $query = "SELECT n.id_nivel 
              FROM deta_niv dn 
              INNER JOIN nivel n ON dn.id_nivel = n.id_nivel 
              WHERE dn.id_usuario = :id_usuario";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_usuario", $_SESSION["id_usuario"]);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $nivel_usuario = $usuario['id_nivel'] ?? 1; // Si no tiene nivel asignado, asumimos nivel 1
    
    // Obtener todas las armas con sus características
    $query = "SELECT a.id_arma, a.nom_arma, a.img_arma, 
                     ta.nom_tip_arma, ta.dano, ta.balas,
                     CASE WHEN da.id_arma IS NOT NULL THEN 1 ELSE 0 END as tiene_arma
              FROM arma a 
              INNER JOIN tipo_arma ta ON a.id_tipo = ta.id_tipo
              LEFT JOIN deta_arma da ON a.id_arma = da.id_arma AND da.id_usuario = :id_usuario
              ORDER BY a.id_arma ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_usuario", $_SESSION["id_usuario"]);
    $stmt->execute();
    $armas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Procesar cada arma
    $armas_procesadas = array_map(function($arma) use ($nivel_usuario) {
        // Determinar si el arma está disponible basado en el nivel del usuario y si ya la tiene
        $nivel_requerido = 2; // Por defecto, todas las armas requieren nivel 2
        
        // El puño y las primeras dos armas (glock y desert eagle) están desbloqueadas desde el inicio
        if ($arma['id_arma'] <= 3) {
            $nivel_requerido = 1;
        }
        
        $disponible = $arma['tiene_arma'] || $nivel_usuario >= $nivel_requerido;
        
        return [
            'id_arma' => $arma['id_arma'],
            'nom_arma' => $arma['nom_arma'],
            'img_arma' => $arma['img_arma'],
            'tipo_arma' => $arma['nom_tip_arma'],
            'dano' => $arma['dano'],
            'balas' => $arma['balas'],
            'disponible' => $disponible,
            'mensaje' => $disponible ? '' : 'Requiere nivel ' . $nivel_requerido
        ];
    }, $armas);
    
    // Ordenar armas: primero las disponibles
    usort($armas_procesadas, function($a, $b) {
        if ($a['disponible'] != $b['disponible']) {
            return $b['disponible'] - $a['disponible'];
        }
        return $a['id_arma'] - $b['id_arma'];
    });
    
    echo json_encode($armas_procesadas);
    
} catch (Exception $e) {
    error_log("Error en get_armas.php: " . $e->getMessage());
    echo json_encode(["error" => "Error al obtener las armas"]);
}
?> 