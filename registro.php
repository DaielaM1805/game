<?php
require 'config/database.php'; 
$db = new Database();
$con = $db->conectar(); 

$sql = "SELECT id_avatar, nom_avatar, img_avatar FROM avatar";
$result = $con->query($sql);
$avatares = [];

if ($result->rowCount() > 0) {  
    $avatares = $result->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST['id_usuario'];
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $contra = password_hash($_POST['contra'], PASSWORD_BCRYPT);
    $id_avatar = $_POST['id_avatar'];
    $id_estado = 1;
    $id_rol = 2;
    $sql = "INSERT INTO usuario (id_usuario, user_name, email, contra, id_avatar, id_estado, id_rol) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $con->prepare($sql); 
    $stmt->execute([$id_usuario, $user_name, $email, $contra, $id_avatar, $id_estado, $id_rol]);

    echo json_encode(["status" => "success", "message" => "Registro exitoso. Ahora puedes iniciar sesión."]);
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - kumite Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
        background-image: url('img/fondo.jpg'); /* Ruta de la imagen */
        background-size: cover; /* Ajusta la imagen para cubrir la pantalla */
        background-position: center; /* Centra la imagen */
        background-repeat: no-repeat; /* No repetir la imagen */
        background-attachment: fixed; /* Mantiene la imagen fija al hacer scroll */
        }

    .avatar {
        width: 120px;
        height: auto; /* Mantiene la proporción */
        max-height: 120px; /* Limita la altura */
        object-fit: contain; /* Evita recortes */
        cursor: pointer;
        border: 3px solid transparent;
        border-radius: 10px;
        transition: transform 0.2s ease, border 0.2s ease;
        }

        .avatar-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            align-items: center; /* Centra las imágenes */
        }


        .avatar:hover {
            transform: scale(1.1); /* Efecto de agrandamiento */
        }

        .selected {
            border: 4px solid #007bff; /* Azul Bootstrap */
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }

        /* Texto del avatar seleccionado */
        #avatar_name {
            font-weight: bold;
            color: #007bff;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Registro</h2>
        <form id="registroForm" method="POST">
            <div class="mb-3">
                <label class="form-label">Documento de usuario</label>
                <input type="number" name="id_usuario" id="id_usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre de usuario</label>
                <input type="text" name="user_name" id="user_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="contra" id="contra" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirma Contraseña</label>
                <input type="password" id="contra2" class="form-control" required>
            </div>
            <div class="mb-3">
            <label >Selecciona un Avatar</label>

            <div class="avatar-container">
            <?php foreach ($avatares as $avatar): ?>
            <img src="<?php echo $avatar['img_avatar']; ?>" 
             alt="<?php echo $avatar['nom_avatar']; ?>" 
             class="avatar"
             data-id="<?php echo $avatar['id_avatar']; ?>">
            <?php endforeach; ?>
            </div>

            <input type="hidden" name="id_avatar" id="id_avatar">
            <p class="text-center mt-3"><strong>Avatar seleccionado:</strong> <span id="avatar_name">Ninguno</span></p>
                 
            </div>

            <button type="submit" class="btn btn-primary w-100">Registrarse</button>

            <div class="text-center mt-3">
                <a href="login.php">Iniciar sesión</a>
            </div>
            <div class="position-absolute top-0 start-0 mt-3 ms-3">
    <a href="index.php" class="btn btn-primary">Volver</a>
</div>



        </form>
        <div id="mensaje" class="mt-3 text-center"></div>
    </div>

    <script src="js/validacionregistro.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    document.querySelectorAll('.avatar').forEach(img => {
    img.addEventListener('click', function () {
        // Quitar selección de todos
        document.querySelectorAll('.avatar').forEach(i => i.classList.remove('selected'));
        
        // Agregar selección al clic
        this.classList.add('selected');
        
        // Guardar el ID en el campo oculto
        document.getElementById('id_avatar').value = this.getAttribute('data-id');
        
        // Cambiar texto del avatar seleccionado
        document.getElementById('avatar_name').innerText = this.alt;
        
    });
});

</script>
</body>
</html>
