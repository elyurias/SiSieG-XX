<?php
$idexcel = $_GET['idexcel']; 
include_once "../../vista/librerias/PHPExcel/Classes/PHPExcel.php";
include_once "../../vista/conexion.php";

$ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
$cicl = mysqli_query($conn, $ci);
$raa = mysqli_fetch_row($cicl);
$escolar = $raa[0];
$cies = preg_replace('([^A-Za-z0-9])', '', $escolar);

$time = time();

$rrr = mysqli_query($conn, "SELECT * FROM docente WHERE matricula = '$idexcel'");
$result = 0;
while ($roro = mysqli_fetch_row($rrr)) {
    $result++;
}

if ($result >= 0) {
$objPHPExcel = new PHPExcel();
   
//Informacion del excel

$docquery = mysqli_query($conn, "SELECT * FROM docente WHERE matricula = '$idexcel'");
$docrow = mysqli_fetch_row($docquery);

$tituloReporte = "REPORTE DEL DOCENTE ".$docrow[2]." ".$docrow[3]." ".$docrow[1];

$i = 1;  
$libro = $objPHPExcel->setActiveSheetIndex(0);

$titulos = array();
$pos = 0;
$matdocquery = mysqli_query($conn, "SELECT * FROM asignarmateria WHERE matricula = '$idexcel' ORDER BY idgrupo ASC");
while ($matdocrow = mysqli_fetch_row($matdocquery)) {
    $titulos[$pos] = $i; 
    $titulosColumnas = array('GRUPO', 'MATERIA', 'TIPO DE REPORTE', 'NOMBRE DEL ARCHIVO');
    $libro->setCellValue('A'.$i,  $titulosColumnas[0])  //Titulo de las columnas
    ->setCellValue('B'.$i,  $titulosColumnas[1])
    ->setCellValue('C'.$i,  $titulosColumnas[2])
    ->setCellValue('D'.$i,  $titulosColumnas[3]);
    $pos++;
    $i++;

    $filequery = mysqli_query($conn, "SELECT * FROM archivos WHERE matricula = '$matdocrow[1]' and idciclo = '$escolar' and clave = '$matdocrow[4]' and idgrupo = '$matdocrow[2]'");
    while ($filerow = mysqli_fetch_row($filequery)) {
        $matquery = mysqli_query($conn, "SELECT * FROM materias WHERE clave = '$matdocrow[4]'");
        $matrow = mysqli_fetch_row($matquery);
        $reportequery = mysqli_query($conn, "SELECT * FROM tiporeporte WHERE idreporte = '$filerow[3]'");
        $reporterow = mysqli_fetch_row($reportequery);
        $libro->setCellValue('A'.$i, "  ".$matdocrow[2]."  ")//grupo
            ->setCellValue('B'.$i, $matrow[1])//materia
            ->setCellValue('C'.$i, $reporterow[1])//tiporeporte
            ->setCellValue('D'.$i, $filerow[8]);//nombre archivo
        $i++;
    } $i++;
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
$columncolor = 0;
$matdocquery = mysqli_query($conn, "SELECT * FROM asignarmateria WHERE matricula = '$idexcel'");
while ($matdocrow = mysqli_fetch_row($matdocquery)) {
    $columncolor++;
    $filequery = mysqli_query($conn, "SELECT * FROM archivos WHERE matricula = '$matdocrow[1]' and idciclo = '$escolar' and clave = '$matdocrow[4]' and idgrupo = '$matdocrow[2]'");
    while ($filerow = mysqli_fetch_row($filequery)) {
        $columncolor++;
    }
    $columncolor++;
}
for ($i=2; $i <= $columncolor; $i++) { 
    $objPHPExcel->getActiveSheet()->calculateColumnWidths()->getStyle('A'.$i.':D'.$i.'')->applyFromArray($estiloTituloReporte);
}

$count = count($titulos);

for ($i=0; $i < $count; $i++) { 
    $objPHPExcel->getActiveSheet()->calculateColumnWidths()->getStyle('A'.$titulos[$i].':D'.$titulos[$i].'')->applyFromArray($estiloTituloColumnas);
}

foreach(range('A', 'D') as $column){
    $libro->getColumnDimension($column)->setAutoSize(true);
    $libro->getRowDimension('1')->setRowHeight(40);
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$docrow[1].$time.'.xls"');
header('Cache-Control: max-age=0');

$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');//Excel5
$objWriter->save('php://output');
exit;
}
?>