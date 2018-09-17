<?php
	$identificacion = isset($_POST['id_docente']) ? $_POST['id_docente'] : $identificacion = $_SESSION['matricula'];
	$firmaSQL = "SELECT * FROM firmas_digitales WHERE fk_docente = ".$identificacion.' LIMIT 1;';
	$firmaQue = mysqli_query($conn, $firmaSQL);
	$validacion_de_firma = 0;
	while($raw_tsen = mysqli_fetch_row($firmaQue)){
		$validacion_de_firma = $raw_tsen[0];
	}
	$firmaTipo = $_SESSION['permisos'] == 660 ? 'JefeDeCarrera' : 'Docente';
?>