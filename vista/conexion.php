<?php
$sistema = "SiSigE-XXI:";
$conn = mysqli_connect("localhost", "root", "");//Windows connection
mysqli_select_db($conn, "SAER2");//Windows connection
@session_start();
mysqli_set_charset($conn, "utf8mb4"); 
//mysqli_query($conn, "SET NAMES 'utf8'");  
//mysqli_query($conn, "SET NAMES 'utf8mb4'");
$librerias = <<<EOT
    <script type="text/javascript" src="../js/selectize.js/dist/js/standalone/selectize.js"></script>
    <link rel="stylesheet" type="text/css" href="../js/selectize.js/dist/css/selectize.css">
EOT;
        $limitanteVisual = "";
        $limitanteVisualDos = "";
        $conectorY = "";
        if(isset($_SESSION['permisos'])){
            if($_SESSION['permisos'] == 660){
                $limitanteVisual = " WHERE IDclave_carrera = ".$_SESSION['id_carrera'];
                $limitanteVisualDos = " WHERE id_carrera = ".$_SESSION['id_carrera'];
                $conectorY = "AND";
            }else{
                $conectorY = "WHERE";
            }
            if($_SESSION['permisos']==777){
                $limitanteVisualDos = " WHERE id_carrera = 10001";
                $carrera = 10002;
            }
            //
            if($_SESSION['permisos']==660 || $_SESSION['permisos']==600){
                $carrera = $_SESSION['id_carrera'];
            }else{
                if(isset($_GET['carrera'])){
                 $carrera = $_GET['carrera'];
                }else{
//                 exit();
                }
            }
            $TOEN = "SELECT Vnombre_carrera FROM tb_c_carreras WHERE IDclave_carrera = ".$carrera." LIMIT 1";
            $TOEN2 = mysqli_query($conn, $TOEN);
            $carrera_en_curso = mysqli_fetch_row($TOEN2);
            $carrera_nombre = $carrera_en_curso[0];
            //
        }
		$carreras = "SELECT * FROM tb_c_carreras ".$limitanteVisual;
        $runcarreras = mysqli_query($conn, $carreras);
        $cadena_carreras = "";
        $array_carreras = "";
        $cadena_carreras_apoyo="";
        while($raw = mysqli_fetch_row($runcarreras)){
            $cadena_carreras.="      
            <li>
                <a href='nuevoeditar.php?variable=0&variable2=no&carrera={$raw[0]}'>
                    {$raw[1]}
                </a>
            </li>";
            $array_carreras.="
            	<option value='$raw[0]'>
					$raw[1]
            	</option>      
            ";
            $cadena_carreras_apoyo.="      
                <li>
                    <a href='apoyodocencia.php?carrera={$raw[0]}'>
                        {$raw[1]}
                    </a>
                </li>";
        }
        if(isset($_POST['dataTrue'])){
        	$identidad = $_POST['carrera'];
        	$materias = "SELECT * FROM materias WHERE id_carrera = $identidad;";
	        $runmaterias = mysqli_query($conn, $materias);
	        $array_materias = "<option value=''>Seleccionar Materia</option>";
	        while($raw = mysqli_fetch_row($runmaterias)){
                $cadeteTosen = substr($raw[0],0,8);
	            $array_materias.="
	            	<option value='$raw[0]'>
						{$cadeteTosen} - $raw[1]
	            	</option>      
	            ";
	        }
	        echo $array_materias;
        }
        if(isset($_POST['dataFalse'])){
            $identidad = $_POST['carrera'];
            $profe = "SELECT * FROM docente WHERE id_carrera = $identidad and idstatus = 1 AND permisos = 600 ORDER BY paterno";
            $cosasmias='<option value="">Seleccionar Docente</option>';
            $runprofe = mysqli_query($conn, $profe);
            while($raw = mysqli_fetch_row($runprofe)){
                  $cosasmias.="<option value={$raw[0]}>{$raw[2]} {$raw[3]} {$raw[1]}</option>";
            } 
            echo $cosasmias;
        }

        function fun_utf8($cadena){
            return $cadena;
            return utf8_decode($cadena);
        }
        function fun_utf8_pdf($cadena){
            //return $cadena;
            return utf8_decode($cadena);
        }
        function fun_json($msg){
            $respuesta = json_encode($msg);
            return $respuesta;
        }
?>