<?php
// session_start();
require_once('../config/database.php');
include('../include/sesion.php');
include 'menu.html';

// Evita que el navegador almacene en caché la página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");

// Verifica si el usuario ha iniciado sesión
// if (!isset($_SESSION['user_name']) || !isset($_SESSION['id_rol'])) {
//     header("Location: ../index.php"); // Redirige al login si no hay sesión
//     exit();
// }

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
    session_destroy(); // Destruye la sesión si hay un error
    // header("Location: ../index.php");
    echo '<script>window.location = "../login.php"</script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <style>
         body{
            background-image: url(../img/fondo_admin.png);
            background-size: cover;
            height: 100vh; 
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
         }
    </style>
</head>
<body>
<header>
    <nav class="opcion">
        <h1>Bienvenido <?php echo htmlspecialchars($fila['user_name']); ?>, su rol es <?php echo htmlspecialchars($fila['nom_rol']); ?></h1>
    </nav>
</header>
</body>
</html>
