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
        $cerrar = fun_utf8("CERRAR SESIÓN");
?>
<!DOCTYPE html>
<html>
<header>
<title><?php echo $sistema." ".$nombre?></title>
<!--<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> -->
<!--CSS NUEVO MODAL-->

  <link rel="stylesheet" href="../estilos/w3.css">
    <script src="../js/data.js"></script>
      <link rel="stylesheet" href="../estilos/formulario.css">
  <script src="../librerias/sweetalert/dist/sweetalert.min.js"></script>
  <link rel="stylesheet" type="text/css" href="../librerias/sweetalert/dist/sweetalert.css">
<link rel="stylesheet" type="text/css" href="../estilos/menu.css">
<script src="../js/menu.js"></script>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<nav id='cssmenu'>
  <!--
    Sistema Integral de Seguimiento al Curso y Evidencias
  -->
<div class="logo"><a href="administrador.php" class="telme"><?php echo $sistema." ".$nombre ?></a></div>
<div id="head-mobile"></div>
<div class="button"></div>
<ul>
<!--<li class='active'><a href='docente.php'>HOME</a></li>-->
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
<li><a href='../../logout.php'><?php echo $cerrar?></a></li>
</ul>
</nav>
</header>
<body>
<?php
include_once "../conexion.php";

$var = $_GET['variable'];
$agregar = $_GET['variable2'];

if($var > 0){
   $sql = "SELECT * FROM docente WHERE matricula = '$var'";

   $result = mysqli_query($conn, $sql);
   $row = mysqli_fetch_row($result);

   ?>
    <body>
    <link rel="stylesheet" href="../estilos/formulario.css">
    <form onSubmit="return valida(this)" action="../../acciones/adminactions/editardocente.php" method="POST" class="form-iniciar">
            <h2>Editar Docente</h2>
            <input type="number" autocomplete="off" name="matricula" value="<?php echo $row[0] ?>" onkeypress="return justNumbers(event);" placeholder="Matricula" readonly required/>
            <input type="text" autocomplete="off" name="nombre" value="<?php echo fun_utf8($row[1]) ?>" onkeypress="return justLetters(event);" placeholder="Nombre" required/>
            <input type="text" autocomplete="off" name="paterno" value="<?php echo fun_utf8($row[2]) ?>" onkeypress="return justLetters(event);" placeholder="Apellido Paterno" required/>
            <input type="text" autocomplete="off" name="materno" value="<?php echo fun_utf8($row[3]) ?>" onkeypress="return justLetters(event);" placeholder="Apellido Materno" required/>
            <input type="text" autocomplete="off" name="correo" value="<?php echo $row[4] ?>" placeholder="Correo" required/>
            <input type="text" autocomplete="off" name="usuario" value="<?php echo $row[5] ?>"  placeholder="Usuario" hidden required/>
            <input type="text" name="contra" placeholder="Contraseña" value="<?php echo $row[6] ?>" hidden required/>
            <input type="text" name="permisos" placeholder="Permisos" value="<?php echo $row[7] ?>" hidden required/>
            <select name='estado' readonly>
                <option value="<?php echo $row[8] ?>"><?php
                 if($row[8] == 1){
                  ?>
                    Activo
                  <?php
                 }elseif($row[8] == 2){
                  ?>
                  Baja Temporal
                  <?php
                 }elseif($row[8] == 3){
                  ?>
                  Baja Definitiva
                  <?php
                 }
                ?></option>
                <option value="1">Activo</option>
                <option value="2">Baja Temporal</option>
                <option value="3">Baja Definitiva</option>
            </select>
            <input type="submit" name="submit" id="botonazo" value="Editar"/>
        </form>
<?php
}elseif($agregar == 'nuevodocente'){
?>
<link rel="stylesheet" href="../estilos/formulario.css">
    <form onSubmit="return valida(this)" action="../../acciones/adminactions/newdocente.php" method="POST" class="form-iniciar">
            <h2>Nuevo Docente</h2>
            <input type="number" autocomplete="off" name="matricula" max-length="9" min-length="9" placeholder="Matricula (3 Digitos)" required/>
            <input type="text" autocomplete="off" name="nombre" placeholder="Nombre" required/>
            <input type="text" autocomplete="off" name="paterno" placeholder="Apellido Paterno" required/>
            <input type="text" autocomplete="off" name="materno" placeholder="Apellido Materno" required/>
            <input type="email" autocomplete="off" name="correo" placeholder="Correo" required/>
            <input type="hidden" autocomplete="off" name="id_carrera" placeholder="" value="<?php echo $carrera;?>" required/>
            <select name='permisos' required>
                <option value="">Permisos</option>
                <!--<option value="777">Administrador</option>-->
                <option value="600">Docente</option>
            </select>
            <select name='estado' required>
              <option value="">Estado</option>
            <?php
            $sql = "SELECT * FROM status";
            $result = mysqli_query($conn, $sql);
              while($row = mysqli_fetch_row($result)){
              ?>
                <option value="<?php echo $row[0]?>"><?php echo $row[1]?></option>
              <?php
              }
              ?>
            </select>
            <input type="submit" name="submit" id="botonazo" value="Registrar"/>
    </form>
<?php
}else{
?>

<link rel="stylesheet" href="../estilos/formulario.css">
<link rel="stylesheet" href="../estilos/tablas.css">

<center>
  <button class="botonn" onclick="agregar('nuevodocente')">Nuevo Docente</button>
  <button class="botonn" onclick="ver_avance_del_docente()">Avance del docente</button>
  <!--MODALS-->
    <div id="id01_docente" class="w3-modal">
      <div class="w3-modal-content w3-animate-opacity">

        <header class="w3-container w3-green"> 
          <span onclick="document.getElementById('id01_docente').style.display='none'" 
          class="w3-button w3-display-topright">&times;</span>
          <h2>Porcentaje de Avance por Docente <div id="nombre_docente_id"></div></h2>
        </header>

        <div class="w3-container" id="data_docente_libreria">
          <table id="data_docentes">
            <thead>
              <tr>
                <th>
                  Matricula
                </th>
                <th>
                  Nombre
                </th>
                <th>
                  Porcentaje de Avance
                </th>
                <th>
                  Subida de Archivos
                </th>
                <th>
                  Estado
                </th>
              </tr>
            </thead>
            <tbody id="data_docente_lier">
              
            </tbody>
          </table>
        </div>

        <footer class="w3-container w3-green">
          <p><?php echo $carrera_nombre;?></p>
        </footer>

      </div>
    </div>
  <?php if($_SESSION['permisos']!=777){
      //require_once('../firma_doc.php');
    ?>
  
  <?php } ?>
</center>
    <script>
        function agregar(nuevo)
            {
            location.href='nuevoeditar.php?variable2='+nuevo+'&variable="no"&carrera=<?php echo $carrera;?>';          
            }
    </script>
<?php
$sql = "SELECT * FROM docente WHERE matricula != 99099 and id_carrera=$carrera and permisos!=660 and permisos!=777 and permisos!=100 ORDER BY PATERNO ASC";

$result = mysqli_query($conn, $sql);

if(!$result){
echo 'No se pueden mostrar los datos';
}else{
?>
<!--MODALS-->
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

<!--MODALS-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $("#tablaPrincipal").DataTable();
  });
</script>
<table style="margin-bottom: 100px;" width="80%" class="display" id="tablaPrincipal">

<thead>
<tr>
    <td>MATRICULA</td>
    <td>NOMBRE</td>
    <td>CORREO</td>
    <td>PERMISOS</td>
    <td>ESTADO</td>
    <td>EDITAR</td>
    <td>VER MATERIAS</td>
    <td>REPORTE PDF</td>
    <td>REPORTE EXCEL</td>
    <td>DESCARGAR ARCHIVOS <?php echo $escolar?></td>
    <td>VER LOS DOCUMENTOS</td>
</tr>
</thead>

<?php
while($row=mysqli_fetch_row($result))

{
  if($row[8] == 2 || $row[8] == 3 || $row[7] == 777){
?>
<tr>
    <td data-label="MATRICULA"><?php echo $row[0]?></td>
    <td data-label="NOMBRE" style="text-align: left;"><?php echo $row[2].' '.$row[3].' '.$row[1]?></td>
    <td data-label="CORREO"><?php echo $row[4]?></td>
    <?php
    if($row[7] == '777'){
      ?>
      <td data-label="PERMISOS"><img border="0" src="/SAER/vista/img/support.png" width="39" height="39"></td>
      <?php
    }elseif ($row[7] == '600') {
      ?>
      <td data-label="PERMISOS"><img border="0" src="/SAER/vista/img/teacher.png" width="39" height="39"></td>
      <?php
    }
    ?>
    <?php
    if($row[8] == 1){
        ?>
        <td data-label="STATUS"><img border="0" onclick="statu(<?php echo $row[0]?>)" src="/SAER/vista/img/ledgreen.png" width="39" height="39"></td>
        <?php
    }elseif($row[8] == 2){
        ?>
        <td data-label="STATUS"><img border="0" onclick="statu(<?php echo $row[0]?>)" src="/SAER/vista/img/ledorange.png" width="39" height="39"></td>
        <?php        
    }elseif($row[8] == 3){
        ?>
        <td data-label="STATUS"><img border="0" onclick="statu(<?php echo $row[0]?>)" src="/SAER/vista/img/ledred.png" width="39" height="39"></td>
        <?php
    }
    ?>
    <td data-label="EDITAR"><img border="0" onclick="enviarid(<?php echo $row[0]?>)" src="/SAER/vista/img/edit.png" width="39" height="39"></td>
    <td data-label="VER MATERIAS"><img border="0" style="cursor:not-allowed" src="/SAER/vista/img/vermateriagray.png" width="39" height="39"></td>
    <td data-label="PDF"><img border="0" style="cursor:not-allowed" src="/SAER/vista/img/pdficongray.png" width="39" height="39"></td>
    <td data-label="EXCEL"><img border="0" style="cursor:not-allowed" src="/SAER/vista/img/excelicongray.png" width="39" height="39"></td>
    <td data-label="ARCHIVOS"><img border="0"  style="cursor:not-allowed" src="/SAER/vista/img/download.png" width="39" height="39"></td>
<?php
  }elseif($row[8] == 1){
?>
    <tr>
    <td data-label="MATRICULA"><?php echo $row[0]?></td>
    <td data-label="NOMBRE" style="text-align: left;"><?php echo $row[2].' '.$row[3].' '.$row[1]?></td>
    <td data-label="CORREO"><?php echo $row[4]?></td>
    <?php
    if($row[7] == '777'){
      ?>
      <td data-label="PERMISOS"><img border="0" src="/SAER/vista/img/support.png" width="39" height="39"></td>
      <?php
    }elseif ($row[7] == '600') {
      ?>
      <td data-label="PERMISOS"><img border="0" src="/SAER/vista/img/teacher.png" width="39" height="39"></td>
      <?php
    }
    ?>
    <?php
    if($_SESSION['permisos']!=777){
          if($row[8] == 1){
        ?>
       <td data-label="STATUS"><img border="0" onclick="statu(<?php echo $row[0]?>)" src="/SAER/vista/img/ledgreen.png" width="39" height="39"></td>
        <?php
    }elseif($row[8] == 2){
        ?>
        <td data-label="STATUS"><img border="0" onclick="statu(<?php echo $row[0]?>)" src="/SAER/vista/img/ledorange.png" width="39" height="39"></td>
        <?php        
    }elseif($row[8] == 3){
        ?>
        <td data-label="STATUS"><img border="0" onclick="statu(<?php echo $row[0]?>)" src="/SAER/vista/img/ledred.png" width="39" height="39"></td>
        <?php
    }
    }
    ?>
    <td data-label="EDITAR"><img border="0" onclick="enviarid(<?php echo $row[0]?>)" src="/SAER/vista/img/edit.png" width="39" height="39"></td>
    <td data-label="VER MATERIAS"><img border="0" onclick="enviarmaterias(<?php echo $row[0] ?>)" src="/SAER/vista/img/vermateria.png" width="39" height="39"></td>
    <td data-label="PDF"><img border="0" onclick="pdffunction(<?php echo $row[0] ?>)" src="/SAER/vista/img/pdficon.png" width="39" height="39"></td>
    <td data-label="EXCEL"><img border="0" onclick="excelfunction(<?php echo $row[0] ?>)" src="/SAER/vista/img/excelicon.png" width="39" height="39"></td>
    <td data-label="ARCHIVOS"><img border="0" onclick="downzip(<?php echo $row[0] ?>)" src="/SAER/vista/img/download.png" width="39" height="39"></td>
    <td data-label="VER_ARCHIVOS"><img border="0" onclick="docs.ver_documentos(<?php echo $row[0]; ?>)" src="/SAER/vista/img/ACA-0910.png" width="39" height="39"></td>
<?php
  }else{

  }
}
?>
</table>
<?php
}
    ?>
    <script>
        function enviarid(matri)
            {
            location.href='nuevoeditar.php?variable='+matri+'&variable2=0&carrera='+<?php echo $carrera;?>;          
            }
        function enviarmaterias(clave)
            {
            location.href='materias.php?muestra='+clave+'#matasignadas';          
            }
        function pdffunction(idpdf)
            {
            location.href='../../acciones/adminactions/pdfgeneralDos.php?idpdf='+idpdf+"&id_carrera=<?php echo $_SESSION['id_carrera'];?>";
            }
        function excelfunction(idexcel)
            {
            location.href='../../acciones/adminactions/reporteexceldocente.php?idexcel='+idexcel;          
            }
        function statu(idstatus)
            {
            location.href='../../acciones/adminactions/statuschange.php?idstatus='+idstatus+"&id_carrera=<?php echo $_SESSION['id_carrera'];?>";          
            }
        function downzip(idzip)
            {
            location.href='../../acciones/adminactions/individualzip.php?idzip='+idzip;          
            }
        function ver_avance_del_docente(){
              document.getElementById('id01_docente').style.display='block';
              $.post('../../acciones/adminactions/avances.php',{},function(data){
                  var contador = 1;
                  $("#data_docente_lier").empty();
                  $.each(data, function(i, item) {
                    var porcentaje = item.ARCHIVOS_SUBIDOS*((item.ARCHIVOS_REQUERIDOS_POR_MATERIA * item.MATERIAS_ASIGNADAS)/100);
                    var estado = "";
                    var status = "NO HA SUBIDO NINGUN ARCHIVO";
                    if(porcentaje>=100){
                      status = "EL DOCENTE HA SUBIDO TODOS SUS ARCHIVOS";
                    }else if(porcentaje>=50 && porcentaje<=99){
                      status = "EL DOCENTE NO SUBIO TODOS SUS DOCUMENTOS";
                    }else if(porcentaje<50){
                      status = "EL DOCENTE NO HA SUBIDO TODOS SUS DOCUMENTOS"
                    }else{
                      status = "EL DOCENTE NO HA SUBIDO NINGUN ARCHIVO";
                    }
                    var imagen = '<canvas id="micanvas'+contador+'" style="width : 100px; height : 50px;"></canvas>';
                    var obj = "<td>"+item.matricula+"</td><td>"+item.nombre+" "+item.paterno+" "+item.materno+"</td><td>"+porcentaje+" %</td><td>"+status+"</td><td>"+imagen+"</td>";
                    $("#data_docente_lier").append("<tr>"+obj+"</tr>");
                    var context = document.querySelector("#micanvas"+contador).getContext('2d');
                      var X = document.querySelector("#micanvas"+contador).width/2;
                      var Y = 70;
                      var r = 50;
                      var porcentaje360 = (360/100)*porcentaje;
                      var aPartida = (Math.PI / 180) * 0;
                      var aFinal =  (Math.PI / 180)  * porcentaje360;
                      context.beginPath();
                      context.lineWidth = 25;
                      context.strokeStyle = 'green';
                      context.arc(X,Y,r,aPartida,aFinal, false);
                      context.stroke();
                      contador = contador + 1;
                  });
              });
        }
    </script>
<?php
}
?>
</body>
</html>
<?php
      
    }else{
      header('location: /SAER/index.php');
    }
?>