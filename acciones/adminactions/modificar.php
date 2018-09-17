<?php
	include_once "../../vista/conexion.php";
	$nombre  = $_POST['nombre'];
	$paterno = $_POST['paterno'];
	$materno = $_POST['materno'];
	$grado   = $_POST['grado'];
    $actualizar = "
    	UPDATE tb_c_carreras 
    		SET Vnombre_jc     = '$nombre' ,
    			Vapellido_p_jc = '$paterno',
    			Vapellido_m_jc = '$materno',
    			Vgrado_jc      = '$grado'
    	WHERE IDclave_carrera = ".$_SESSION['id_carrera'].";

    ";
    $json = array();
    $run = mysqli_query($conn, $actualizar);
    if($run){
    	$json = array(
    		'status'=>true,
    		'sql'=>$actualizar
    	);
    }else{
		$json = array(
    		'status'=>false,
    		'sql'=>$actualizar
    	);
    }
    $jsonstring = json_encode($json);
	echo $jsonstring;
	die();
?>