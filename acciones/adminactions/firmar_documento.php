<?php
	include_once "../../vista/conexion.php";
	@session_start();
	include_once "extraer_firma.php";
	$data = array('status'=>'0');
	if(isset($_POST['firmar'])){
		$stats = 0;
		switch ($firmaTipo) {
			case 'JefeDeCarrera':
				$stats = 1;
			break;
			case 'Docente':
				$stats = 2;
			break;
		}
		$sql = "UPDATE archivos SET estado_firma = ".$stats." WHERE idarchivo = ".$_REQUEST['id_archivo'];
		$pps = mysqli_query($conn, $sql);
		if($pps){
			$data = array('status'=>'1');
		}else{
			$data = array('status'=>'0');
		}
	}else{
		$data = array('status'=>'-1');
	}
	echo fun_json($data);
?>