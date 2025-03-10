<?php
session_start();
require 'config/database.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kumite Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Iniciar Sesión</h2>
        <form id="loginForm" method="POST" action="include/procesarlogin.php">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario" id="usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="contra" id="contra" class="form-control" required>

            </div>

            


            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
              <div class="position-absolute top-0 start-0 mt-3 ms-3">
    <a href="index.php" class="btn btn-primary">Volver</a>
</div>
        

              <!-- Botón para recuperar contraseña -->
              <label><a href="recovery.php" class="btn btn-outline-secondary w-100 mt-2">¿Olvidaste tu contraseña?</a></label>
              <div id="mensaje" class="mt-3 text-center"></div>
            </form>
            <div id="mensaje" class="mt-3 text-center"></div>    
        </div>
    
    <script src="js/validacionlogin.js"></script>
</body>
</html>
