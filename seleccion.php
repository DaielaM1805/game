<?php
// Conexión a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'bd_juego');

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener mundos
$query_mundos = "SELECT * FROM mundos";
$result_mundos = $conexion->query($query_mundos);

// Obtener armas
$query_armas = "SELECT arma.*, tipo_arma.dano FROM arma INNER JOIN tipo_arma ON arma.id_tipo = tipo_arma.id";
$result_armas = $conexion->query($query_armas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Selección de Mundo y Arma</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        select, button { width: 100%; padding: 10px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Selecciona tu Mundo y Arma</h1>
        <form action="sala.php" method="post">
            <label for="mundo">Mundo:</label>
            <select name="mundo" id="mundo" required>
                <?php while($mundo = $result_mundos->fetch_assoc()): ?>
                    <option value="<?php echo $mundo['id']; ?>"><?php echo $mundo['nombre']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="arma">Arma:</label>
            <select name="arma" id="arma" required>
                <?php while($arma = $result_armas->fetch_assoc()): ?>
                    <option value="<?php echo $arma['id']; ?>"><?php echo $arma['nombre']; ?> (Daño: <?php echo $arma['dano']; ?>)</option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Entrar a la Sala</button>
        </form>
    </div>
</body>
</html>
