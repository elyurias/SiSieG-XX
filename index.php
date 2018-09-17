<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>SiSigE-XXI</title>
    <!--CSS-->
    <link rel="stylesheet" type="text/css" href="vista/estilos/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="vista/estilos/bootstrap-responsive.css">
    <!--Javascript-->
    <script type="text/javascript" language="javascript" src="vista/js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" language="javascript" src="vista/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="md-12 text-center" style="margin-top: 50px;">
            <img src="vista/img/logo_isc.png" alt="SAER" height="250" width="250">
        </div>
        <div class="row-fluid">
            <div class="span4">
            </div>
            <div class="span4">
                <?php
      if(empty($_SESSION['nombre'])) { // comprobamos que las variables de sesión estén vacías
  ?>
                    <form class="form-signin" action="./acciones/comprobar.php" method="post">
                       <br>
                       <div class="input-group">
                         <span class="input-group-addon"><i class="fa fa-user fa-2x" aria-hidden="true"></i></span>
                        <input type="text" name="usuario_matricula" class="form-control btn-block" placeholder="Usuario"  required autofocus />
                       </div>
                        <br>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-key fa-2x" aria-hidden="true"></i></span>
                          <input type="password" name="usuario_clave" class="form-control btn-block" placeholder="Contraseña" required/>
                        </div>
                        <br>
                        <input type="submit" name="enviar" value="Iniciar sesión" class="btn btn-lg btn-default btn-block" />
                    </form>
                    <br>                  
                    
                    <?php
      }else {
         if ($_SESSION['permisos'] == 600 ) {
               header("location: ./vista/docente/subirarchivos.php");
           }else {
                header("location: ./vista/administrador/administrador.php");
           }
        }
  ?>
              <footer class="mainFooter" align="center">
                  <br>
                 <p align="center">&copy; TESCHA - INGENIERIA EN SISTEMAS COMPUTACIONALES - All rights reserved. <br> SISTEMA DE ENTREGA DE AVANCES PROGRAMATICOS SEMESTRALES
              </footer>
            </div>
        </div>
    </div>
</body>

</html>
