<?php
require 'config/database.php'; // Archivo con la conexión a la base de datos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kumite game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-image: url('img/fondo.jpg'); /* Ruta de la imagen de fondo */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(0, 0, 0, 0.7); /* Fondo semi-transparente para mejor legibilidad */
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            max-width: 500px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container text-center mt-5">
        <h1>Bienvenido a kumite Game</h1>
        <p>Sumérgete en una batalla épica. Inicia sesión o regístrate para jugar.</p>
        <a href="login.php" class="btn btn-success">Iniciar Sesión</a>
        <a href="registro.php" class="btn btn-warning">Registrarse</a>
    </div>
</body>
</html>
