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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-dark text-white">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2>📊 Estadísticas Generales</h2>
            <a href="index.php" class="btn btn-primary">Volver</a>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-dark table-striped text-center">
                <thead>
                    <tr>
                        <th>👥 Usuario</th>
                        <th>🛡️ Tipo de Usuario</th>
                        <th>🔹 Estado</th>
                        <th>📅 Última Actividad</th>
                        <th>📈 Nivel Actual</th>
                        <th>🌎 Mundo Actual</th>
                        <th>🎮 Partidas Jugadas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtener datos de los jugadores con su tipo de usuario, nivel y partidas jugadas
                    $sql = $con->prepare("
                        SELECT 
                            u.user_name, 
                            r.nom_rol AS tipo_usuario,
                            CASE 
                                WHEN u.id_estado = 1 AND u.ultima_sesion >= NOW() - INTERVAL 10 DAY 
                                    THEN 'Activo' 
                                ELSE 'Inactivo (+10 días)' 
                            END AS estado,
                            u.ultima_sesion AS ultima_actividad,
                            COALESCE(MAX(d_n.id_nivel), 'Sin Nivel') AS nivel_actual,
                            COALESCE(MAX(m.nom_mundo), 'Sin Mundo') AS mundo_actual,
                            COUNT(s.id_sala) AS partidas_jugadas
                        FROM usuario u
                        LEFT JOIN rol r ON u.id_rol = r.id_rol
                        LEFT JOIN deta_niv d_n ON u.id_usuario = d_n.id_usuario
                        LEFT JOIN sala s ON d_n.id_nivel = s.id_nivel
                        LEFT JOIN mundos m ON s.id_mundo = m.id_mundo
                        GROUP BY u.id_usuario
                    ");
                    $sql->execute();
                    $usuarios = $sql->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($usuarios as $usuario) {
                        echo "<tr>
                                <td>{$usuario['user_name']}</td>
                                <td>{$usuario['tipo_usuario']}</td>
                                <td>{$usuario['estado']}</td>
                                <td>{$usuario['ultima_actividad']}</td>
                                <td>{$usuario['nivel_actual']}</td>
                                <td>{$usuario['mundo_actual']}</td>
                                <td>{$usuario['partidas_jugadas']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
