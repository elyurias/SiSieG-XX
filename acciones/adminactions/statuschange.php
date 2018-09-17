<?php
include_once "../../vista/conexion.php";

$idstatus = $_GET['idstatus'];
$docsql = "SELECT * FROM docente WHERE matricula = '$idstatus'";
$docquery = mysqli_query($conn, $docsql);
$docrow = mysqli_fetch_row($docquery);

switch($docrow[8]) {
	case '1':
		$actsql = "UPDATE docente SET idstatus = 2 WHERE matricula = '$idstatus'";
		$actquery = mysqli_query($conn, $actsql);
		header('Location:/SAER/vista/administrador/nuevoeditar.php?variable=0&variable2=no');
		break;
	case '2':
		$actsql = "UPDATE docente SET idstatus = 3 WHERE matricula = '$idstatus'";
		$actquery = mysqli_query($conn, $actsql);
		header('Location:/SAER/vista/administrador/nuevoeditar.php?variable=0&variable2=no');
		break;
	case '3':
		$actsql = "UPDATE docente SET idstatus = 1 WHERE matricula = '$idstatus'";
		$actquery = mysqli_query($conn, $actsql);
		header('Location:/SAER/vista/administrador/nuevoeditar.php?variable=0&variable2=no');
		break;
	default:
		header('Location:/SAER/vista/administrador/nuevoeditar.php?variable=0&variable2=no');
		break;
}
?>