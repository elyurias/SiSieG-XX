<?php
    session_start();
    include('../vista/conexion.php');
    if(isset($_POST['enviar'])) { // comprobamos que se hayan enviado los datos del formulario
        // comprobamos que los campos usuarios_nombre y usuario_clave no estén vacíos
        if(empty($_POST['usuario_matricula']) || empty($_POST['usuario_clave'])) {
            echo "El usuario o la contraseña no han sido ingresados. <a href='javascript:history.back();'>Reintentar</a>";
        }else {
            // "limpiamos" los campos del formulario de posibles códigos maliciosos
            $usuario_matricula = mysqli_real_escape_string($conn, $_POST['usuario_matricula']);
            $usuario_clave = mysqli_real_escape_string($conn, $_POST['usuario_clave']);
            //$usuario_clave = md5($usuario_clave);
            // comprobamos que los datos ingresados en el formulario coincidan con los de la BD
            $sql = mysqli_query($conn, "SELECT * FROM docente WHERE usuario='".$usuario_matricula."' AND contrasena='".$usuario_clave."'");
            if($row = mysqli_fetch_array($sql)) {
                // comprobamos que tenga asignados algun tipo de permisos
                if ($row['permisos'] == 777 || $row['permisos'] == 600 || $row['permisos'] == 660) {
                    //El permiso 660, es el nuevo usuario que se generara para los jefes de carrera,
                    //Es bastante curioso que cuando garduño se fue, aceptaron integrar este proyecto a toda la carrera, lamentable
                    //:v malas ideas diria yo jaja
                   $_SESSION['matricula'] = $row['matricula']; // creamos la sesion "usuario_id" y le asignamos como valor el campo usuario_id
                   $_SESSION['nombre'] = $row['nombre']; // creamos la sesion "usuario_nombre" y le asignamos como valor el campo usuario_nombre
                   $_SESSION['permisos'] = $row['permisos']; // creamos la sesion "usuario_permisos" y le asignamos como valor el campo usuario_permisos
                   $_SESSION['id_carrera'] = $row['id_carrera']; // es importante saber a que carrera corresponde el usuario, esto puede mejorarse
                   //se presenta de esta forma ya que bueno... solo tenemos 2 dias para entregarlo jajaja....
                   //en todo caso yo recomendaria re estructurar todo el sistema para que funcione en mvc
                   //si nos vamos mas lejos, con NodeJS + MongoDB, eso estaria mejor, y los sistemas seria mas seguros, pero ignorenme
                   //estoy divagando.... dibagando... naa jajaja
                   //una clase que maneje las sesiones es super importante, esta cosa va a tronar con todos los demas sercicios si se deja de esta forma....
                   //es evidente que esta pagina no considero que los sistemas tengan otras carreras relacionadas
                   //imagina que existe en el mismo web service una variable matricula igual, te va a dejar pasar sin pensar jajaja..
                    if ($_SESSION['permisos'] == 600) {
                        header("Location: ./../vista/docente/subirarchivos.php");
                    }else {
                        header("Location: ./../vista/administrador/administrador.php");
                    }
                    exit();
                }else { ?>
                    Error parece que no tienes ningun tipo de permisos para acceder, <a href="../index.php">Reintentar</a>
                    <?php
                }
            }else  {
    echo "          
<script type='text/javascript'>  alert('USUARIO o PASSWORD Incorrectos!');
  window.location='../index.php';
</script> ";
        }
        }
    }else {
        header("Location: ../index.php");
    }
?>