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
<script src="../librerias/sweetalert/dist/sweetalert.min.js"></script>
<link rel="stylesheet" type="text/css" href="../librerias/sweetalert/dist/sweetalert.css">
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
<li><a href='../../logout.php'>CERRAR SESIÓN</a></li>
</ul>
</nav>
</header>
<body>
  
   <?php
    if($_SESSION['permisos'] == 777){
       
$cicloescolar = array(                                
        0 => "No Existe Ciclo Anterior",     
        1 => "2017-1", 
        2 => "2017-2", 
        3 => "2018-1", 
        4 => "2018-2",
        5 => "2019-1", 
        6 => "2019-2", 
        7 => "2020-1", 
        8 => "2020-2",
        9 => "2021-1", 
        10 => "2021-2", 
        11 => "2022-1", 
        12 => "2022-2",
        13 => "2023-1", 
        14 => "2023-2", 
        15 => "2024-1", 
        16 => "2024-2",
        17 => "2025-1", 
        18 => "2025-2", 
        19 => "2026-1", 
        20 => "2026-2",
        21 => "2027-1", 
        22 => "2027-2", 
        23 => "2028-1", 
        24 => "2028-2",
        25 => "2029-1", 
        26 => "2029-2", 
        27 => "2030-1", 
        28 => "2030-2",
        29 => "2031-1", 
        30 => "2031-2", 
        31 => "2032-1", 
        32 => "2032-2",
        33 => "2033-1", 
        34 => "2033-2", 
        35 => "2034-1", 
        36 => "2034-2",
        37 => "2035-1", 
        38 => "2035-2", 
        39 => "2036-1", 
        40 => "2036-2",
        41 => "2037-1", 
        42 => "2037-2", 
        43 => "2038-1", 
        44 => "2038-2",
        45 => "2039-1", 
        46 => "2039-2",  
        47 => "2040-1", 
        48 => "2040-2",
        49 => "2041-1", 
        50 => "2041-2", 
        51 => "2042-1", 
        52 => "2042-2",
        53 => "2043-1", 
        54 => "2043-2", 
        55 => "2044-1", 
        56 => "2044-2",
        57 => "2045-1", 
        58 => "2045-2", 
        59 => "2046-1", 
        60 => "2046-2",
        61 => "2047-1", 
        62 => "2047-2", 
        63 => "2048-1", 
        64 => "2048-2",
        65 => "2049-1", 
        66 => "2049-2"
        );     
        $actual = array_search($escolar, $cicloescolar);
        $antiguo = $actual-1;
        $nuevo = $actual+1;

    ?>
    <center><button class="botonn">Ciclo Actual: <?php echo $escolar;?></button></center>
    <center><button class="botonn" onclick="agregar('<?php echo $nuevo;?>')">Nuevo Ciclo <?php echo $cicloescolar[$nuevo]?></button></center>
    <script>
        function agregar(nuevo)
            {
            if (confirm("¿Estas seguro de iniciar un nuevo ciclo? Al hacerlo no podrás habilitar de nuevo el ciclo anterior")){
            location.href='../../acciones/docenteactions/seleccionciclo.php?varciclo='+nuevo;          
            }else{
                alert("No te preocupes el ciclo seguirá siendo el mismo");
            }
            }
    </script>   
</body>
</html>
<?php
      }else{
        ?>
        <?php
        $sql = "SELECT * FROM tb_c_carreras WHERE IDclave_carrera = ".$_SESSION['id_carrera'];
        $pps = mysqli_query($conn, $sql);
        while($row=mysqli_fetch_row($pps)){
            $identidad = $row[0];
            $carrera = $row[1];
            $nombre = $row[4];
            $paterno = $row[5];
            $materno = $row[6];
            $grado = $row[7];
        }
        ?>
            <form onSubmit="return false;" id = "actualizar_jefe" method="POST" class="form-iniciar">
            <h2>Jefe de Carrera en el Ciclo Escolar:  <?php echo $escolar ?></h2>
            <label style="color:white;">Nombre</label>
            <input type="text" value="<?php echo $nombre ?>" name="nombre" />
            <label style="color:white;">Apellido Paterno</label>
            <input type="text" value="<?php echo $paterno ?>" name="paterno" />
            <label style="color:white;">Apellido Materno</label>
            <input type="text" value="<?php echo $materno ?>" name="materno" />
            <label style="color:white;">Grado</label>
            <input type="text" value="<?php echo $grado ?>" name="grado" />
            <input type="submit" name="submit" id="botonazo" onclick="actualizar_jefe();" value="Realizar Cambios"/>
            <script type="text/javascript">
                function actualizar_jefe(){
                        swal({
                            title: 'Desea realizar la operacion?',
                            text:  'Los cambios seran permanentes para todos los docentes de la carrera!',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dd6b55',
                            cancelButtonColor: '#999',
                            confirmButtonText: 'Si!',
                            cancelButtonText: 'No',
                            closeOnConfirm: true,
                            animation: "slide-from-top"              
                        },
                        function(value){
                            if(value){
                                $.post('../../acciones/adminactions/modificar.php',$('#actualizar_jefe').serialize(),function(data){
                                    setTimeout ( function () {
                                        data = JSON.parse(data);
                                        var j = "Ocurrio un problema";
                                        var error = "error";
                                        if(data.status){
                                            j = "¡ Actualizado Satisfactoriamente ! ";
                                            error = "success";
                                        }
                                        swal ( "Jefe de Carrera" , j , error );
                                    }, 2000 );
                                });
                            }
                        });
                }

            </script>
        <?php
      }
    }else{
      header('location: ../../index.php');
    }
?>