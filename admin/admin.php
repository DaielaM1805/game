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
    <link rel="stylesheet" href="../css/switch.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
   <div><br>
       <h1 class="text-center tit">LISTA DE TODOS LOS JUGADORES</h1>
       <div class="container table-responsive">
           <div>.</div>

           <style>
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
               th, td {
                   padding: 8px;
                   text-align: center;
               }
           </style>

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
               <a class="btn btn-danger" href="../include/sali.php">Cerrar Sesión</a>
           </div><br>
       </div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</body>
</html>
