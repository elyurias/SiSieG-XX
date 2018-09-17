<?php
        session_start();
        // comprobamos que se haya iniciado la sesión
        if(isset($_SESSION['nombre']) && $_SESSION['permisos'] == 600) {
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
<link rel="stylesheet" type="text/css" href="../estilos/menu.css">
<link rel="stylesheet" href="../estilos/tablas.css">
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="../js/menu.js"></script>
<nav id='cssmenu'>
<div class="logo"><a href="docente.php">S<?php echo $sistema." ".$nombre?></a></div>
<div id="head-mobile"></div>
<div class="button"></div>
<ul>
<!--<li class='active'><a href='docente.php'>HOME</a></li>-->
<li><a href='vermaterias.php'>MATERIAS/ARCHIVOS <?php echo $escolar;?></a></li>
<li><a href='subirarchivos.php'>SUBIR <?php echo $escolar;?></a></li>
<li><a href='#'>REPORTE DE ARCHIVOS</a></li>
<li><a href='../../logout.php'>CERRAR SESIÓN</a></li>
</ul>
</nav>
</header>
<body>
<?php
echo $matricula;
?>
</body>
</html>
<?php
      
    }else{
      header('location: /SAER/index.php');
    }
?>