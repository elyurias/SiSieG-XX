<?php
	use setasign\Fpdi\Fpdi;		
	include_once "../../vista/conexion.php";
	include_once "../../vista/librerias/fpdf/fpdf.php";
	require_once('../../vista/librerias/fpdi/src/autoload.php');		
	$sql_documento = "SELECT * FROM archivos WHERE idarchivo = ".$_REQUEST['id_archivo'];
	$con_documento = mysqli_query($conn, $sql_documento);
	$nombre_documento = "";
	$ver_documento_status = 0;
	$docente = 0;
	while ($ems = mysqli_fetch_row($con_documento)) {
		$nombre_documento = $ems[2];
		$ver_documento_status = $ems[10];
		$docente = $ems[4];
	}
	if($ver_documento_status != 2 || !isset($_REQUEST['id_archivo'])) {
		echo "El documento no ha sido firmado por el Jefe de Carrera y el Docente, o el medio de acceso no es valido";
		die();
	}
	//$docente = $_SESSION['permisos']==660?$_REQUEST['id_docente']:$_SESSION['matricula'];
	$sqlGetFirmaJefeDeCarrera = "SELECT * FROM firmas_digitales fd 	inner join docente d on fd.fk_docente = d.matricula where d.id_carrera = ".$_SESSION['id_carrera']." LIMIT 1;";
	$sqlGetFirmaJefeDeDocente = "SELECT * FROM firmas_digitales WHERE fk_docente = ".$docente." LIMIT 1;";
	$ppsT = mysqli_query($conn, $sqlGetFirmaJefeDeCarrera);
	$url_firma_jefe = "../../vista/img/x.png";
	while ($ems = mysqli_fetch_row($ppsT)) {
		$url_firma_jefe = $ems[1];
	}
	$url_firma_docente = "../../vista/img/x.png";
	$ppsT2 = mysqli_query($conn, $sqlGetFirmaJefeDeDocente);
	while ($emst = mysqli_fetch_row($ppsT2)) {
		$url_firma_docente = $emst[1];
	}
	$pdf = new Fpdi();
	$pageCount = $pdf->setSourceFile($nombre_documento);
	for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
		$tplIdx = $pdf->importPage($pageNo);
		$pdf->AddPage();
		$pdf->useTemplate($tplIdx, 0, 0, 210);
		if($pageNo==$pageCount){

			$pdf->SetFont('Arial','B',7);
			$pdf->Rect(20, 220, 60, 30, "D");
			$pdf->Rect(130, 220, 60, 30, "D");
			$pdf->SetXY(0, 240);
			$pdf->Ln();
			$pdf->Cell(80, 5, ""  ,0, "","C", false);
			$pdf->Cell(140, 5, ""  ,0, "","C", false);
			$pdf->Ln();
			$pdf->Cell(80, 5, "NOMBRE Y FIRMA DEL JEFE DE CARRERA" ,0, "","C", false);
			$pdf->Cell(140, 5, "NOMBRE Y FIRMA DEL DOCENTE" ,0, "","C", false);

			$pdf->SetXY(27, 223);
			$pdf->Cell( 40, 40, $pdf->Image($url_firma_jefe, $pdf->GetX(), $pdf->GetY(), 35), 0, 0, 'L', false );
			$pdf->SetXY(150, 223);
			$pdf->Cell( 40, 40, $pdf->Image($url_firma_docente, $pdf->GetX(), $pdf->GetY(), 35), 0, 0, 'L', false );
		}
	}
	$pdf->Output();