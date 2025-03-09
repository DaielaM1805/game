<?php
session_start();
require_once('../config/database.php');
$conexion = new database();
$con = $conexion->conectar();



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ADMINISTRADOR - Estadísticas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="estilo.css"> -->
</head>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const menu = document.querySelector(".menu_lateral");
    const menuBtn = document.createElement("button");
    menuBtn.textContent = "☰";
    menuBtn.classList.add("menu-btn");
    document.body.appendChild(menuBtn);
    menuBtn.addEventListener("click", function() {
        menu.classList.toggle("mostrar");
    });
});
</script>

<body>
    <div class="menu_lateral">
        <?php
        $sql = $con->prepare("SELECT user_name, id_usuario FROM usuario LIMIT 1");
        $sql->execute();
        $fila = $sql->fetch(PDO::FETCH_ASSOC);
        ?>

        <?php if ($fila): ?>
            <h1><?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            <p><?php echo htmlspecialchars($_SESSION['id_usuario']); ?></p>
        <?php else: ?>
            <h1>Usuario no encontrado</h1>
        <?php endif; ?>

        <div class="menu">
            <a href="index.php">index administrador</a>
            <!-- <a href="partidas.php">Partidas</a> -->
            <!-- <a href="estadisticas_usu.php">Estadísticas de jugadores</a> -->
            <a href="../index.php" class="cerrar-sesion">Cerrar sesión</a>
        </div>
    </div>
    <div class="wrapper">
        <div id="menu-wrapper">
            <!-- <nav>
                <a href="" id="logo">
                    <img src="../img/logo.png" alt="Logo" class="logo" />
                </nav>
            </a> -->
        </div>

        <h2>📊 Estadísticas Generales</h2>
        <div class="estadisticas">

            <?php
            // Total de jugadores
            $sql = $con->prepare("SELECT COUNT(*) as total_usuarios FROM usuario");
            $sql->execute();
            $totalUsuarios = $sql->fetch(PDO::FETCH_ASSOC)['total_usuarios'];

            // Usuarios activos (asumiendo que "id_estado = 1" representa usuarios activos)
            $sql = $con->prepare("SELECT COUNT(*) as usuarios_activos FROM usuario WHERE id_estado = 1");
            $sql->execute();
            $usuariosActivos = $sql->fetch(PDO::FETCH_ASSOC)['usuarios_activos'];

            // Promedio de nivel de los jugadores
            $sql = $con->prepare("SELECT AVG(id_nivel) as id_nivel FROM deta_niv");
            $sql->execute();
            $promedioNivel = round($sql->fetch(PDO::FETCH_ASSOC)['id_nivel'], 2);

            // Jugador con el nivel más alto
            $sql = $con->prepare ("SELECT usuario.user_name, rol.id_rol FROM usuario 
                                  INNER JOIN rol ON usuario.id_rol = rol.id_rol 
                                  ORDER BY rol.id_rol
                                  ");
            $sql->execute();
            $topJugador = $sql->fetch(PDO::FETCH_ASSOC);

            // Total de partidas jugadas
            $sql = $con->prepare("SELECT COUNT(*) as total_partidas FROM sala");
            $sql->execute();
            $totalPartidas = $sql->fetch(PDO::FETCH_ASSOC)['total_partidas'];
            
            
            
            ?>


            <div class="tarjeta">
                <h3>👥 Total de Usuarios</h3>
                <p><?php echo $totalUsuarios; ?></p>
            </div>

            <div class="tarjeta">
                <h3>✅ Usuarios Activos</h3>
                <p><?php echo $usuariosActivos; ?></p>
            </div>

            <div class="tarjeta">
                <h3>📈 Promedio de Nivel</h3>
                <p><?php echo $promedioNivel; ?></p>
            </div>

            <div class="tarjeta">
                <h3>🏆 Jugador con Mayor Nivel</h3>
                <p><?php echo htmlspecialchars($topJugador['user_name'] ?? 'N/A'); ?> - Nivel <?php echo $topJugador['nivel_actual'] ?? '0'; ?></p>
            </div>

            <div class="tarjeta">
                <h3>🎮 Partidas Jugadas</h3>
                <p><?php echo $totalPartidas; ?></p>
            </div>

        </div>
    </div>
</body>
</html>

<style>
/* Estilos para las estadísticas */
.estadisticas {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
}

.tarjeta {
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    flex: 1 1 200px;
}

.tarjeta h3 {
    margin: 0 0 10px;
    font-size: 1.2em;
}

.tarjeta p {
    font-size: 1.5em;
    font-weight: bold;
    margin: 0;
}
</style>
