<?php
include_once "../../vista/conexion.php";
$docente = $_POST['docente'];
$ciclo = $_POST['ciclo'];
$grupo = $_POST['grupo'];
$materia = $_POST['materia'];
    $insertar = "INSERT INTO asignarmateria(matricula, idgrupo, idciclo, clave) VALUES('$docente', '$grupo', '$ciclo', '$materia')";
    $run = mysqli_query($conn, $insertar);
    if($run){
    	$cts = 'Location:/SAER/vista/administrador/materias.php?ACTION&muestra='.$docente.'#matasignadas';
    }else{
    	$cts = 'Location:/SAER/vista/administrador/materias.php?NOACTION&muestra='.$docente.'#matasignadas';
    }
    header($cts);
?>