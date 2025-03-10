<?php
require_once('../config/database.php');
$conex = new database();
$con = $conex->conectar();
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
         body{
            background-image: url(../img/fondo_admin.png);
            background-size: cover;
            height: 100vh; 
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: collapse
         }
         h1 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
         }
        .table {
            font-size: 14px;
            border-radius: 20px;
            
        }
        .table-rounded {
            border-collapse: collapse;
            border-spacing: 0 -2px;
        }
        .table-rounded th, .table-rounded td {
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }
        th,tr, td {
            padding: 8px;
            text-align: center;
            border-collapse: collapse;
            border: solid 3px yellow;
        }

        /* Estilos del switch */
        .switch {
            position: relative;
            height: 1.5rem;
            width: 3rem;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            border-radius: 9999px;
            background-color: rgba(100, 116, 139, 0.377);
            transition: all .3s ease;
        }

        .switch:checked {
            background-color: rgb(25, 125, 240);
        }

        .switch::before {
            position: absolute;
            content: "";
            left: calc(1.5rem - 1.6rem);
            top: calc(1.5rem - 1.6rem);
            display: block;
            height: 1.6rem;
            width: 1.6rem;
            cursor: pointer;
            border: 1px solid rgba(100, 116, 139, 0.527);
            border-radius: 9999px;
            background-color: rgba(255, 255, 255, 1);
            box-shadow: 0 3px 10px rgba(100, 116, 139, 0.327);
            transition: all .3s ease;
        }

        .switch:hover::before {
            box-shadow: 0 0 0px 8px rgba(0, 0, 0, .15);
        }

        .switch:checked:hover::before {
            box-shadow: 0 0 0px 8px rgba(236, 72, 153, .15);
        }

        .switch:checked:before {
            transform: translateX(100%);
            border-color: rgb(25, 125, 240);
        }
    </style>
</head>
<body>
   <div><br>
       <h1 class="text-center tit">LISTA DE TODOS LOS JUGADORES</h1>
       <div class="container table-responsive">
           <div>.</div>

           <table class="table table-light table-bordered border-secondary table-rounded">
               <thead class="table-dark">
                   
               <tr>
                       <th>Documento</th>
                       <th>Nombre de usuario</th>
                       <th>Correo</th>
                       <th>Rol</th>
                       <th>Estado</th>
                       <th>Editar</th>
                       <th>Eliminar</th>
                   </tr>
               </thead>
               <tbody>
                   <?php
                    $sql = "SELECT u.id_usuario, u.user_name, u.email, r.nom_rol, e.estado 
                            FROM usuario u
                            INNER JOIN estado e ON u.id_estado = e.id_estado
                            INNER JOIN rol r ON u.id_rol = r.id_rol";

                   $stmt = $con->query($sql);
                   $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                   foreach ($usuarios as $usuario) {
                   ?>
                       <tr>
                           <td><?php echo $usuario['id_usuario']; ?></td>
                           <td><?php echo $usuario['user_name']; ?></td>
                           <td><?php echo $usuario['email']; ?></td>
                           <td><?php echo $usuario['nom_rol']; ?></td>
                           <td>
                               <form id="estadoForm<?php echo $usuario['id_usuario']; ?>" action="procesar_usuario.php" method="post">
                                   <input type="hidden" name="usuario_id" value="<?php echo $usuario['id_usuario']; ?>">
                                   <input class="switch" type="checkbox" id="estadoCheckbox<?php echo $usuario['id_usuario']; ?>" 
                                          name="estado" <?php echo ($usuario['estado'] == 'Activo') ? 'checked' : ''; ?>>
                               </form>
                           </td>

                           <script>
                               document.getElementById('estadoCheckbox<?php echo $usuario['id_usuario']; ?>').addEventListener('click', function() {
                                   document.getElementById('estadoForm<?php echo $usuario['id_usuario']; ?>').submit();
                                   this.disabled = true;
                               });
                           </script>

                           <td>
                               <div class="btn btn-success btn-sm">
                                   <a href="actualizar.php?id_usuario=<?php echo $usuario['id_usuario']; ?>" 
                                      onclick="window.open(this.href, '', 'width=800, height=500'); return false;">
                                      Editar
                                   </a>  
                               </div>
                           </td>
                           <td>
                               <div class="btn btn-danger btn-sm">
                                   <a href="eliminar.php?id_usuario=<?php echo $usuario['id_usuario']; ?>" 
                                      onclick="return confirm('¿Desea eliminar el registro?');">
                                      Eliminar
                                   </a>
                               </div>
                           </td>
                       </tr>
                   <?php } ?>
               </tbody>
           </table>

           <div class="text-center">
               <a class="btn btn-danger" href="../include/salir.php">Cerrar Sesión</a>
               <div class="position-absolute top-0 start-0 mt-3 ms-3">
                   <a href="index.php" class="btn btn-primary">Volver</a>
               </div>
           </div><br>
       </div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</body>
</html>
