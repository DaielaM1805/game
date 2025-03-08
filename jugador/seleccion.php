<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección - Juego</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: url('/game/img/lobbyjugador.png') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }
        .card {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .world-card, .weapon-card {
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .world-card:hover, .weapon-card:hover {
            transform: scale(1.02);
        }
        .world-card .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        .world-card:hover .card-img-top {
            transform: scale(1.1);
        }
        .world-card .card-body {
            position: relative;
            z-index: 2;
        }
        .world-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            pointer-events: none;
        }
        .selected {
            border: 3px solid #0d6efd;
            box-shadow: 0 0 15px rgba(13, 110, 253, 0.5);
        }
        #weaponSection {
            display: none;
        }
        .weapon-card.blocked {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .card-img-container {
            position: relative;
            overflow: hidden;
        }
        .blocked-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
        }
        .blocked-overlay i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .nivel-requerido {
            margin: 0;
            text-align: center;
            padding: 5px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 5px;
        }
        .world-description {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-top: 5px;
        }
        .section-title {
            text-align: center;
            margin-bottom: 30px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .section-title p {
            font-size: 1.1rem;
            opacity: 0.8;
        }
        .alert-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: none;
        }
        #loadingSpinner {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            padding: 2rem;
            border-radius: 10px;
            display: none;
            z-index: 1000;
            text-align: center;
            color: white;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="section-title">
                    <h2>Selecciona tu Mundo</h2>
                    <p>Elige el escenario donde se desarrollará tu batalla</p>
                </div>
            </div>
            <div class="col-md-8 mx-auto">
                <input type="hidden" id="id_mundo" name="id_mundo" value="">
                <input type="hidden" id="id_arma" name="id_arma" value="">
                
                <div class="row" id="worldsContainer">
                    <!-- Los mundos se cargarán dinámicamente -->
                </div>

                <div id="weaponSection" class="mt-5">
                    <div class="section-title">
                        <h2>Selecciona tu Arma</h2>
                        <p>Elige sabiamente tu arma para la batalla</p>
                    </div>
                    <div class="row" id="weaponsContainer">
                        <!-- Las armas se cargarán dinámicamente -->
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="button" 
                            id="btnContinuar" 
                            class="btn btn-primary btn-lg" 
                            style="display: none;">
                        Continuar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-danger alert-floating" id="errorAlert" role="alert"></div>
    <div id="loadingSpinner">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <h5 class="mb-0" id="loadingText">Buscando sala disponible...</h5>
    </div>

    <script>
        let select_mundo = null;
        let select_arma = null;

        // Agregar el event listener después de que el DOM esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btnContinuar').addEventListener('click', enviarDatos);
        });

        function mostrarError(mensaje) {
            const alert = $('#errorAlert');
            alert.text(mensaje);
            alert.fadeIn();
            setTimeout(() => alert.fadeOut(), 3000);
        }

        function mostrarCargando(mensaje) {
            $('#loadingText').text(mensaje);
            $('#loadingSpinner').fadeIn();
        }

        function ocultarCargando() {
            $('#loadingSpinner').fadeOut();
        }

        async function enviarDatos(event) {
            if (event) event.preventDefault();
            
            try {
                if (!select_mundo || !select_arma) {
                    mostrarError('Por favor selecciona un mundo y un arma');
                    return;
                }

                mostrarCargando('Enviando datos...');

                const params = new URLSearchParams();
                params.append('id_mundo', select_mundo);
                params.append('id_arma', select_arma);

                const response = await fetch('../include/buscarsala.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: params
                });

                const data = await response.json();
                console.log('Respuesta:', data);

                if (data.success) {
                    mostrarCargando('Redirigiendo a sala...');
                    setTimeout(() => {
                        window.location.href = data.redirect_url || 'sala.php';
                    }, 1000);
                } else {
                    ocultarCargando();
                    mostrarError(data.mensaje || 'Error al buscar sala');
                }
            } catch (error) {
                console.error('Error:', error);
                ocultarCargando();
                mostrarError('Error al conectar con el servidor');
            }
        }

        function selectWorld(id, element) {
            select_mundo = id;
            document.getElementById('id_mundo').value = id;
            document.querySelectorAll('.world-card').forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('weaponSection').style.display = 'block';
            checkContinueButton();
        }

        function selectWeapon(id, element) {
            select_arma = id;
            document.getElementById('id_arma').value = id;
            document.querySelectorAll('.weapon-card').forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            checkContinueButton();
        }

        function checkContinueButton() {
            const btnContinuar = document.getElementById('btnContinuar');
            if (select_mundo && select_arma) {
                btnContinuar.style.display = 'inline-block';
            } else {
                btnContinuar.style.display = 'none';
            }
        }

        // Cargar mundos
        fetch('../include/get_mundos.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('worldsContainer');
                data.forEach(mundo => {
                    const div = document.createElement('div');
                    div.className = 'col-md-6 mb-4';
                    div.innerHTML = `
                        <div class="card world-card" onclick="selectWorld(${mundo.id_mundo}, this)">
                            <div class="card-img-container">
                                <img src="../${mundo.img_mundo}" class="card-img-top" alt="${mundo.nom_mundo}">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${mundo.nom_mundo}</h5>
                            </div>
                        </div>
                    `;
                    container.appendChild(div);
                });
            });

        // Cargar armas
        fetch('../include/get_armas.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('weaponsContainer');
                data.forEach(arma => {
                    const div = document.createElement('div');
                    div.className = 'col-md-4 mb-4';
                    div.innerHTML = `
                        <div class="card weapon-card" onclick="selectWeapon(${arma.id_arma}, this)">
                            <div class="card-img-container">
                                <img src="../${arma.img_arma}" class="card-img-top" alt="${arma.nom_arma}">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${arma.nom_arma}</h5>
                                <p class="card-text">Daño: ${arma.dano}</p>
                            </div>
                        </div>
                    `;
                    container.appendChild(div);
                });
            });
    </script>
</body>
</html>