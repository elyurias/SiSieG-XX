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

?>
<!DOCTYPE html>
<html>
<header>
<title><?php echo $sistema." ".$nombre?></title>
<link rel="shortcut icon" href="../img/saericon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
<link rel="stylesheet" type="text/css" href="../estilos/menu.css">
<link rel="stylesheet" type="text/css" href="../estilos/formulario.css">
<link rel="stylesheet" type="text/css" href="../estilos/tablas.css">
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
    <form onSubmit="return valida(this)" action="/SAER/acciones/adminactions/descargararchivos.php" method="POST" class="form-iniciar">
    <h2>DESCARGAR ARCHIVOS POR CICLO</h2>
    <center><h6>*Recuerda que estó puede tomar un tiempo*</h6></center>
    <br>
    <br>
    <select name='ciclo' required>
        <option value="">Seleccionar Ciclo</option>
        <?php
        $materia = "SELECT * FROM ciclos where idciclo != '' ORDER BY idciclo ASC ";
        $runmateria = mysqli_query($conn, $materia);

        while($row = mysqli_fetch_row($runmateria)){
          ?>
          <option value="<?php echo $row[0]?>"><?php echo $row[0]?></option>
          <?php
        }
        ?>
    </select>
    <input type="submit" name="submit" id="botonazo" value="DESCARGAR ARCHIVOS"/>
    </form>
</body>
</html>
<?php
      
    }else{
      header('location: ../../index.php');
    }
?>