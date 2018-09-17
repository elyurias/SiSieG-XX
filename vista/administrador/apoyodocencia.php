<?php
        session_start();
        // comprobamos que se haya iniciado la sesión
        if(isset($_SESSION['nombre']) && $_SESSION['permisos'] == 777 || $_SESSION['permisos'] == 660) {
        $nombre = $_SESSION['nombre'];
        $matricula = $_SESSION['matricula'];
        include_once "../conexion.php";

    $ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
    $cicl = mysqli_query($conn, $ci);
    $raa = mysqli_fetch_row($cicl);
    $escolar = $raa[0];
    if($_SESSION['permisos']==660){
      $carrera = $_SESSION['id_carrera'];
    }else{
      if(isset($_GET['carrera'])){
        $carrera = $_GET['carrera'];
      }else{
        exit();
      }
    }
?>
<!DOCTYPE html>
<html>
<header>
<title><?php echo $sistema." ".$nombre?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
<link rel="stylesheet" type="text/css" href="../estilos/menu.css">
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="../js/menu.js"></script>
<nav id='cssmenu'>
<div class="logo"><a href="administrador.php" class="telme"><?php echo $sistema." ".$nombre?></a></div>
<div id="head-mobile"></div>
<div class="button"></div>
<ul>
<!--<li class='active'><a href='docente.php'>HOME</a></li>-->
<!--No se hace de esa forma tonto e.e-->
<li><a href='#'>DOCENTES</a>
  <ul>
        <?php
            echo $cadena_carreras;
        ?>
   </ul>
</li>
<li><a href='#'>ASIGNAR</a>
   <ul>
      <li><a href='materias.php'>MATERIAS</a></li>
      <li><a href='#'>APOYO DOCENCIA</a>
        <ul>
          <?php
              echo $cadena_carreras_apoyo;
          ?>
        </ul>
      </li>
   </ul>
</li>
<li><a href='#'>REPORTE GENERAL</a>
   <ul>
      <li><a href='../../acciones/adminactions/pdfgeneralDos.php'>PDF</a></li>
      <li><a href='../../acciones/adminactions/excelgeneral.php'>EXCEL</a></li>
   </ul>
</li>
<li><a href='descargar.php'>DESCARGAR</a></li>
<li><a href='../../logout.php'>CERRAR SESIÓN</a></li>
</ul>
</nav>
</header>
<body>
  <link rel="stylesheet" href="../estilos/formulario.css">
    <form onSubmit="return valida(this)" action="/SAER/acciones/adminactions/asignarapoyo.php" method="POST" class="form-iniciar">
            <h2>Asignar Apoyo a la Docencia</h2>
            <select name='docente' required>
                <option value="">Seleccionar Docente</option>
                <?php
                $profe = "SELECT * FROM docente WHERE id_carrera = $carrera and idstatus = 1 AND permisos = 600 ORDER BY paterno";
                $runprofe = mysqli_query($conn, $profe);

                while($raw = mysqli_fetch_row($runprofe)){
                  ?>
                  <option value="<?php echo $raw[0]?>"><?php echo $raw[2].' '.$raw[3].' '.$raw[1]?></option>
                  <?php
                }
                ?>
                <!-- APOYOS A DOCENTES YA QUE NO ESTAN DENTRO DE LA BASE SE SELECCIONAN CAVERNICOLAMENTE -->
            </select>
            <select name='apoyo'>
                <option value="">Seleccionar Apoyo</option>
                <option value="LINEA DE INVESTIGACIÓN">INVESTIGACIÓN</option>
                <option value="TUTOR">TUTOR</option>
                <option value="TUTORIAS DE CASOS ESPECIALES">TUTORIAS DE CASOS ESPECIALES</option>
                <option value="ASESOR DE RESIDENCIAS PROFESIONALES">ASESOR DE RESIDENCIAS PROFESIONALES</option>
                <option value="SOPORTE TECNICO DEL MODELO SEMIPRESENCIAL">SOPORTE TECNICO DEL MODELO SEMIPRESENCIAL</option>
                <option value="SISTEMA DE EVALAUACION DOCENTE ">SISTEMA DE EVALAUACION DOCENTE </option>
                <option value="SECREATRIA DE ACADEMIA ">SECREATRIA DE ACADEMIA </option>
                <option value="PROYECTO INTEGRADOR">PROYECTO INTEGRADOR</option>
                <option value="PRESIDENTE DE ACADEMIA ">PRESIDENTE DE ACADEMIA </option>
                <option value="PLATAFORMA MOODLE">PLATAFORMA MOODLE</option>
                <option value="OLIMPIADA DE CIENCIAS BASICAS">OLIMPIADA DE CIENCIAS BASICAS</option>
                <option value="OBTENCION PERFIL PRODEP">OBTENCION PERFIL PRODEP</option>
                <option value="FORMACION DE CUERPOS ACADEMICOS ">FORMACION DE CUERPOS ACADEMICOS </option>
                <option value="ESTUDIO DE FACTIBILIDAD PARA LA NUEVA ESPECIALIDAD ">ESTUDIO DE FACTIBILIDAD PARA LA NUEVA ESPECIALIDAD </option>
                <option value="COORDINADOR MODELO DUAL">COORDINADOR MODELO DUAL</option>
                <option value="COORDINADOR DE LA JORNADA DE EMPRENDURISMO">COORDINADOR DE LA JORNADA DE EMPRENDURISMO</option>
                <option value="COORDINADOR DE ACTIVIDADES COMPLEMENTARIAS ">COORDINADOR DE ACTIVIDADES COMPLEMENTARIAS </option> 
                <option value="LINEA DE INVESTIGACION">LINEA DE INVESTIGACION</option>
            </select>
            <input type="text" autocomplete="off" name="otroapoyo" placeholder="Otro"/>
            <input type="text" value="<?php echo $escolar?>" name="ciclo" readonly/>
            <input type="hidden" value="<?php echo $carrera?>" name="id_carrera" readonly/>
            <select name='grupo' required>
                <option value="">Seleccionar Grupo</option>
                <option value="1111">N/A</option>
                <?php
                $grupo = "SELECT * FROM grupo WHERE  id_carrera = $carrera and  idgrupo <> 1111";
                $rungrupo = mysqli_query($conn, $grupo);

                while($rew = mysqli_fetch_row($rungrupo)){
                  ?>
                  <option value="<?php echo $rew[0]?>"><?php echo $rew[0]?></option>
                  <?php
                }
                ?>
            </select>
            <input type="submit" name="submit" id="botonazo" value="Asignar Apoyo"/>
    </form>
</body>
</html>
<?php
      
    }else{
      header('location: /SAER/index.php');
    }
?>