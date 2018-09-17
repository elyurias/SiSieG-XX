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
<script src="../librerias/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" type="text/css" href="../librerias/sweetalert/dist/sweetalert.css">
<nav id='cssmenu'>
<div class="logo"><a href="#"><?php echo $sistema." ".$nombre?></a></div>
<div id="head-mobile"></div>
<div class="button"></div>
<ul>
<!--<li class='active'><a href='docente.php'>HOME</a></li>-->
<li><a href='vermaterias.php'>MATERIAS/ARCHIVOS <?php echo $escolar;?></a></li>
<li class='active'><a href='subirarchivos.php'>SUBIR <?php echo $escolar;?></a></li>
<li><a href='../../acciones/docenteactions/faltante.php'><?php echo $falta?></a></li>
<li><a href='../../logout.php'><?php echo utf8_decode($cerrar)?></a></li>
</ul>
</nav>
</header>
<body>
<center><button class="botonn">MATERIAS</button>
  <?php require_once('../firma_doc.php');
  ?>
</center>
  <?php  
    $_REQUEST['id_docente'] = $_SESSION['matricula'];
    require_once('../../acciones/adminactions/obtener_documentos_docente.php');
  ?><br>
  <table>
      <thead>
      <tr>
          <td></td>
          <td>GRUPO</td>
          <td>MATERIA</td>
          <td>REPORTE</td>
          <td>ARCHIVO</td>
          <td></td>
      </tr>
      </thead>
    <?php
    $mate = "SELECT * FROM asignarmateria WHERE matricula = '$matricula' and idciclo = '$escolar' ORDER BY idgrupo";
    $runmate = mysqli_query($conn, $mate);

    while ($raw = mysqli_fetch_row($runmate)) {
        $mostrar = "SELECT idreporte FROM tiporeporte WHERE tiporeporte.idreporte not in (select idreporte from archivos where matricula = '$matricula' and clave = '$raw[4]' and idgrupo = '$raw[2]' and idciclo = '$escolar')";
        $runmostrar = mysqli_query($conn, $mostrar);
        $conta = 0;
        while($run = mysqli_fetch_row($runmostrar)){
          $conta++;
        }
        if($conta > 0){
          ?>
          <tr>
    <form action="/SAER/acciones/docenteactions/subirarchivos.php" method="post" enctype="multipart/form-data" name="inscripcion">
        <td data-label=""><img border="0" src="/SAER/vista/img/<?php echo $raw[4]?>.png" width="30" height="30"></td>
        <input type="text" name="grupo" value="<?php echo $raw[2]?>" hidden/>
        <td data-label="GRUPO"><?php echo $raw[2]?></td>
        <?php
        $ma = "SELECT * FROM materias WHERE clave = '$raw[4]'";
        $amt = mysqli_query($conn, $ma);
        $amte = mysqli_fetch_row($amt);
        ?>
        <td data-label="MATERIA"><?php echo $amte[1]?></td>
        <input type="text" name="materia" value="<?php echo $raw[4]?>" hidden/>
        <td data-label="REPORTE"><select name='tiporeporte' required>
          <?php
          $tiporepo = "SELECT idreporte FROM tiporeporte WHERE tiporeporte.idreporte not in (select idreporte from archivos where matricula = '$matricula' and clave = '$raw[4]' and idgrupo = '$raw[2]' and idciclo = '$escolar') and nombrereporte!=''";
          $repo = mysqli_query($conn, $tiporepo);
          while($repor = mysqli_fetch_row($repo)){
            $res = "SELECT * FROM tiporeporte WHERE idreporte = '$repor[0]'";
            $resu = mysqli_query($conn, $res);
            $reporte = mysqli_fetch_row($resu);
            ?>
              <option value="<?php echo $reporte[0]?>"><?php echo $reporte[1]?></option>
            <?php
          }
          ?>
        </select></td>
        <input type="text" name="rubro" value="1" hidden/>
        <td data-label="ARCHIVO"><input type="file" name="archivo1[]" accept="application/pdf"></td>
        <td><input type="submit" id="botonazo" onsubmit="this.disabled = true;" value="SUBIR ARCHIVO"></td>
    </form>
  </tr>
          <?php
        }else{
          
        }

    ?>
    <?php
    }
  ?>
</table>

<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->

<center><button class="botonn">APOYO A LA DOCENCIA</button></center>
  <table>
      <thead>
      <tr>
          <td></td>
          <td>GRUPO</td>
          <td>APOYO</td>
          <td>REPORTE</td>
          <td>ARCHIVO</td>
          <td></td>
      </tr>
      </thead>
    
    <?php
    $apoyodoc = "SELECT * FROM asignarapoyo WHERE matricula = '$matricula' and idciclo = '$escolar' ORDER BY idgrupo";
    $runapoyo = mysqli_query($conn, $apoyodoc);

    while ($raw = mysqli_fetch_row($runapoyo)) {
        $mostrar = "SELECT idreportedocencia FROM reportedocencia WHERE reportedocencia.idreportedocencia not in (select idreportedocencia from archivosapoyodocencia where matricula = '$matricula' and apoyo = '$raw[2]' and idgrupo = '$raw[4]' and idciclo = '$escolar')";
        $runmostrar = mysqli_query($conn, $mostrar);
        $conta = 0;
        while($run = mysqli_fetch_row($runmostrar)){
          $conta++;
        }
        if($conta > 0){
          ?>
          <tr>
    <form action="/SAER/acciones/docenteactions/subirapoyodocencia.php" method="post" enctype="multipart/form-data" name="inscripcion">
        <td data-label=""><img border="0" src="/SAER/vista/img/apoyo.png" width="30" height="30"></td>
        <input type="text" name="grupo" value="<?php echo $raw[4]?>" hidden/>
        <td data-label="GRUPO"><?php 
        if($raw[4] == 1111){
          echo 'N/A';
        }else{
        echo $raw[4];
        }
        ?></td>
        <td data-label="APOYO"><?php echo $raw[2]?></td>
        <input type="text" name="apoyo" value="<?php echo $raw[2]?>" hidden/>
        <td data-label="REPORTE"><select name='tiporeporte' required>
          <?php
          $tiporepodoc = "SELECT idreportedocencia FROM reportedocencia WHERE reportedocencia.idreportedocencia not in (select idreportedocencia from archivosapoyodocencia where matricula = '$matricula' and apoyo = '$raw[2]' and idgrupo = '$raw[4]' and idciclo = '$escolar')";
          $repodoc = mysqli_query($conn, $tiporepodoc);
          while($repordoc = mysqli_fetch_row($repodoc)){
            $res = "SELECT * FROM reportedocencia WHERE idreportedocencia = '$repordoc[0]'";
            $resu = mysqli_query($conn, $res);
            $reporte = mysqli_fetch_row($resu);
            $tip = $reporte[1];
            ?>
              <option value="<?php echo $reporte[0]?>"><?php echo $tip?></option>
            <?php
          }
          ?>
        </select></td>
        <input type="text" name="rubro" value="1" hidden/>
        <td data-label="ARCHIVO"><input type="file" name="archivo2[]" accept="application/pdf" required></td>
        <td><input id="botonazo" type="submit" onsubmit="this.disabled = true; buen_trabajo_persona_especial()" value="SUBIR ARCHIVO"></td>
    </form>
  </tr>
          <?php
        }else{
          
        }

    ?>
    <?php
    }
  ?>
</table>

<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->

<center><button class="botonn">VISITAS INDUSTRIALES</button></center>
<div class="selectron">
<center><select onchange="mostrar(this.value);">
      <option value=''>SELECCIONAR</option>
      <option value='alta'>DAR DE ALTA VISITA</option>
      <option value='subir'>SUBIR ARCHIVOS VISITAS</option>
    </select></center> 
</div>
<script type="text/javascript">
function mostrar(id) {
    if (id == "alta") {
        $("#alta").show();
        $("#subir").hide();
    }
    if (id == "subir") {
        $("#alta").hide();
        $("#subir").show();
    }
}
</script>

<div id="alta" hidden>
    <link rel="stylesheet" href="../estilos/validationEngine.jquery.css" type="text/css"/>
    <script src="../js/languages/jquery.validationEngine-es.js" type="text/javascript" charset="utf-8"></script>
    <script src="../js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>    
    <script>
        jQuery(document).ready(function(){
            jQuery("#nuevavisita").validationEngine();
        });
    </script>  

  <form action="./../../acciones/docenteactions/newvisita.php" method="POST" onSubmit="return valida(this)" name="nuevavisita" id="nuevavisita" class="nuevavisita"> 
    <h2>NUEVA VISITA</h2>
    <input type="text" autocomplete="off" name="lugar" class="validate[required,custom[onlyLetterSp],minSize[3]] text-input" placeholder="Lugar" required/>
    <input type="text" autocomplete="off" name="fechavisita" class="validate[required,custom[date]]" placeholder="DD-MM-AAAA" required/>
    <input type="number" autocomplete="off" min="1" class="validate[required,custom[integer]] text-input" name="numalumnos" placeholder="Numero de alumnos" required/>
    <input type="text" value="<?php echo $escolar ?>" name="ciclo" readonly/>
    <select name='grupo' required>
        <option value="">Seleccionar Grupo</option>
        <?php
        $grupo = "SELECT * FROM grupo WHERE idgrupo <> 1111 and id_carrera = ".$_SESSION['id_carrera'];
        $rungrupo = mysqli_query($conn, $grupo);

        while($rew = mysqli_fetch_row($rungrupo)){
          ?>
          <option value="<?php echo $rew[0]?>"><?php echo $rew[0]?></option>
          <?php
        }
        ?>
    </select>    
    <input type="submit" name="submit" id="botonazo" value="Registrar Visita"/>
  </form>
</div>

<div id="subir">
  <table style="margin-bottom: 100px;">
      <thead>
      <tr>
          <td></td>
          <td>GRUPO</td>
          <td>VISITA A:</td>
          <td>FECHA:</td>
          <td>ALUMNOS:</td>
          <td>REPORTE</td>
          <td>ARCHIVO</td>
          <td></td>
      </tr>
      </thead>
    
    <?php
    $apoyodoc = "SELECT * FROM visitas WHERE matricula = '$matricula' and idciclo = '$escolar' ORDER BY idgrupo";
    $runapoyo = mysqli_query($conn, $apoyodoc);

    while ($raw = mysqli_fetch_row($runapoyo)) {
        $mostrar = "SELECT idreportevisita FROM reportevisita WHERE reportevisita.idreportevisita not in (select idreportevisita from archivosvisitas where matricula = '$matricula' and lugar = '$raw[1]' and idgrupo = '$raw[6]' and idciclo = '$escolar')";
        $runmostrar = mysqli_query($conn, $mostrar);
        $conta = 0;
        while($run = mysqli_fetch_row($runmostrar)){
          $conta++;
        }
        if($conta > 0){
          ?>
          <tr>
    <form action="/SAER/acciones/docenteactions/subirvisitas.php" method="post" enctype="multipart/form-data" name="inscripcion">
        <td data-label=""><img border="0" src="/SAER/vista/img/visita.png" width="30" height="30"></td>
        <input type="text" name="grupo" value="<?php echo $raw[6]?>" hidden/>
        <td data-label="GRUPO"><?php 
        if($raw[6] == 1111){
          echo 'N/A';
        }else{
        echo $raw[6];
        }
        ?></td>
        <td data-label="VISITA A"><?php echo $raw[1]?></td>
        <input type="text" name="lugar" value="<?php echo $raw[1]?>" hidden/>
        <td data-label="FECHA"><?php echo $raw[2]?></td>
        <input type="text" name="fecha" value="<?php echo $raw[2]?>" hidden/>
        <td data-label="ALUMNOS"><?php echo $raw[3]?></td>
        <input type="text" name="numalumnos" value="<?php echo $raw[3]?>" hidden/>
        <td data-label="REPORTE"><select name='tiporeporte' required>
          <?php
          $tiporepodoc = "SELECT idreportevisita FROM reportevisita WHERE reportevisita.idreportevisita not in (select idreportevisita from archivosvisitas where matricula = '$matricula' and lugar = '$raw[1]' and idgrupo = '$raw[6]' and idciclo = '$escolar')";
          $repodoc = mysqli_query($conn, $tiporepodoc);
          while($repordoc = mysqli_fetch_row($repodoc)){
            $res = "SELECT * FROM reportevisita WHERE idreportevisita = '$repordoc[0]'";
            $resu = mysqli_query($conn, $res);
            $reporte = mysqli_fetch_row($resu);
            $tip = $reporte[1];
            ?>
              <option value="<?php echo $reporte[0]?>"><?php echo $tip?></option>
            <?php
          }
          ?>
        </select></td>
        <input type="text" name="rubro" value="1" hidden/>
        <td data-label="ARCHIVO"><input type="file" name="archivo3[]" accept="application/pdf" required></td>
        <td><input id="botonazo" type="submit" onsubmit="this.disabled = true" value="SUBIR ARCHIVO"></td>
    </form>
  </tr>
          <?php
        }else{
          
        }

    ?>
    <?php
    }
  ?>
</table>
</div>
<div id="id02" class="w3-modal">
  <div class="w3-modal-content w3-animate-opacity">

    <header class="w3-container w3-green"> 
      <span onclick="document.getElementById('id02').style.display='none'" 
      class="w3-button w3-display-topright">&times;</span>
      <h2>Documento</h2>
    </header>

    <div class="w3-container"  style="height: 500px;" id="documento_general_maximus">
      
    </div>

    <footer class="w3-container w3-green">
      <p><?php echo $carrera_nombre;?></p>
    </footer>

  </div>
</div>
<div id="id01" class="w3-modal">
  <div class="w3-modal-content w3-animate-opacity">

    <header class="w3-container w3-green"> 
      <span onclick="document.getElementById('id01').style.display='none'" 
      class="w3-button w3-display-topright">&times;</span>
      <h2>Documentos del Docente <div id="nombre_docente_id"></div></h2>
    </header>

    <div class="w3-container" id="documentos_totales_torales">
      
    </div>

    <footer class="w3-container w3-green">
      <p><?php echo $carrera_nombre;?></p>
    </footer>
  </div>
</div>
</body>
</html>
<script>
  function buen_trabajo_persona_especial(){
    swal("ARCHIVO CORRECTO!", "", "success");
  }
</script>
<?php
    if(isset($_GET['ACTIONS'])){
      echo "<script>buen_trabajo_persona_especial();</script>";
    }
    }else{
      header('location: /SAER/index.php');
    }
?>