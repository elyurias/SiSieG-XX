<?php
include_once "../../vista/librerias/fpdf/fpdf.php";
include_once "../../vista/conexioninsertar.php";
@session_start();
$stats = "";
if($_SESSION['permisos']==660){
    $carr = $_SESSION['id_carrera'];
    $stats = 'id_carrera = '.$carr.' and';
}
$ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
$cicl = mysqli_query($conn, $ci);
$raa = mysqli_fetch_row($cicl);
$escolar = $raa[0];
$cies = preg_replace('([^A-Za-z0-9])', '', $escolar);

$time = time();

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
$pdf->SetTitle('Reporte General de Archivos');
$pdf->SetFont('Arial','',12);
//Aquí escribimos lo que deseamos mostrar
$pdf->Image('../../vista/img/teschaLogoNegro.png', 10, 10, 80, 28);
$pdf->Image('../../vista/img/elbalibre.png', 115, 10, 80, 28);
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 25);
$pdf->Cell(80,30,'',10,0,'C');
$pdf->Ln();
$pdf->Cell(190, 5,'Reporte General de Archivos',10,0,'C');
$pdf->Ln();

$docentesquery = mysqli_query($conn, "SELECT * FROM docente WHERE $stats IDSTATUS = 1 AND PERMISOS = 600 ORDER BY PATERNO");

while ($docenterow = mysqli_fetch_row($docentesquery)) {
    $pdf->Ln();
        $materiaquery = mysqli_query($conn, "SELECT * FROM asignarmateria WHERE MATRICULA = '$docenterow[0]' AND IDCICLO = '$escolar'");
        $materiasconteo = 0;
        while ($materiasrow = mysqli_fetch_row($materiaquery)) {
            $materiasconteo++;
        }
        $apoyoquery = mysqli_query($conn, "SELECT * FROM asignarapoyo WHERE MATRICULA = '$docenterow[0]' AND IDCICLO = '$escolar'");
        $apoyoconteo = 0;
        while ($apoyorow = mysqli_fetch_row($apoyoquery)) {
            $apoyoconteo++;
        }
        $visitaquery = mysqli_query($conn, "SELECT * FROM visitas WHERE MATRICULA = '$docenterow[0]' AND IDCICLO = '$escolar'");
        $visitaconteo = 0;
        while ($visitarow = mysqli_fetch_row($visitaquery)) {
            $visitaconteo++;
        }
        $materiasconteo2 = $materiasconteo*26;
        $apoyoconteo2 = $apoyoconteo*5;
        $visitaconteo2 = $visitaconteo*1;
        $totalasignados = $materiasconteo2+$apoyoconteo2+$visitaconteo2;
/*********************************************************************************************************************************/
        $totalmateriacumplida = 0;
        $materiadocquery = mysqli_query($conn, "SELECT * FROM asignarmateria WHERE MATRICULA = '$docenterow[0]' AND IDCICLO = '$escolar'");
        while ($materiadocrow = mysqli_fetch_row($materiadocquery)) {
            $matcumpquery = mysqli_query($conn, "SELECT * FROM archivos WHERE MATRICULA = '$materiadocrow[1]' AND IDGRUPO = '$materiadocrow[2]' AND IDCICLO = '$escolar' AND CLAVE = '$materiadocrow[4]'");
            while ($matcumrow = mysqli_fetch_row($matcumpquery)) {
                $totalmateriacumplida++;
            }
        }
        $totalapoyocumplida = 0;
        $apoyodocquery = mysqli_query($conn, "SELECT * FROM asignarapoyo WHERE MATRICULA = '$docenterow[0]' AND IDCICLO = '$escolar'");
        while ($apoyodocrow = mysqli_fetch_row($apoyodocquery)) {
            $apocumpquery = mysqli_query($conn, "SELECT * FROM archivosapoyodocencia WHERE MATRICULA = '$apoyodocrow[1]' AND IDCICLO = '$escolar'");
            while ($apocumrow = mysqli_fetch_row($apocumpquery)) {
                $totalapoyocumplida++;
            }
        }
        $totalvisitacumplida = 0;
        $visitadocquery = mysqli_query($conn, "SELECT * FROM visitas WHERE MATRICULA = '$docenterow[0]' AND IDCICLO = '$escolar'");
        while ($visitadocrow = mysqli_fetch_row($visitadocquery)) {
            $viscumpquery = mysqli_query($conn, "SELECT * FROM archivosvisitas WHERE MATRICULA = '$visitadocrow[4]' AND IDGRUPO = '$visitadocrow[6]' AND IDCICLO = '$escolar' AND LUGAR = '$visitadocrow[1]'");
            while ($viscumrow = mysqli_fetch_row($viscumpquery)) {
                $totalvisitacumplida++;
            }
        }
        $sumtotalsubido = $totalmateriacumplida+$totalapoyocumplida+$totalvisitacumplida;
        if($totalasignados > 0){
            $porcentajetotal = ($sumtotalsubido*100)/$totalasignados;
        }else{
            $porcentajetotal = 0;
        }
        
        if($porcentajetotal > 100){
            $porcentajetotal = 100;
        }

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(0, 128, 85);
        $pdf->Cell(190,10,$docenterow[2].' '.$docenterow[3].' '.$docenterow[1],1,0,'C', true);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(190,10, substr($porcentajetotal,0,4).'% COMPLETADO',1,0,'C', true);
        $pdf->Ln();
        //***********************************************ARCHIVOS SUBIDOS**********************************************************
            $pdf->SetFillColor(51, 255, 51);
            $pdf->Cell(190,10,'ARCHIVOS SUBIDOS',1,0,'C', true);
            $pdf->Ln();    
            $materiaquery = mysqli_query($conn, "SELECT * FROM asignarmateria WHERE MATRICULA = '$docenterow[0]' AND IDCICLO = '$escolar'");
            while ($materiadocrow = mysqli_fetch_row($materiaquery)) {
                $subido = mysqli_query($conn, "SELECT * FROM archivos WHERE MATRICULA = '$materiadocrow[1]' AND IDGRUPO = '$materiadocrow[2]' AND IDCICLO = '$escolar' AND CLAVE = '$materiadocrow[4]'");
                while($subidorow = mysqli_fetch_row($subido)){
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->SetFillColor(192, 192, 192);
                    $asigna = mysqli_query($conn, "SELECT * FROM materias WHERE CLAVE = '$subidorow[1]'");
                    $asrow = mysqli_fetch_row($asigna);
                    $pdf->Cell(95,5,utf8_decode($asrow[1]),1,0,'C', true);
                    $pdf->SetFillColor(224, 224, 224);
                    $pdf->Cell(95,5,utf8_decode($subidorow[8]),1,0,'C', true);
                    $pdf->Ln();
                }
            }
            $apoyoquery = mysqli_query($conn, "SELECT * FROM asignarapoyo WHERE MATRICULA = '$docenterow[0]' AND IDCICLO = '$escolar'");
            while ($apoyodocrow = mysqli_fetch_row($apoyoquery)) {
                $aposubido = mysqli_query($conn, "SELECT * FROM archivosapoyodocencia WHERE MATRICULA = '$apoyodocrow[1]' AND IDGRUPO = '$apoyodocrow[4]' AND IDCICLO = '$escolar' AND APOYO = '$apoyodocrow[2]'");
                while($aposubidorow = mysqli_fetch_row($aposubido)){
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->SetFillColor(192, 192, 192);
                    $pdf->Cell(95,5,utf8_decode($aposubidorow[2]),1,0,'C', true);
                    $pdf->SetFillColor(224, 224, 224);
                    $pdf->Cell(95,5,utf8_decode($aposubidorow[7]),1,0,'C', true);
                    $pdf->Ln();
                }
            }
            $visitaquery = mysqli_query($conn, "SELECT * FROM visitas WHERE MATRICULA = '$docenterow[0]' AND IDCICLO = '$escolar'");
            while ($visitadocrow = mysqli_fetch_row($visitaquery)) {
                $vissubido = mysqli_query($conn, "SELECT * FROM archivosvisitas WHERE MATRICULA = '$visitadocrow[4]' AND IDGRUPO = '$visitadocrow[6]' AND IDCICLO = '$escolar' AND LUGAR = '$visitadocrow[1]'");
                while($vissubidorow = mysqli_fetch_row($vissubido)){
                    $pdf->SetFont('Arial', 'B', 9);
                    $pdf->SetFillColor(192, 192, 192);
                    $pdf->Cell(95,5,utf8_decode($vissubidorow[4]),1,0,'C', true);
                    $pdf->SetFillColor(224, 224, 224);
                    $pdf->Cell(95,5,utf8_decode($vissubidorow[9]),1,0,'C', true);
                    $pdf->Ln();
                }
            }
            //***********************************************ARCHIVOS SUBIDOS**********************************************************
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->SetFillColor(255, 0, 0);
            $pdf->Cell(190,10,'ARCHIVOS RESTANTES',1,0,'C', true);
            $pdf->Ln();    
            $sql1 = "SELECT * FROM asignarmateria WHERE matricula = '$docenterow[0]' and idciclo = '$escolar'";
            $runsql1 = mysqli_query($conn, $sql1);

            while($row1 = mysqli_fetch_row($runsql1)){
                $sql2 = "SELECT idreporte FROM tiporeporte WHERE tiporeporte.idreporte not in (select idreporte from archivos where matricula = '$docenterow[0]' and clave = '$row1[4]' and idgrupo = '$row1[2]' and idciclo = '$escolar')";
                $runsql2 = mysqli_query($conn, $sql2);
            
            $pdf->SetFont('Arial', 'B', 9);
            while($row2 = mysqli_fetch_row($runsql2)){
                $pdf->SetFillColor(192, 192, 192);
                $pdf->Cell(20,5,utf8_decode($row1[2]),1,0,'C', true);//GRUPO
                        $sql3 = "SELECT * FROM materias WHERE CLAVE = '$row1[4]'";
                        $runsql3 = mysqli_query($conn, $sql3);
                        $row3 = mysqli_fetch_row($runsql3);
                $pdf->Cell(85,5,utf8_decode($row3[1]),1,0,'C', true);//MATERIA
                        $sql4 = "SELECT * FROM tiporeporte WHERE IDREPORTE = '$row2[0]'";
                        $runsql4 = mysqli_query($conn, $sql4);
                        $row4 = mysqli_fetch_row($runsql4);
                        $pdf->SetFillColor(224, 224, 224);
                $pdf->Cell(85,5,utf8_decode($row4[1]),1,0,'C', true);//TIPOREPORTE
                        $pdf->Ln(); 
                }
            }

            $sql5 = "SELECT * FROM asignarapoyo WHERE matricula = '$docenterow[0]' and idciclo = '$escolar'";
            $runsql5 = mysqli_query($conn, $sql5);

            while($row5 = mysqli_fetch_row($runsql5)){
                $sql6 = "SELECT idreportedocencia FROM reportedocencia WHERE reportedocencia.idreportedocencia not in (select idreportedocencia from archivosapoyodocencia where matricula = '$docenterow[0]' and apoyo = '$row5[2]' and idgrupo = '$row5[4]' and idciclo = '$escolar')";
                $runsql6 = mysqli_query($conn, $sql6);
                while($row6 = mysqli_fetch_row($runsql6)){
                $pdf->SetFillColor(192, 192, 192);
            if ($row5[4] == '1111') {
                $pdf->Cell(20,5,'N/A',1,0,'C', true);//GRUPO    
            }else{
                $pdf->Cell(20,5,$row5[4],1,0,'C', true);//GRUPO
            }  
            $pdf->Cell(85,5,$row5[2],1,0,'C', true);//APOYO
                    $sql7 = "SELECT * FROM reportedocencia WHERE IDREPORTEDOCENCIA = '$row6[0]'";
                    $runsql7 = mysqli_query($conn, $sql7);
                    $row7 = mysqli_fetch_row($runsql7);
                    $pdf->SetFillColor(224, 224, 224);
            $pdf->Cell(85,5,utf8_decode($row7[1]),1,0,'C', true);//TIPOREPORTE
                    $pdf->Ln(); 
                }
            }

            $sql8 = "SELECT * FROM visitas WHERE matricula = '$docenterow[0]' and idciclo = '$escolar'";
            $runsql8 = mysqli_query($conn, $sql8);

            while($row8 = mysqli_fetch_row($runsql8)){
                $sql9 = "SELECT idreportevisita FROM reportevisita WHERE reportevisita.idreportevisita not in (select idreportevisita from archivosvisitas where matricula = '$docenterow[0]' and lugar = '$row8[1]' and idgrupo = '$row8[6]' and idciclo = '$escolar')";
                $runsql9 = mysqli_query($conn, $sql9);
                while($row9 = mysqli_fetch_row($runsql9)){
                $pdf->SetFillColor(192, 192, 192);
            if ($row8[6] == '1111') {
                $pdf->Cell(20,5,'N/A',1,0,'C', true);//GRUPO    
            }else{
                $pdf->Cell(20,5,$row8[6],1,0,'C', true);//GRUPO
            }   
            $pdf->Cell(85,5,$row8[1],1,0,'C', true);//LUGAR
                    $sql10 = "SELECT * FROM reportevisita WHERE IDREPORTEVISITA = '$row9[0]'";
                    $runsql10 = mysqli_query($conn, $sql10);
                    $row10 = mysqli_fetch_row($runsql10);
            $pdf->SetFillColor(224, 224, 224);
            $pdf->Cell(85,5,utf8_decode($row10[1]),1,0,'C', true);//TIPOREPORTE
            $pdf->Ln(); 
                }
            }

}

$modo="I";
$nombre_archivo="reporte general de archivos $time.pdf"; 
$pdf->Output($nombre_archivo, $modo);
?>