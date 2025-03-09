<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jugador - Lobby</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../img/lobbyjugador.png') no-repeat center center fixed;
            background-size: cover;
        }
        .card {
            background: rgba(0, 0, 0, 0.7);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 text-center" style="width: 22rem;">
            <img id="avatar" class="rounded-circle mx-auto d-block" width="100">
            <h3 id="username" class="mt-3">Cargando...</h3>
            <p>Nivel: <span id="nivel">0</span></p>
            <p>Puntos: <span id="puntos">0</span></p>
            <button class="btn btn-primary" id="iniciarPartida">Iniciar Partida</button>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        fetch("../include/infojugador.php")
            .then(response => response.text())
            .then(text => {
                console.log("Respuesta del servidor:", text);
                try {
                    const jsonData = JSON.parse(text);
                    if (jsonData.error) {
                        console.error("Error en la respuesta:", jsonData.error);
                        return;
                    }

                    document.getElementById("username").textContent = jsonData.username;
                    document.getElementById("puntos").textContent = jsonData.puntos;
                    document.getElementById("avatar").src = `../img/avatares/${jsonData.avatar}`;

                } catch (error) {
                    console.error("Error al parsear JSON:", error);
                }
            });
    });

    document.getElementById("iniciarPartida").addEventListener("click", () => {
        window.location.href = "seleccion.php";
    });
    </script>
</body>
</html>
