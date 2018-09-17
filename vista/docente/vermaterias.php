<?php
        session_start();
        // comprobamos que se haya iniciado la sesión
        if(isset($_SESSION['nombre']) && $_SESSION['permisos'] == 600) {
        $nombre = $_SESSION['nombre'];
        $matricula = $_SESSION['matricula'];
        include_once "../conexioninsertar.php";

        $ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
        $cicl = mysqli_query($conn, $ci);
        $raa = mysqli_fetch_row($cicl);
        $escolar = $raa[0];

        $falta = 'REPORTE DE ARCHIVOS';
        $cerrar = 'CERRAR SESIÓN';
?>
<!DOCTYPE html>
<html>
<header>
<link rel="stylesheet" type="text/css" href="../estilos/menu.css">
<link rel="stylesheet" href="../estilos/tablas.css">
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="../js/menu.js"></script>
<nav id='cssmenu'>
<div class="logo"><a href="#"><?php echo $sistema." ".$nombre?></a></div>
<div id="head-mobile"></div>
<div class="button"></div>
<ul>
<!--<li class='active'><a href='docente.php'>HOME</a></li>-->
<li class='active'><a href='vermaterias.php'>MATERIAS/ARCHIVOS <?php echo $escolar;?></a></li>
<li><a href='subirarchivos.php'>SUBIR <?php echo $escolar;?></a></li>
<li><a href='../../acciones/docenteactions/faltante.php'><?php echo utf8_decode($falta)?></a></li>
<li><a href='../../logout.php'><?php echo utf8_decode($cerrar)?></a></li>
</ul>
</nav>
</header>
<body>
<center><button class="botonn">MATERIAS</button></center>
  <table>
      <thead>
      <tr>
          <td></td>
          <td>MATERIA</td>
          <td>GRUPO</td>
          <td>CICLO</td>
          <td></td>
      </tr>
      </thead>
    <?php
    $mate = "SELECT * FROM asignarmateria WHERE matricula = '$matricula' and idciclo = '$escolar' ORDER BY idgrupo";
    $runmate = mysqli_query($conn, $mate);

    while ($raw = mysqli_fetch_row($runmate)) {
          ?>
          <tr>
        <td data-label=""><img border="0" src="/SAER/vista/img/<?php echo $raw[4]?>.png" width="45" height="45"></td>  
        <?php
        $sqlclave = "SELECT * FROM materias WHERE clave = '$raw[4]'"; 
        $runclave = mysqli_query($conn, $sqlclave);
        $eq = mysqli_fetch_row($runclave);
        ?>
        <td data-label="MATERIA"><?php echo $eq[1]?></td>  
        <td data-label="GRUPO"><?php echo $raw[2]?></td>
        <td data-label="CICLO"><?php echo $raw[3]?></td>
        
        <td data-label=""></td>
          </tr>
    <?php
    }
  ?>
</table>

<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<center><button class="botonn">APOYO DOCENCIA</button></center>
  <table>
      <thead>
      <tr>
          <td></td>
          <td>APOYO</td>
          <td>GRUPO</td>
          <td>CICLO</td>
          <td></td>
      </tr>
      </thead>
    <?php
    $mate = "SELECT * FROM asignarapoyo WHERE matricula = '$matricula' and idciclo = '$escolar' ORDER BY idgrupo";
    $runmate = mysqli_query($conn, $mate);

    while ($raw = mysqli_fetch_row($runmate)) {
          ?>
          <tr>
        <td data-label=""><img border="0" src="/SAER/vista/img/apoyo.png" width="45" height="45"></td>  
        <td data-label="APOYO"><?php echo $raw[2]?></td>  
        <?php
        if($raw[4] == '1111'){
          ?>
          <td data-label="GRUPO">N/A</td>
          <?php
        }else{
          ?>
          <td data-label="GRUPO"><?php echo $raw[4]?></td>
          <?php
        }
        ?>
        <td data-label="CICLO"><?php echo $raw[3]?></td>
        <td data-label=""></td>
          </tr>
    <?php
    }
  ?>
</table>

<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<center><button class="botonn" style="margin-top: 2%;">ARCHIVOS SUBIDOS DE MATERIAS</button></center>
  <table>
      <thead>
      <tr>
          <td></td>
          <td>MATERIA</td>
          <td>GRUPO</td>
          <td>RUBRO</td>
          <td>NOMBRE ARCHIVO</td>
          <td>ACTUALIZAR ARCHIVO</td>
          <td></td>
      </tr>
      </thead>
    <?php
    $mate = "SELECT * FROM archivos WHERE matricula = '$matricula' and idciclo = '$escolar' ORDER BY clave ASC";
    $runmate = mysqli_query($conn, $mate);

    while ($raw = mysqli_fetch_row($runmate)) {
          ?>
          <tr> 
        <form action="/SAER/acciones/docenteactions/actualizararchivosmaterias.php" method="post" enctype="multipart/form-data" name="inscripcion">
          <input type="text" name="idarchivo" value="<?php echo $raw[0]?>" hidden/>
          <input type="text" name="clave" value="<?php echo $raw[1]?>" hidden/>
          <input type="text" name="url" value="<?php echo $raw[2]?>" hidden/>
          <input type="text" name="idreporte" value="<?php echo $raw[3]?>" hidden/>
          <input type="text" name="idrubro" value="<?php echo $raw[5]?>" hidden/>
          <input type="text" name="idgrupo" value="<?php echo $raw[7]?>" hidden/>
          <input type="text" name="nombrearchivo" value="<?php echo $raw[8]?>" hidden/>
          <?php
          $as = "SELECT * FROM materias WHERE clave = '$raw[1]'";
          $asqu = mysqli_query($conn, $as);
          $asrow = mysqli_fetch_row($asqu);
          ?>
          <td><a href="../../acciones/docenteactions/descargadocente.php?archivo=<?php echo $raw[0]?>"><img border="0" src="/SAER/vista/img/archivoexplorer.png" width="30" height="30"></a></td>   
          <td data-label="MATERIA"><?php echo $asrow[1]?></td>  
          <td data-label="GRUPO"><?php echo $raw[7]?></td>
          <?php
          $busru = "SELECT * FROM tiporeporte WHERE idreporte = '$raw[3]'";
          $busruquery = mysqli_query($conn, $busru);
          $busrow = mysqli_fetch_row($busruquery);
          ?>
          <td data-label="RUBRO"><?php echo $busrow[1]?></td> 
          <td data-label="NOMBRE"><?php echo $raw[8]?></td>
          <td data-label="ARCHIVO"><input type="file" name="archivo1[]" accept="application/pdf" required></td>
          <td><input type="submit" id="botonazo" value="ACTUALIZAR ARCHIVO"></td>
        </form>
          </tr>
    <?php
    }
  ?>
</table>

<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<center><button class="botonn" style="margin-top: 2%;">ARCHIVOS SUBIDOS DE APOYO DOCENCIA</button></center>
  <table>
      <thead>
      <tr>
          <td></td>
          <td>APOYO</td>
          <td>GRUPO</td>
          <td>RUBRO</td>
          <td>NOMBRE ARCHIVO</td>
          <td>ACTUALIZAR ARCHIVO</td>
          <td></td>
      </tr>
      </thead>
    <?php
    $mate = "SELECT * FROM archivosapoyodocencia WHERE matricula = '$matricula' and idciclo = '$escolar' ORDER BY apoyo ASC";
    $runmate = mysqli_query($conn, $mate);

    while ($raw = mysqli_fetch_row($runmate)) {
          ?>
          <tr> 
        <form action="/SAER/acciones/docenteactions/actualizararchivosapoyo.php" method="post" enctype="multipart/form-data" name="inscripcion">
          <input type="text" name="idarchivo" value="<?php echo $raw[0]?>" hidden/>
          <input type="text" name="url" value="<?php echo $raw[1]?>" hidden/>
          <input type="text" name="apoyo" value="<?php echo $raw[2]?>" hidden/>
          <input type="text" name="idreportedocencia" value="<?php echo $raw[3]?>" hidden/>
          <input type="text" name="idgrupo" value="<?php echo $raw[6]?>" hidden/>
          <input type="text" name="nombrearchivo" value="<?php echo $raw[7]?>" hidden/>

          <td><a href="../../acciones/docenteactions/descargadocente.php?archivo=<?php echo $raw[1]?>"></a><img border="0" src="/SAER/vista/img/archivoexplorer.png" width="30" height="30"></td>  
          <td data-label="APOYO"><?php echo $raw[2]?></td>  
          <td data-label="GRUPO"><?php echo $raw[6]?></td>
          <?php
          $busru = "SELECT * FROM reportedocencia WHERE idreportedocencia = '$raw[3]'";
          $busruquery = mysqli_query($conn, $busru);
          $busrow = mysqli_fetch_row($busruquery);
          ?>
          <td data-label="RUBRO"><?php echo $busrow[1]?></td> 
          <td data-label="NOMBRE"><?php echo $raw[7]?></td>
          <td data-label="ARCHIVO"><input type="file" name="archivo2[]" accept="application/pdf" required></td>
          <td><input type="submit" id="botonazo" value="ACTUALIZAR ARCHIVO"></td>
        </form>
          </tr>
    <?php
    }
  ?>
</table>

<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<center><button class="botonn" style="margin-top: 2%;">ARCHIVOS SUBIDOS DE VISITAS</button></center>
  <table style="margin-bottom: 200px;">
      <thead>
      <tr>
          <td></td>
          <td>LUGAR</td>
          <td>GRUPO</td>
          <td>RUBRO</td>
          <td>NOMBRE ARCHIVO</td>
          <td>ACTUALIZAR ARCHIVO</td>
          <td></td>
      </tr>
      </thead>
    <?php
    $mate = "SELECT * FROM archivosvisitas WHERE matricula = '$matricula' and idciclo = '$escolar' ORDER BY lugar DESC";
    $runmate = mysqli_query($conn, $mate);

    while ($raw = mysqli_fetch_row($runmate)) {
          ?>
          <tr> 
        <form action="/SAER/acciones/docenteactions/actualizararchivosvisitas.php" method="post" enctype="multipart/form-data" name="inscripcion">
          <input type="text" name="idarchivo" value="<?php echo $raw[0]?>" hidden/>
          <input type="text" name="url" value="<?php echo $raw[1]?>" hidden/>
          <input type="text" name="fecha" value="<?php echo $raw[2]?>" hidden/>
          <input type="text" name="numalumnos" value="<?php echo $raw[3]?>" hidden/>
          <input type="text" name="lugar" value="<?php echo $raw[4]?>" hidden/>
          <input type="text" name="idreportevisita" value="<?php echo $raw[5]?>" hidden/>
          <input type="text" name="idgrupo" value="<?php echo $raw[8]?>" hidden/>
          <input type="text" name="nombrearchivo" value="<?php echo $raw[9]?>" hidden/>

          <td><img border="0" src="/SAER/vista/img/archivoexplorer.png" width="30" height="30"></td>  
          <td data-label="LUGAR"><?php echo $raw[4]?></td>  
          <td data-label="GRUPO"><?php echo $raw[8]?></td>
          <?php
          $busru = "SELECT * FROM reportevisita WHERE idreportevisita = '$raw[5]'";
          $busruquery = mysqli_query($conn, $busru);
          $busrow = mysqli_fetch_row($busruquery);
          ?>
          <td data-label="RUBRO"><?php echo $busrow[1]?></td> 
          <td data-label="NOMBRE"><?php echo $raw[9]?></td>
          <td data-label="ARCHIVO"><input type="file" name="archivo3[]" accept="application/pdf" required></td>
          <td><input type="submit" id="botonazo" value="ACTUALIZAR ARCHIVO"></td>
        </form>
          </tr>
    <?php
    }
  ?>
</table>


</body>
</html>
<?php
      
    }else{
      header('location: /SAER/index.php');
    }
?>