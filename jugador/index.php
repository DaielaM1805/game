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
            <img id="avatar" src="../img/avatar.png" class="rounded-circle mx-auto d-block" width="100">
            <h3 id="username" class="mt-3">Cargando...</h3>
            <p>Nivel: <span id="nivel">0</span></p>
            <p>Puntos: <span id="puntos">0</span></p>
            <button class="btn btn-primary" id="iniciarPartida">Iniciar Partida</button>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        fetch("../include/infojugador.php")
            .then(response => response.text()) // <-- Cambio a .text() para ver el contenido
            .then(data => {
                console.log("Respuesta del servidor:", data); // <-- Verifica qué devuelve el servidor
                try {
                    const jsonData = JSON.parse(data); // Intentamos convertirlo en JSON
                    document.getElementById("username").textContent = jsonData.username;
                    document.getElementById("nivel").textContent = jsonData.nivel;
                    document.getElementById("puntos").textContent = jsonData.puntos;
                    document.getElementById("avatar").src = "../img/avatares/" + jsonData.avatar;
                } catch (error) {
                    console.error("Error al parsear JSON:", error);
                }
            });
    });

    document.getElementById("iniciarPartida").addEventListener("click", () => {
        fetch("../include/buscar_sala.php")
            .then(response => response.json())
            .then(data => {
                if (data.sala) {
                    window.location.href = "../partidas/sala.php?id=" + data.sala;
                } else {
                    alert("No se pudo encontrar o crear una sala.");
                }
            });
    });
</script>

</body>
</html>
