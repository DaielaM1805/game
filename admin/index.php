<?php
session_start();
require_once('../config/database.php');
include 'menu.html';

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_name']) || !isset($_SESSION['id_rol'])) {
    die("Acceso denegado. Inicie sesión primero.");
}

// Conectar a la base de datos
$conex = new Database();
$con = $conex->conectar();

if (!$con) {
    die("Error de conexión a la base de datos.");
}

$user_name = $_SESSION['user_name'];

// Consulta para obtener los datos del usuario
$sql = $con->prepare("SELECT usuario.user_name, rol.nom_rol 
                      FROM usuario
                      INNER JOIN rol ON usuario.id_rol = rol.id_rol 
                      WHERE usuario.user_name = :user_name");

$sql->bindParam(':user_name', $user_name, PDO::PARAM_STR);
$sql->execute();
$fila = $sql->fetch(PDO::FETCH_ASSOC);

if (!$fila) {
    die("Error al recuperar los datos del usuario.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
</head>
<body>
<header>
    <nav class="opcion">
        <h1>Bienvenido <?php echo htmlspecialchars($fila['user_name']); ?>, su rol es <?php echo htmlspecialchars($fila['nom_rol']); ?></h1>
    </nav>
</header>
</body>
</html>
