<?php
include_once "../../vista/librerias/PHPExcel/Classes/PHPExcel.php";
include_once "../../vista/conexion.php";

$ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
$cicl = mysqli_query($conn, $ci);
$raa = mysqli_fetch_row($cicl);
$escolar = $raa[0];
$cies = preg_replace('([^A-Za-z0-9])', '', $escolar);

$time = time();

$objPHPExcel = new PHPExcel();

$tituloReporte = "REPORTE DE LOS DOCENTES";

$libro = $objPHPExcel->setActiveSheetIndex(0);

$queryprofesores = mysqli_query($conn, "SELECT * FROM docente $limitanteVisualDos $conectorY permisos = 600 AND matricula != 5565 ORDER BY paterno ASC");

$filanombre = array();
$pos = 0;
$fila = 1;
while($profesor = mysqli_fetch_array($queryprofesores)){
    $filanombre[$pos] = $fila; 
    
    $libro
    ->setCellValue('A'.$fila,  $profesor['matricula'])  //Titulo de las columnas
    ->setCellValue('B'.$fila,  $profesor['paterno'])
    ->setCellValue('C'.$fila,  $profesor['materno'])
    ->setCellValue('D'.$fila,  $profesor['nombre']);

    $fila++;

	$matriculaprofesor = $profesor['matricula'];

/*********************************************************************************************/
    $querymaterias = mysqli_query($conn, "SELECT * FROM asignarmateria WHERE matricula = '$matriculaprofesor' AND idciclo = '$escolar'");
    while($materiasasignadas = mysqli_fetch_row($querymaterias)){
    	$queryarchivosmaterias = mysqli_query($conn, "SELECT * FROM archivos WHERE matricula = '$matriculaprofesor' AND idciclo = '$escolar' AND clave = '$materiasasignadas[4]' AND idgrupo = '$materiasasignadas[2]'");
    	while($archivos = mysqli_fetch_row($queryarchivosmaterias)){
    		$fila++;
    		$querynombremateria = mysqli_query($conn, "SELECT nombre FROM materias WHERE clave ='$archivos[1]'");
    		$nombremateria = mysqli_fetch_row($querynombremateria);
    		$querynombrereporte = mysqli_query($conn, "SELECT nombrereporte FROM tiporeporte WHERE idreporte = '$archivos[3]'");
    		$nombrereporte = mysqli_fetch_row($querynombrereporte);
    		$libro
		    ->setCellValue('A'.$fila,  $nombremateria[0])
		    ->setCellValue('B'.$fila,  $archivos[7])
		    ->setCellValue('C'.$fila,  $nombrereporte[0])
		    ->setCellValue('D'.$fila,  $archivos[8]);
    	}
    }
/*********************************************************************************************/
	$queryapoyos = mysqli_query($conn, "SELECT * FROM asignarapoyo WHERE matricula = '$matriculaprofesor' AND idciclo = '$escolar'");
	while($apoyosasignados = mysqli_fetch_row($queryapoyos)){
		$queryarchivosapoyos = mysqli_query($conn, "SELECT * FROM archivosapoyodocencia WHERE matricula = '$matriculaprofesor' AND idciclo = '$escolar' AND apoyo = '$apoyosasignados[2]'");
		while($archivosapoyo = mysqli_fetch_row($queryarchivosapoyos)){
			$fila++;
			$grupo;
			if($archivosapoyo[6] == '1111'){
				$grupo = 'N/A';
			}else{
				$grupo = $archivosapoyo[6];
			}
			$querynamereporte = mysqli_query($conn, "SELECT nombre FROM reportedocencia WHERE idreportedocencia = '$archivosapoyo[3]'");
			$namereporte = mysqli_fetch_row($querynamereporte);
			$libro
		    ->setCellValue('A'.$fila,  $archivosapoyo[2])
		    ->setCellValue('B'.$fila,  $grupo)
		    ->setCellValue('C'.$fila,  $namereporte[0])
		    ->setCellValue('D'.$fila,  $archivosapoyo[7]);
		}
	}

/*********************************************************************************************/
	$queryvisitas = mysqli_query($conn, "SELECT * FROM visitas WHERE matricula = '$matriculaprofesor' AND idciclo = '$escolar'");
	while($visitasasignadas = mysqli_fetch_row($queryvisitas)){
		$queryarchivosvisitas = mysqli_query($conn, "SELECT * FROM archivosvisitas WHERE matricula = '$matriculaprofesor' AND idciclo = '$escolar' AND fecha = '$visitasasignadas[2]' AND lugar = '$visitasasignadas[1]'");
		while($archivosvisitas = mysqli_fetch_row($queryarchivosvisitas)) {
			$fila++;
			$libro
		    ->setCellValue('A'.$fila,  $archivosvisitas[4])
		    ->setCellValue('B'.$fila,  $archivosvisitas[8])
		    ->setCellValue('C'.$fila,  "REPORTE")
		    ->setCellValue('D'.$fila,  $archivosvisitas[9]);
		}
	}
	$fila++;
    $pos++;
}

$estiloTituloReporte = array(
    'font' => array(
        'name'      => 'Arial',
        'bold'      => false,
        'italic'    => false,
        'strike'    => false,
        'size' =>10,
        'color'     => array(
            'rgb' => '000000'
        )
    ),
    'fill' => array(
        'type'       => PHPExcel_Style_Fill::FILL_SOLID,
  'rotation'   => 0,
        'startcolor' => array(
            'rgb' => '3FC95F'
        ),
        'endcolor' => array(
            'argb' => '3FC95F'
        )
    ),
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_NONE
        )
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'rotation' => 0,
        'wrap' => TRUE
    )
);
 
$estiloTituloColumnas = array(
    'font' => array(
        'name'  => 'Arial',
        'bold'  => true,
        'italic'    => false,
        'strike'    => false,
        'size' => 12,
        'color' => array(
            'rgb' => '000000'
        )
    ),
    'fill' => array(
        'type'       => PHPExcel_Style_Fill::FILL_SOLID,
  'rotation'   => 0,
        'startcolor' => array(
            'rgb' => '25A519'
        ),
        'endcolor' => array(
            'argb' => '25A519'
        )
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
            'color' => array(
                'rgb' => '143860'
            )
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
            'color' => array(
                'rgb' => '143860'
            )
        )
    ),
    'alignment' =>  array(
        'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'      => TRUE
    )
);

$count = count($filanombre);

for ($i=0; $i < $count; $i++) { 
    $objPHPExcel->getActiveSheet()->calculateColumnWidths()->getStyle('A'.$filanombre[$i].':D'.$filanombre[$i].'')->applyFromArray($estiloTituloColumnas);
}

foreach(range('A', 'D') as $column){
    $libro->getColumnDimension($column)->setAutoSize(true);
    $libro->getRowDimension('1')->setRowHeight(40);
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename= REPORTE DOCENTES '.$time.'.xls');
header('Cache-Control: max-age=0');

$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');//Excel5
$objWriter->save('php://output');
exit;
?>