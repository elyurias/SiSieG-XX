<?php
        session_start();
        // comprobamos que se haya iniciado la sesión
        if(isset($_SESSION['nombre']) && $_SESSION['permisos'] == 777  || $_SESSION['permisos'] == 660) {
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
<link rel="stylesheet" type="text/css" href="../estilos/menu.css">
<link rel="stylesheet" href="../estilos/tablas.css">
<link rel="stylesheet" href="../estilos/formulario.css">
<script src="../js/jquery-3.1.1.min.js"></script>
<script src="../js/menu.js"></script>
<?php
  echo $librerias;
?>
<script src="../librerias/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" type="text/css" href="../librerias/sweetalert/dist/sweetalert.css">
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
<li><a href='../../logout.php'><?php echo fun_utf8("CERRAR SESIÓN")?></a></li>
</ul>
</nav>
</header>
<body>
  <?php
  if(empty($_POST["idasimat"])){
      }else{
        $delmat = $_POST["idasimat"];
        $delsql = "DELETE FROM asignarmateria WHERE idasignar = '$delmat'";
        $delquery = mysqli_query($conn, $delsql);
      }
  ?>
    <form onSubmit="return valida(this)" action="/SAER/acciones/adminactions/asignarmaterias.php" method="POST" class="form-iniciar">
            <h2>Asignar Materia en el <?php echo $escolar ?></h2>
            <select name='carrera' id="carrera" required>
                <?php
                  echo $array_carreras;
                ?>
            </select>
            <select name='docente' id="docentes" required>
                <option value="">Seleccionar Docente</option>
                <?php
                $temporal=10001;
                if($_SESSION['permisos']==660){
                  $temporal = $_SESSION['id_carrera'];
                }
                $profe = "SELECT * FROM docente WHERE id_carrera = $temporal and idstatus = 1 AND permisos = 600 ORDER BY paterno";
                $runprofe = mysqli_query($conn, $profe);

                while($raw = mysqli_fetch_row($runprofe)){
                  ?>
                  <option value="<?php echo $raw[0]?>"><?php echo $raw[2].' '.$raw[3].' '.$raw[1]?></option>
                  <?php
                }
                ?>
            </select>
            <select name='grupo' required>
                <option value="">Seleccionar Grupo</option>
                <?php
                $grupo = "SELECT * FROM grupo WHERE idgrupo <> 1111";
                $rungrupo = mysqli_query($conn, $grupo);

                while($rew = mysqli_fetch_row($rungrupo)){
                  ?>
                  <option value="<?php echo $rew[0];?>"><?php echo $rew[0]?></option>
                  <?php
                }
                ?>
            </select>
            <input type="hidden" value="<?php echo $escolar ?>" name="ciclo" readonly/>
            <select name='materia' id="materia" required>
                <option value="">Seleccionar Materia</option>
                <?php
                $materia = "SELECT * FROM materias $limitanteVisualDos ORDER BY nombre ASC";
                $runmateria = mysqli_query($conn, $materia);

                while($row = mysqli_fetch_row($runmateria)){
                  ?>
                  <option value="<?php echo $row[0]?>"><?php echo substr($row[0],0,8). " - " .$row[1]?></option>
                  <?php
                }
                ?>
            </select>
            <input type="submit" name="submit" id="botonazo" value="Asignar Materia"/>
    </form>
    <center><h2 style="color: black;">ELIGE UN PROFESOR PARA VER SUS MATERIAS</h2></center>
        <center><select id="ver_las_materias" onchange="mostrar(this.value);" style="color: green; background-color: black; width: 300px; border-radius: 15px;">
          <option value="">SELECCIONAR DOCENTE</option>
          <?php
          $docsql = "SELECT * FROM docente $limitanteVisualDos AND permisos != 660 ORDER BY paterno ASC";
          $docquery = mysqli_query($conn, $docsql);
          while ($docrow = mysqli_fetch_row($docquery)) {
             ?>
              <option value="<?php echo $docrow[0]?>"><?php echo fun_utf8($docrow[2]).' '.fun_utf8($docrow[3]).' '.fun_utf8($docrow[1])?></option>
             <?php
           } 
          ?>
        </select></center>
    <script>
        $(document).ready(function(){
          $("#carrera").change(function(){
            fun_repeat();
            fun_repeat_docentes("docentes");
            fun_repeat_docentes("ver_las_materias");
          });
        });
        function fun_repeat(){
          $.post('../conexion.php',
              {
                dataTrue: 1,
                carrera: $("#carrera").val() 
              },
              function(data){
                $("#materia").html(data);
            });
        }
        function fun_repeat_docentes(ntr){
          $.post('../conexion.php',
              {
                dataFalse: 1,
                carrera: $("#carrera").val() 
              },
              function(data){
                $("#"+ntr+"").html(data);
            });
        }
        function mostrar(muestra)
            {
            location.href='materias.php?muestra='+muestra+'#matasignadas';          
            }
    </script>
    <div id="matasignadas">
      <?php
      
      if(empty($_GET["muestra"])){
        echo '<h2>SELECCIONA UN DOCENTE</h2>';
      }else{
        $prof = $_GET["muestra"];
        $nomdoc = "SELECT * FROM docente WHERE matricula = '$prof'";
        $nomdocquery = mysqli_query($conn, $nomdoc);
        $nomrow = mysqli_fetch_row($nomdocquery);
        ?>
        <center><button class="botonn">Materias: <?php echo $nomrow[2].' '.$nomrow[3].' '.$nomrow[1]?></button></center>
        <table style="margin-bottom: 150px;">
          <thead>
            <tr>
                <td></td>
                <td>MATERIA</td>
                <td>GRUPO</td>
                <td>CICLO</td>
                <td>ELIMINAR MATERIA</td>
            </tr>
          </thead>
        <?php
        $matsql = "SELECT * FROM asignarmateria WHERE matricula = '$prof' and idciclo = '$escolar' ORDER BY idgrupo ASC";
        $matquery = mysqli_query($conn, $matsql);
        while($matrow = mysqli_fetch_row($matquery)){
          ?>

          <tr>
          <form action="/SAER/vista/administrador/materias.php?muestra=<?php echo $prof?>#matasignadas" method="post">
            <td><img border="0" src="/SAER/vista/img/<?php echo $matrow[4]?>.png" width="30" height="30"></td>
            <input type="text" name="idasimat" value="<?php echo $matrow[0]?>" hidden/>
            <?php
            $namemat = "SELECT * FROM materias WHERE clave = '$matrow[4]'";
            $namematquery = mysqli_query($conn, $namemat);
            $namematrow = mysqli_fetch_row($namematquery);
            ?>
            <td data-label="MATERIA"><?php echo $namematrow[1]?></td>
            <td data-label="GRUPO"><?php echo $matrow[2]?></td>
            <td data-label="CICLO"><?php echo $matrow[3]?></td>
            <td><input id="botonazo" type="submit" value="ELIMINAR"></td>
          </form>
          </tr>
          <?php
        }
      }
      ?>
    </div>
</body>
</html>
<?php
    }else{
      header('location: /SAER/index.php');
    }
      $datatrue = "";
      $statusTrue = "";
        if(isset($_GET['ACTION'])){
          $datatrue ="MATERIA REGISTRADA A DOCENTE DE MANERA SATISFACTORIA";
          $statusTrue = "success";
          echo '<script>
            swal("'.$datatrue.'", "", "'.$statusTrue.'");
          </script>';
        }else if(isset($_GET['NOACTION'])){
          $datatrue = "ERROR AL REALIZAR LA ASIGNACION, VERIFICA LA INFORMACION";
          $statusTrue = "error";
          echo '<script>
            swal("'.$datatrue.'", "", "'.$statusTrue.'");
          </script>';
        }
?>