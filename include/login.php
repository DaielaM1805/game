<?php
session_start();
require '../config/database.php'; 

$db = new Database();
$con = $db->conectar(); 

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     if (isset($_POST['logout'])) {
//         // Si se envía el logout, cerramos la sesión
//         session_unset();
//         session_destroy();
//         echo json_encode(["status" => "success", "message" => "Sesión cerrada."]);
//         exit();
//     }

    // Procesar el inicio de sesión
    $usuario = $_POST['usuario'] ?? null;
    $contra = $_POST['contra'] ?? null;

    if (empty($usuario) || empty($contra)) {
        echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
        exit();
    }

    // Buscar usuario en la base de datos usando user_name en vez de email
    $sql = "SELECT id_usuario, user_name, contra, id_avatar, id_estado, id_rol 
            FROM usuario WHERE user_name = :usuario";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fila && password_verify($contra, $fila["contra"])) {
        if ($fila["id_estado"] == 1) { // Usuario activo
            $_SESSION['id_usuario'] = $fila['id_usuario'];
            $_SESSION['user_name'] = $fila['user_name'];
            $_SESSION['id_avatar'] = $fila['id_avatar'];
            $_SESSION['id_rol'] = $fila['id_rol'];
            $_SESSION['id_estado'] = $fila['id_estado'];

            // Redirección según el rol del usuario
            if ($fila["id_rol"] == 1) {
                echo json_encode(["status" => "success", "redirect" => "admin/index.php"]);
            } elseif ($fila["id_rol"] == 2) {
                echo json_encode(["status" => "success", "redirect" => "jugador/index.php"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Rol no reconocido."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Usuario inactivo. Contacte al administrador."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Credenciales incorrectas."]);
    }
    exit();

?>
