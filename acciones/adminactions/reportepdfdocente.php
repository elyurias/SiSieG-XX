<?php
$idpdf = $_GET['idpdf']; 
include_once "../../vista/librerias/fpdf/fpdf.php";
include_once "../../vista/conexioninsertar.php";

$ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
$cicl = mysqli_query($conn, $ci);
$raa = mysqli_fetch_row($cicl);
$escolar = $raa[0];
$cies = preg_replace('([^A-Za-z0-9])', '', $escolar);

$time = time();

$docsql = "SELECT * FROM docente WHERE matricula = '$idpdf'";
$docquery = mysqli_query($conn, $docsql);
$docrow = mysqli_fetch_row($docquery);

class PDF extends FPDF
{

protected $B = 0;
protected $I = 0;
protected $U = 0;
protected $HREF = '';

function WriteHTML($html)
{
    // Intérprete de HTML
    $html = str_replace("\n",' ',$html);
    $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            // Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            // Etiqueta
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                // Extraer atributos
                $a2 = explode(' ',$e);
                $tag = strtoupper(array_shift($a2));
                $attr = array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])] = $a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    // Etiqueta de apertura
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF = $attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}

function CloseTag($tag)
{
    // Etiqueta de cierre
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF = '';
}

function SetStyle($tag, $enable)
{
    // Modificar estilo y escoger la fuente correspondiente
    $this->$tag += ($enable ? 1 : -1);
    $style = '';
    foreach(array('B', 'I', 'U') as $s)
    {
        if($this->$s>0)
            $style .= $s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
    // Escribir un hiper-enlace
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}
//Pie de página
function Footer()
{
$this->SetY(-10);
$this->SetFont('Arial','I',8);
$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'',0,0,'C');
}
}

$pdf=new PDF();
$pdf->AddPage();
$pdf->SetTitle('Reporte de Archivos');
$pdf->SetFont('Arial','',12);
//Aquí escribimos lo que deseamos mostrar
$pdf->Image('../../vista/img/teschaLogoNegro.png', 10, 10, 80, 28);
$pdf->Image('../../vista/img/elbalibre.png', 115, 10, 80, 28);
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 25);
$pdf->Cell(80,30,'',10,0,'C');
$pdf->Ln();
$pdf->Cell(190, 5,'Reporte de Archivos',10,0,'C');
$pdf->Ln();

$cont1 = 0;
$sql1 = "SELECT * FROM asignarmateria WHERE matricula = '$idpdf' and idciclo = '$escolar'";
$runsql1 = mysqli_query($conn, $sql1);
while($row1 = mysqli_fetch_row($runsql1)){
    $sql2 = "SELECT idreporte FROM tiporeporte WHERE tiporeporte.idreporte not in (select idreporte from archivos where matricula = '$idpdf' and clave = '$row1[4]' and idgrupo = '$row1[2]' and idciclo = '$escolar')";
    $runsql2 = mysqli_query($conn, $sql2);
    
while($row2 = mysqli_fetch_row($runsql2)){
    $cont1++;
}
}
$cont2 = 0;
$sql5 = "SELECT * FROM asignarapoyo WHERE matricula = '$idpdf' and idciclo = '$escolar'";
$runsql5 = mysqli_query($conn, $sql5);
while($row5 = mysqli_fetch_row($runsql5)){
$sql6 = "SELECT idreportedocencia FROM reportedocencia WHERE reportedocencia.idreportedocencia not in (select idreportedocencia from archivosapoyodocencia where matricula = '$idpdf' and apoyo = '$row5[2]' and idgrupo = '$row5[4]' and idciclo = '$escolar')";
$runsql6 = mysqli_query($conn, $sql6);
while($row6 = mysqli_fetch_row($runsql6)){
    $cont2++;
}
}
$cont3 = 0;
$sql8 = "SELECT * FROM visitas WHERE matricula = '$idpdf' and idciclo = '$escolar'";
$runsql8 = mysqli_query($conn, $sql8);
while($row8 = mysqli_fetch_row($runsql8)){
    $sql9 = "SELECT idreportevisita FROM reportevisita WHERE reportevisita.idreportevisita not in (select idreportevisita from archivosvisitas where matricula = '$idpdf' and lugar = '$row8[1]' and idgrupo = '$row8[6]' and idciclo = '$escolar')";
    $runsql9 = mysqli_query($conn, $sql9);
while($row9 = mysqli_fetch_row($runsql9)){
    $cont3++;
}
}
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
if($cont1 > 0){
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 15);
        $html='<b></b>El docente '.utf8_decode($docrow[2]).' '.utf8_decode($docrow[3]).' '.utf8_decode($docrow[1]).' ha subido los siguientes archivos en las materias:';
        $pdf->WriteHTML($html);
        $pdf->Ln();

        $matsql = "SELECT * FROM asignarmateria WHERE matricula = '$idpdf' and idciclo = '$escolar' ORDER BY clave";
        $matquery = mysqli_query($conn, $matsql);

        while ($matrow = mysqli_fetch_row($matquery)) {
        	$pdf->Ln();
	        $pdf->SetFont('Arial', 'B', 16);
	        $pdf->SetFillColor(68, 141, 0);

	        $nommatsql = "SELECT * FROM materias WHERE clave = '$matrow[4]'";
	        $nommatquery = mysqli_query($conn, $nommatsql);
	        $nommatrow = mysqli_fetch_row($nommatquery);

	        $pdf->Cell(190,10,utf8_decode($nommatrow[1]).' '.$matrow[2],1,0,'C', true);
	        $pdf->Ln();
	        $pdf->SetFont('Arial', 'B', 10);
	        $pdf->SetFillColor(121, 198, 49);
	            $pdf->Cell(95,5,'NOMBRE DE REPORTE',1,0,'C', true);
	            $pdf->Cell(95,5,'NOMBRE ARCHIVO',1,0,'C', true);
	        $pdf->Ln();

	        $filesql = "SELECT * FROM archivos WHERE matricula = '$idpdf' and idciclo = '$escolar' and clave = '$matrow[4]' and idgrupo = '$matrow[2]'";
	        $filequery = mysqli_query($conn, $filesql);
	        
	        while ($filerow = mysqli_fetch_row($filequery)) {
	        	$reportesql = "SELECT * FROM tiporeporte WHERE idreporte = '$filerow[3]'";
	        	$reportequery = mysqli_query($conn, $reportesql);
	        	$reporterow = mysqli_fetch_row($reportequery);
	        	$pdf->SetFont('Arial', 'B', 9);
	        	$pdf->Cell(95,5,utf8_decode($reporterow[1]),1,0,'C');
	        	$pdf->Cell(95,5,utf8_decode($filerow[8]),1,0,'C');
	        	$pdf->Ln();
	        }

        }
    }else{

}
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
if($cont2 > 0){
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 15);
        $html='<b></b>El docente '.utf8_decode($docrow[2]).' '.utf8_decode($docrow[3]).' '.utf8_decode($docrow[1]).' ha subido los siguientes archivos de los apoyos:';
        $pdf->WriteHTML($html);
        $pdf->Ln();

        $matsql = "SELECT * FROM asignarapoyo WHERE matricula = '$idpdf' and idciclo = '$escolar' ORDER BY apoyo";
        $matquery = mysqli_query($conn, $matsql);

        while ($matrow = mysqli_fetch_row($matquery)) {
        	$pdf->Ln();
	        $pdf->SetFont('Arial', 'B', 16);
	        $pdf->SetFillColor(68, 141, 0);
	        if($matrow[4] == '1111'){
				$pdf->Cell(190,10,utf8_decode($matrow[2]),1,0,'C', true);
	        }else{
				$pdf->Cell(190,10,utf8_decode($matrow[2]).' '.utf8_decode($matrow[4]),1,0,'C', true);
	        }
	        $pdf->Ln();
	        $pdf->SetFont('Arial', 'B', 10);
	        $pdf->SetFillColor(121, 198, 49);
	            $pdf->Cell(95,5,'NOMBRE DE REPORTE',1,0,'C', true);
	            $pdf->Cell(95,5,'NOMBRE ARCHIVO',1,0,'C', true);
	        $pdf->Ln();

	        $filesql = "SELECT * FROM archivosapoyodocencia WHERE matricula = '$idpdf' and idciclo = '$escolar' and apoyo = '$matrow[2]' and idgrupo = '$matrow[4]'";
	        $filequery = mysqli_query($conn, $filesql);
	        
	        while ($filerow = mysqli_fetch_row($filequery)) {
	        	$reportesql = "SELECT * FROM reportedocencia WHERE idreportedocencia = '$filerow[3]'";
	        	$reportequery = mysqli_query($conn, $reportesql);
	        	$reporterow = mysqli_fetch_row($reportequery);
	        	$pdf->SetFont('Arial', 'B', 9);
	        	$pdf->Cell(95,5,utf8_decode($reporterow[1]),1,0,'C');
	        	$pdf->Cell(95,5,utf8_decode($filerow[7]),1,0,'C');
	        	$pdf->Ln();
	        }

        }
    }else{

}
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
if($cont3 > 0){
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 15);
        $html='<b></b>El docente '.utf8_decode($docrow[2]).' '.utf8_decode($docrow[3]).' '.utf8_decode($docrow[1]).' ha subido los siguientes archivos en las materias:';
        $pdf->WriteHTML($html);
        $pdf->Ln();

        $matsql = "SELECT * FROM asignarmateria WHERE matricula = '$idpdf' and idciclo = '$escolar' ORDER BY clave";
        $matquery = mysqli_query($conn, $matsql);

        while ($matrow = mysqli_fetch_row($matquery)) {
        	$pdf->Ln();
	        $pdf->SetFont('Arial', 'B', 16);
	        $pdf->SetFillColor(68, 141, 0);

	        $nommatsql = "SELECT * FROM materias WHERE clave = '$matrow[4]'";
	        $nommatquery = mysqli_query($conn, $nommatsql);
	        $nommatrow = mysqli_fetch_row($nommatquery);

	        $pdf->Cell(190,10,utf8_decode($nommatrow[1]).' '.utf8_decode($matrow[2]),1,0,'C', true);
	        $pdf->Ln();
	        $pdf->SetFont('Arial', 'B', 10);
	        $pdf->SetFillColor(121, 198, 49);
	            $pdf->Cell(95,5,'NOMBRE DE REPORTE',1,0,'C', true);
	            $pdf->Cell(95,5,'NOMBRE ARCHIVO',1,0,'C', true);
	        $pdf->Ln();

	        $filesql = "SELECT * FROM archivos WHERE matricula = '$idpdf' and idciclo = '$escolar' and clave = '$matrow[4]' and idgrupo = '$matrow[2]'";
	        $filequery = mysqli_query($conn, $filesql);
	        
	        while ($filerow = mysqli_fetch_row($filequery)) {
	        	$reportesql = "SELECT * FROM tiporeporte WHERE idreporte = '$filerow[3]'";
	        	$reportequery = mysqli_query($conn, $reportesql);
	        	$reporterow = mysqli_fetch_row($reportequery);
	        	$pdf->SetFont('Arial', 'B', 9);
	        	$pdf->Cell(95,5,utf8_decode($reporterow[1]),1,0,'C');
	        	$pdf->Cell(95,5,utf8_decode($filerow[8]),1,0,'C');
	        	$pdf->Ln();
	        }

        }
    }else{

}

/*--------------------------------------------------------------------------------------------------------------------------*/

$modo="I";
$nombre_archivo="reporte faltante $idpdf $time.pdf"; 
$pdf->Output($nombre_archivo, $modo);
?>