<?php
include_once "../../vista/conexion.php";
$matricula = $_POST['docente'];
$ciclo = $_POST['ciclo'];

$queryasignacion = mysqli_query($conn, "SELECT * FROM asignarmateria WHERE matricula = '$matricula' and idciclo = '$ciclo';");
while($row1 = mysqli_fetch_row($queryasignacion)) {
	$queryreportes = mysqli_query($conn, "SELECT * FROM tiporeporte");
	while($row2 = mysqli_fetch_row($queryreportes)){

		$nombrearchivo = $row1[2].'_'.$row2[1];
		//echo "INSERT INTO archivos(clave, url, idreporte, matricula, idrubro, idciclo, idgrupo, nombrearchivo) VALUES('$row1[4]', '$nombrearchivo', '$row2[0]', '$matricula', '1', '$ciclo', '$row1[2]', '$nombrearchivo')".'<br>';
		$queryarchivos = mysqli_query($conn, "INSERT INTO archivos(clave, url, idreporte, matricula, idrubro, idciclo, idgrupo, nombrearchivo) VALUES('$row1[4]', '$nombrearchivo', '$row2[0]', '$matricula', '1', '$ciclo', '$row1[2]', '$nombrearchivo')");
	}
}

$queryapoyos = mysqli_query($conn, "SELECT * FROM asignarapoyo WHERE matricula = '$matricula' and idciclo = '$ciclo'");
while($row3 = mysqli_fetch_row($queryapoyos)){
	$queryrepo = mysqli_query($conn, "SELECT * FROM reportedocencia");
	while($row4 = mysqli_fetch_row($queryrepo)){
		$nomarcapoy = $row3[4].'_'.$row4[1];
		/*echo "INSERT INTO archivosapoyodocencia(url, apoyo, idreportedocencia, matricula, idciclo, idgrupo, nombrearchivo) VALUES('$nomarcapoy', '$row4[1]', '$row4[0]', '$matricula', '$ciclo', '$row3[4]', '$nomarcapoy')".'<br>';*/
		
		$queryinsapo = mysqli_query($conn, "INSERT INTO archivosapoyodocencia(url, apoyo, idreportedocencia, matricula, idciclo, idgrupo, nombrearchivo) VALUES('$nomarcapoy', '$row4[1]', '$row4[0]', '$matricula', '$ciclo', '$row3[4]', '$nomarcapoy')");
	}
}
//
		if($queryarchivos || $queryinsapo){
			?>
			<script>
				alert('Has subido todos los archivos');
				location.href='../../vista/administrador/completar.php';          
			</script>
			<?php
		}



/*
SELECT * FROM asignarapoyo WHERE matricula = 153 and idciclo = '2017-2';
row3{
	idapoyo
	matricula
	apoyo
	idciclo
	idgrupo
}
SELECT * FROM archivosapoyodocencia a;
row5{
	idarchivo
	url
	apoyo
	idreportedocencia
	matricula
	ciclo
	idgrupo
	nombrearchivo
}
SELECT * FROM reportedocencia r;
row4{
	idreportedocencia
	nombre
}



*/
?>
