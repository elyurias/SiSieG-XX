<?php
	include_once "../../vista/librerias/fpdf/fpdf.php";
	include_once "../../vista/conexioninsertar.php";
	@session_start();
	$stats = "";
	$docente_hiperline = "  ";
	if($_SESSION['permisos']==660 || $_SESSION['permisos']==600){
	    $carr = $_SESSION['id_carrera'];
	    $stats = ' d.id_carrera = '.$carr.' and ';
	}else{
		if(!isset($_GET['id_carrera'])) exit();
		$carr = $_GET['id_carrera'];
	    $stats = ' d.id_carrera = '.$carr.' and ';
	}
	if($_SESSION['permisos'] == 600){
		$docente_hiperline = " a.matricula = '".$_SESSION['matricula']."' AND ";
	}
	if(isset($_GET['idpdf'])){
		$docente_hiperline = " a.matricula = '".$_GET['idpdf']."' AND ";
	}
	$ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
	$cicl = mysqli_query($conn, $ci);
	$raa = mysqli_fetch_row($cicl);
	$escolar = $raa[0];

	$time = time();
class PDF extends FPDF {
	function Header(){
		$this->Image('../../vista/img/teschaLogoNegro.png', 4, 4, 60, 20);
		$this->Image('../../vista/img/elbalibre.png', 150, -5, 60, 40);
	}

	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(0,10,fun_utf8_pdf('Página ').$this->PageNo().'/{nb}',0,0,'C');
	}
}

	// Instanciation of inherited class
	$pdf = new PDF();
	$pdf->AliasNbPages();
	
	$cadenaparati = "SELECT * FROM asignarmateria a 
					 INNER JOIN materias m on m.clave = a.clave
					 INNER JOIN docente d on a.matricula = d.matricula 
					 INNER JOIN tb_c_carreras th on d.id_carrera = th.IDclave_carrera 
						WHERE 
						$docente_hiperline 
						$stats 
						a.IDCICLO = '$escolar';";
	$materiaquery = mysqli_query($conn, $cadenaparati);
	$pdf->SetFont('Arial', 'B', 9);
	while ($materiadocrow = mysqli_fetch_row($materiaquery)) {
		$nombre_completo_jefe_de_carrera = fun_utf8_pdf($materiadocrow[27])." ".fun_utf8_pdf($materiadocrow[24])." ".fun_utf8_pdf($materiadocrow[25])." ".fun_utf8_pdf($materiadocrow[26]);
		$limitebase = 64;
		$avanceMateriasContador = 0;
		$avanceMateriasObtenido = 0;
		$pdf->AddPage();
		$pdf->Cell(80,20,'',10,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 15);
		$pdf->Cell(190, 5,'Reporte General de Archivos por Materia',10,0,'C');
		$pdf->SetFillColor(234, 250, 241);		
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(60,7,fun_utf8_pdf("CLAVE"),0,0,'L', true);
		$pdf->Cell(130,7,fun_utf8_pdf("MATERIA"),0,0,'R', true);
		$pdf->Ln();
		$pdf->SetFillColor(212, 239, 223);
		$pdf->Cell(60,7,fun_utf8_pdf("".$materiadocrow[5]),0,0,'L', true);
		$pdf->Cell(130,7,fun_utf8_pdf("".$materiadocrow[6]),0,0,'R', true);
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetFillColor(234, 250, 241);
		$pdf->Line(10, 49, 200, 49);
		$pdf->Cell(20,5,fun_utf8_pdf('N° DOCENTE') ,0, "","C", false);
		$pdf->Cell(20,5,fun_utf8_pdf('N° GRUPO') ,0, "","C", false);
		//$pdf->Write(10,'                                    ','');
		$pdf->Cell(50,5,'DOCENTE ENCARGADO' ,0, "","C", false);
		//$pdf->Write(10,'                                    ','');
		$pdf->Cell(50,5,'CREDITOS' ,0, "","C", false);
		//$pdf->Write(10,'                                    ','');
		$pdf->Cell(50,5,'PORCENTAJE DE AVANCE' ,0, "","C", false);
		$pdf->Ln();
		$pdf->SetFont('Arial', '', 7);
		$pdf->Line(10, 54, 200, 54);
		$pdf->Cell(20, 5, $materiadocrow[1] ,0, "","C", false);
		$pdf->Cell(20, 5, $materiadocrow[2] ,0, "","C", false);
		$docente_en_linea = fun_utf8_pdf($materiadocrow[11])." ".fun_utf8_pdf($materiadocrow[12])." ".fun_utf8_pdf($materiadocrow[13]);
		$pdf->Cell(50, 5, $docente_en_linea ,0, "","C", false);
		$pdf->Cell(50, 5, $materiadocrow[7] ,0, "","C", false);
		$pdf->Cell(60, 5, "            " ,0, "","C", false);
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(70, 4, fun_utf8_pdf("TIPO DE ARCHIVO") ,1, "","R", false);
		$pdf->Cell(70, 4, fun_utf8_pdf("NOMBRE DE ARCHIVO") ,1, "","R", false);
		$pdf->Cell(20, 4, fun_utf8_pdf("ESTADO") ,1, "","L", false);
		$pdf->Cell(30, 4, fun_utf8_pdf("FECHA") ,1, "","L", false);
		$pdf->Ln();
		$squere = "SELECT * FROM tiporeporte WHERE nombrereporte != ''";
		$subido = mysqli_query($conn, $squere);
		$id = $materiadocrow[1];
		$grupo = $materiadocrow[2];
		$clave = $materiadocrow[4];
		$pdf->Ln(1);
		$pdf->SetFont('Arial', '', 7);
		while($subidorow = mysqli_fetch_row($subido)){
			$limitebase+=5;
			$pdf->Cell(70, 5, fun_utf8_pdf($subidorow[1]) ,0, "","R", false);
	    	$datalib = "SELECT a.*, tmp.*, IFNULL(tmp.idreporte, 0) as identidad FROM archivos a
						RIGHT JOIN (
							SELECT * FROM tiporeporte
						) as tmp
						 on a.idreporte = tmp.idreporte	  
						  WHERE a.MATRICULA = '$id' 
							AND a.IDGRUPO = '$grupo' 
							AND a.IDCICLO = '$escolar' 
							AND a.CLAVE = '$clave'
							AND a.idreporte = {$subidorow[0]} LIMIT 1;";	
	    	$subidodos = mysqli_query($conn, $datalib);
	    	$urlruta = "../../vista/img/";
	    	$constantinopla = $urlruta."x.png";
	    	$cadenaMultiple = "";
	    	$fecha_de_registro = "";
	    	while($existeno = mysqli_fetch_row($subidodos)){
	    		$cadenaMultiple = $existeno[8];
				$constantinopla = $urlruta."ok.png";
				$avanceMateriasObtenido+=1;
				$fecha_de_registro = $existeno[9];
		    }
		    $pdf->Cell(70, 5, fun_utf8_pdf($cadenaMultiple) ,0, "","R", false);
		    $pdf->Cell(20, 5, $pdf->Image($constantinopla, $pdf->GetX(), $pdf->GetY(), 4), 0, 0, 'L', false );
		    $pdf->Cell(30, 5, $fecha_de_registro, 0, 0, 'L', false );
		    $pdf->Ln();
		    $avanceMateriasContador+=1;
		}
		$pdf->Ln();
		$pdf->Line(200, 49, 200, $limitebase);
		$pdf->Line($limitebase+6, $limitebase, 80, $limitebase);

		$pdf->Rect(20, 230, 60, 30, "D");
		$pdf->Rect(130, 230, 60, 30, "D");

		$pdf->SetXY(0, 250);
		$pdf->Ln();
		$pdf->Cell(80, 5, $nombre_completo_jefe_de_carrera ,0, "","C", false);
		$pdf->Cell(140, 5, $docente_en_linea ,0, "","C", false);
		$pdf->Ln();
		$pdf->Cell(80, 5, "NOMBRE Y FIRMA DEL JEFE DE CARRERA" ,0, "","C", false);
		$pdf->Cell(140, 5, "NOMBRE Y FIRMA DEL DOCENTE" ,0, "","C", false);

		$pdf->SetXY(170, 54);
		$pdf->Cell(10, 5, substr(fun_utf8_pdf($avanceMateriasObtenido*100/$avanceMateriasContador),0,5)."% COMPLETADO" ,0, "","C", false);
	}
	$apoyoquery = mysqli_query($conn, "SELECT * FROM asignarapoyo a inner join docente d on a.matricula = d.matricula  WHERE $docente_hiperline a.id_carrera = $carr AND a.IDCICLO = '$escolar'");
    while ($apoyodocrow = mysqli_fetch_row($apoyoquery)) {
        $avanceMateriasContador = 0;
		$avanceMateriasObtenido = 0;
		$pdf->AddPage();
		$pdf->Cell(80,20,'',10,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 15);
		$pdf->Cell(190, 5,'Reporte General de Archivos por Apoyo a Docencia',10,0,'C');
		$pdf->SetFillColor(234, 250, 241);		
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetFillColor(234, 250, 241);		
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(60,7,fun_utf8_pdf("N° DOCENTE"),0,0,'L', true);
		$pdf->Cell(130,7,fun_utf8_pdf("NOMBRE DEL DOCENTE"),0,0,'R', true);
		$pdf->Ln();
		$pdf->SetFillColor(212, 239, 223);
		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(60,7,fun_utf8_pdf("".$apoyodocrow[1]),0,0,'L', true);
		$pdf->Cell(130,7,fun_utf8_pdf($apoyodocrow[7])." ".fun_utf8_pdf($apoyodocrow[8])." ".fun_utf8_pdf($apoyodocrow[9]),0,0,'R', true);
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetFillColor(234, 250, 241);
		$pdf->Line(10, 47, 200, 47);
		$pdf->Cell(50,5,fun_utf8_pdf('N° GRUPO') ,0, "","R", false);
		$pdf->Cell(100,5,fun_utf8_pdf('TIPO DE APOYO') ,0, "","L", false);
		$pdf->Cell(40,5,'CICLO' ,0, "","R", false);
		$pdf->Ln();
		$pdf->SetFont('Arial', '', 7);
		$pdf->Line(10, 54, 200, 54);
		$pdf->Line(38, 59, 200, 59);
		$pdf->Cell(50, 5, $apoyodocrow[4] ,0, "","R", false);
		$pdf->Cell(100, 5, $apoyodocrow[2] ,0, "","L", false);
		$pdf->Cell(40, 5, $apoyodocrow[3],0, "","R", false);
		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(60, 5, "ARCHIVO" ,0, "","R", false);
		$pdf->Cell(60, 5, "NOMBRE DEL ARCHIVO" ,0, "","L", false);
		$pdf->Cell(20, 5, "ESTADO" ,0, "","L", false);
		$pdf->Cell(40, 5, "FECHA" ,0, "","L", false);
		$pdf->Ln(5);
		$pdf->Line(38, 63, 200, 63);
		$pdf->Line(38, 54, 38, 94);
		$pdf->Line(38, 94, 200, 94);
		$pdf->Line(200, 54, 200, 94);
		$reportesDocencia = mysqli_query($conn, "SELECT * FROM reportedocencia");
        $itemLine = "";
        $pdf->SetFont('Arial', '', 7);
        while($aposubidorow = mysqli_fetch_row($reportesDocencia)){
        	$itemLine = $aposubidorow[1];
        	$pdf->Cell(60, 5, $itemLine ,0, "","R", false);
        	$aposubido = mysqli_query($conn, "SELECT * FROM archivosapoyodocencia WHERE MATRICULA = '$apoyodocrow[1]' AND IDGRUPO = '$apoyodocrow[4]' AND IDCICLO = '$escolar' AND APOYO = '$apoyodocrow[2]' and idreportedocencia = '$aposubidorow[0]'");
        	$urlruta = "../../vista/img/";
	    	$constantinopla = $urlruta."x.png";
	    	$cadenaMultiple = "";
	    	$fecha_de_registroDos = "";
        	while($misarchivos = mysqli_fetch_row($aposubido)){
        		$cadenaMultiple = $misarchivos[7];
				$constantinopla = $urlruta."ok.png";
				$fecha_de_registroDos = $misarchivos[8];
        	}
        	$pdf->Cell(60, 5, fun_utf8_pdf($cadenaMultiple) ,0, "","L", false);
		    $pdf->Cell(20, 5, $pdf->Image($constantinopla, $pdf->GetX(), $pdf->GetY(), 4), 0, 0, 'R', false );
		    $pdf->Cell(20, 5, $fecha_de_registroDos, 0, 0, 'L', false );
        	$pdf->Ln();
        }
        $pdf->Rect(20, 230, 60, 30, "D");
		$pdf->Rect(130, 230, 60, 30, "D");

		$pdf->SetXY(0, 250);
		$pdf->Ln();
		$docente_en_linea = fun_utf8_pdf($apoyodocrow[7])." ".fun_utf8_pdf($apoyodocrow[8])." ".fun_utf8_pdf($apoyodocrow[9]);
		$pdf->Cell(80, 5, $nombre_completo_jefe_de_carrera ,0, "","C", false);
		$pdf->Cell(140, 5, $docente_en_linea ,0, "","C", false);
		$pdf->Ln();
		$pdf->Cell(80, 5, "NOMBRE Y FIRMA DEL JEFE DE CARRERA" ,0, "","C", false);
		$pdf->Cell(140, 5, "NOMBRE Y FIRMA DEL DOCENTE" ,0, "","C", false);
    }
$pdf->Output();
