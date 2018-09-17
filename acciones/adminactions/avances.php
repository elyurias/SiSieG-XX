<?php
	include_once "../../vista/conexion.php";
	$id_carrera = $_SESSION['id_carrera'];
	$ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
	$cicl = mysqli_query($conn, $ci);
	$raa = mysqli_fetch_row($cicl);
	$ciclo_escolar = $raa[0];
	$docentes_materias = "SELECT 
								d.matricula,
								d.nombre,
								d.paterno,
								d.materno,
								d.correo,
								(SELECT COUNT(*) FROM 
									tiporeporte t 
										right join 
									archivos a
										on t.idreporte = a.idreporte
									WHERE 
										t.nombrereporte != '' AND 
										a.idciclo = '$ciclo_escolar' AND
										a.matricula = d.matricula
								) as ARCHIVOS_SUBIDOS,
								(SELECT COUNT(*) FROM 
									tiporeporte t 
										WHERE t.nombrereporte != ''
								) as ARCHIVOS_REQUERIDOS_POR_MATERIA,
								(SELECT COUNT(*) FROM
									asignarmateria WHERE idciclo = '$ciclo_escolar' AND d.matricula = matricula
								) as MATERIAS_ASIGNADAS
							FROM docente d
							WHERE d.id_carrera = $id_carrera AND d.permisos = 600;
						  ";
	$docquery = mysqli_query($conn, $docentes_materias);
	$docentes_array = [];
	while($row = mysqli_fetch_object($docquery))
	{
		array_push($docentes_array, $row);
	}
	header('Content-Type: application/json');
	echo json_encode($docentes_array);