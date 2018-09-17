<?php // incluimos la libreria fpdf

    require('../adminactions/pdfgeneralDos.php');
    exit();
	session_start();
	include_once "../../vista/librerias/fpdf/fpdf.php";
    include_once "../../vista/conexion.php";
    $nombre = $_SESSION['nombre'];
    $matricula = $_SESSION['matricula'];

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
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AddPage();
$pdf->SetTitle('Reporte de Archivos');
$pdf->SetFont('Arial','',12);
//Aquí escribimos lo que deseamos mostrar
$pdf->Image('../../vista/img/teschaLogoNegro.png', 10, 10, 80, 28);
$pdf->Image('../../vista/img/elbalibre.png', 115, 10, 80, 28);
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 35);
$pdf->Cell(80,30,'',10,0,'C');
$pdf->Ln();
$pdf->Cell(180,10,'Reporte de Archivos',10,0,'C');
$pdf->Ln();
$pdf->Cell(0,10,'',10,0,'C');
/*--------------------------------------------------------------------------------------------------------------------------*/
            $materiasasignadas = "SELECT * FROM asignarmateria WHERE matricula = '$matricula' and idciclo = '$escolar'";
            $runasignarmateria = mysqli_query($conn, $materiasasignadas);
            $conteomaterias = 0;
            while ($res1 = mysqli_fetch_row($runasignarmateria)) {
                $conmat = "SELECT idreporte FROM tiporeporte WHERE tiporeporte.idreporte not in (select idreporte from archivos where matricula = '$matricula' and clave = '$res1[4]' and idgrupo = '$res1[2]' and idciclo = '$escolar')";
                $runcon = mysqli_query($conn, $conmat);
                while($res2 = mysqli_fetch_row($runcon)){
                    $conteomaterias ++;
                }
            }

            $apoyosasignadas = "SELECT * FROM asignarapoyo WHERE matricula = '$matricula' and idciclo = '$escolar'";
            $runasignarapoyo = mysqli_query($conn, $apoyosasignadas);
            $conteoapoyos = 0;
            while ($res3 = mysqli_fetch_row($runasignarapoyo)) {
                $conapo = "SELECT idreportedocencia FROM reportedocencia WHERE reportedocencia.idreportedocencia not in (select idreportedocencia from archivosapoyodocencia where matricula = '$matricula' and apoyo = '$res3[2]' and idgrupo = '$res3[4]' and idciclo = '$escolar')";
                $runconapo = mysqli_query($conn, $conapo);
                while($res4 = mysqli_fetch_row($runconapo)){
                    $conteoapoyos ++;
                }
            }

            $visitasasignadas = "SELECT * FROM visitas WHERE matricula = '$matricula' and idciclo = '$escolar'";
            $runasignarvisita = mysqli_query($conn, $visitasasignadas);
            $conteovisitas = 0;
            while ($res5 = mysqli_fetch_row($runasignarvisita)) {
                $convis = "SELECT idreportevisita FROM reportevisita WHERE reportevisita.idreportevisita not in (select idreportevisita from archivosvisitas where matricula = '$matricula' and lugar = '$res5[1]' and idgrupo = '$res5[6]' and idciclo = '$escolar')";
                $runconvis = mysqli_query($conn, $convis);
                while($res6 = mysqli_fetch_row($runconvis)){
                    $conteovisitas ++;
                }
            }
/*--------------------------------------------------------------------------------------------------------------------------*/
$totalfaltante = $conteomaterias+$conteoapoyos+$conteovisitas;

if($totalfaltante == 0){
$datadocente = "SELECT * FROM docente WHERE matricula = '$matricula'";
$rundata = mysqli_query($conn, $datadocente);
$rowdata = mysqli_fetch_row($rundata);
/*----------------------------------------------------------------------------------------------------------------------*/
$ma = "SELECT * FROM asignarmateria WHERE matricula = '$matricula' and idciclo = '$escolar'";
$runma = mysqli_query($conn, $ma);
$numma = 0;
while($ha = mysqli_fetch_row($runma)){
    $numma++;
}
/*----------------------------------------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------------------------------------*/
$ma2 = "SELECT * FROM asignarapoyo WHERE matricula = '$matricula' and idciclo = '$escolar'";
$runma2 = mysqli_query($conn, $ma2);
$numma2 = 0;
while($ha2 = mysqli_fetch_row($runma2)){
    $numma2++;
}
/*----------------------------------------------------------------------------------------------------------------------*/
/*----------------------------------------------------------------------------------------------------------------------*/
$ma3 = "SELECT * FROM visitas WHERE matricula = '$matricula' and idciclo = '$escolar'";
$runma3 = mysqli_query($conn, $ma3);
$numma3 = 0;
while($ha3 = mysqli_fetch_row($runma3)){
    $numma3++;
}
/*----------------------------------------------------------------------------------------------------------------------*/

$pdf->Ln();
$pdf->SetFont('Arial', 'B', 15);
$html='<b></b>El docente <b>'.$rowdata[2].' '.$rowdata[3].' '.$rowdata[1].'</b>, ha concluido su alta de reportes del ciclo '.$escolar.'. Teniendo a su cargo los siguientes rubros: ';
/*
$html='<b></b>El docente <b>'.$rowdata[2].' '.$rowdata[3].' '.$rowdata[1].'</b>, <i>itálica</i>,
<u>subrayado</u>, o ¡ <b><i><u>todos a la vez</u></i></b>!<br><br>También puede incluir enlaces en el
texto, como <a href="http://www.fpdf.org">www.fpdf.org</a>, o en una imagen: pulse en el logotipo.';
*/
$pdf->WriteHTML($html);
$pdf->Ln();
$pdf->Ln();
/*----------------------------------------------------------------------------------------------------------------------*/
if($numma > 0){
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetFillColor(68, 141, 0);
        $pdf->Cell(190,10,'MATERIAS',1,0,'C', True);
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(121, 198, 49);
    $pdf->Cell(20,5,'GRUPO',1,0,'C', true);
    $pdf->Cell(170,5,'MATERIA',1,0,'C', true);
    $pdf->Ln();
    $ma = "SELECT * FROM asignarmateria WHERE matricula = '$matricula' and idciclo = '$escolar'";
    $runma = mysqli_query($conn, $ma);
    while($uno = mysqli_fetch_row($runma)){
        $lamateria = "SELECT * FROM materias WHERE clave = '$uno[4]'";
        $runlamateria = mysqli_query($conn, $lamateria);
        $rowlamateria = mysqli_fetch_row($runlamateria);
    $pdf->Cell(20,5,$uno[2],1,0,'C');//GRUPO
    $pdf->Cell(170,5,$rowlamateria[1],1,0,'C');//LUGAR
    $pdf->Ln();
    }
}else{

}
/*----------------------------------------------------------------------------------------------------------------------*/
if($numma2 > 0){
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetFillColor(68, 141, 0);
        $pdf->Cell(190,10,'APOYO DOCENCIA',1,0,'C', True);
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(121, 198, 49);
    $pdf->Cell(20,5,'GRUPO',1,0,'C', true);
    $pdf->Cell(170,5,'APOYO',1,0,'C', true);
    $pdf->Ln();
    $ma = "SELECT * FROM asignarapoyo WHERE matricula = '$matricula' and idciclo = '$escolar'";
    $runma = mysqli_query($conn, $ma);
    while($uno = mysqli_fetch_row($runma)){
    if($uno[4] == '1111'){
        $pdf->Cell(20,5,'N/A',1,0,'C');//GRUPO
    }else{
        $pdf->Cell(20,5,$uno[4],1,0,'C');//GRUPO
    }
    $pdf->Cell(170,5,$uno[2],1,0,'C');//LUGAR
    $pdf->Ln();
    }
}else{

}
/*----------------------------------------------------------------------------------------------------------------------*/
if($numma3 > 0){
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetFillColor(68, 141, 0);//121, 198, 49
        $pdf->Cell(190,10,'VISITAS',1,0,'C', True);
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(121, 198, 49);
    $pdf->Cell(20,5,'GRUPO',1,0,'C', true);
    $pdf->Cell(170,5,'VISITA A: ',1,0,'C', true);
    $pdf->Ln();
    $ma = "SELECT * FROM visitas WHERE matricula = '$matricula' and idciclo = '$escolar'";
    $runma = mysqli_query($conn, $ma);
    while($uno = mysqli_fetch_row($runma)){
    if($uno[6] == 'N/A'){
        $pdf->Cell(20,5,'N/A',1,0,'C');//GRUPO
    }else{
        $pdf->Cell(20,5,$uno[6],1,0,'C');//GRUPO
    }
    $pdf->Cell(170,5,$uno[1],1,0,'C');//LUGAR
    $pdf->Ln();
    }
}else{

}
/*----------------------------------------------------------------------------------------------------------------------*/
$modo="I";
$nombre_archivo="reporte faltante $nombre $time.pdf"; 
$pdf->Output($nombre_archivo, $modo);

}else{
/*--------------------------------------------------------------------------------------------------------------------------*/
$cont1 = 0;
$sql1 = "SELECT * FROM asignarmateria WHERE matricula = '$matricula' and idciclo = '$escolar'";
$runsql1 = mysqli_query($conn, $sql1);
while($row1 = mysqli_fetch_row($runsql1)){
    $sql2 = "SELECT idreporte FROM tiporeporte WHERE tiporeporte.idreporte not in (select idreporte from archivos where matricula = '$matricula' and clave = '$row1[4]' and idgrupo = '$row1[2]' and idciclo = '$escolar')";
    $runsql2 = mysqli_query($conn, $sql2);
    
while($row2 = mysqli_fetch_row($runsql2)){
    $cont1++;
}
}
$cont2 = 0;
$sql5 = "SELECT * FROM asignarapoyo WHERE matricula = '$matricula' and idciclo = '$escolar'";
$runsql5 = mysqli_query($conn, $sql5);
while($row5 = mysqli_fetch_row($runsql5)){
$sql6 = "SELECT idreportedocencia FROM reportedocencia WHERE reportedocencia.idreportedocencia not in (select idreportedocencia from archivosapoyodocencia where matricula = '$matricula' and apoyo = '$row5[2]' and idgrupo = '$row5[4]' and idciclo = '$escolar')";
$runsql6 = mysqli_query($conn, $sql6);
while($row6 = mysqli_fetch_row($runsql6)){
    $cont2++;
}
}
$cont3 = 0;
$sql8 = "SELECT * FROM visitas WHERE matricula = '$matricula' and idciclo = '$escolar'";
$runsql8 = mysqli_query($conn, $sql8);
while($row8 = mysqli_fetch_row($runsql8)){
    $sql9 = "SELECT idreportevisita FROM reportevisita WHERE reportevisita.idreportevisita not in (select idreportevisita from archivosvisitas where matricula = '$matricula' and lugar = '$row8[1]' and idgrupo = '$row8[6]' and idciclo = '$escolar')";
    $runsql9 = mysqli_query($conn, $sql9);
while($row9 = mysqli_fetch_row($runsql9)){
    $cont3++;
}
}
/*--------------------------------------------------------------------------------------------------------------------------*/

if($cont1 > 0){
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 15);
        $html='<b></b>Le quedan por subir los siguientes archivos de materias:';
        /*
        $html='<b></b>El docente <b>'.$rowdata[2].' '.$rowdata[3].' '.$rowdata[1].'</b>, <i>itálica</i>,
        <u>subrayado</u>, o ¡ <b><i><u>todos a la vez</u></i></b>!<br><br>También puede incluir enlaces en el
        texto, como <a href="http://www.fpdf.org">www.fpdf.org</a>, o en una imagen: pulse en el logotipo.';
        */
        $pdf->WriteHTML($html);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(68, 141, 0);//121, 198, 49
            $pdf->Cell(190,10,'MATERIAS',1,0,'C', true);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(121, 198, 49);
            $pdf->Cell(20,5,'GRUPO',1,0,'C', true);
            $pdf->Cell(85,5,'MATERIA',1,0,'C', true);
            $pdf->Cell(85,5,'REPORTE POR SUBIR',1,0,'C', true);
        $pdf->Ln();

        $sql1 = "SELECT * FROM asignarmateria WHERE matricula = '$matricula' and idciclo = '$escolar'";
        $runsql1 = mysqli_query($conn, $sql1);

        while($row1 = mysqli_fetch_row($runsql1)){
        	$sql2 = "SELECT idreporte FROM tiporeporte WHERE tiporeporte.idreporte not in (select idreporte from archivos where matricula = '$matricula' and clave = '$row1[4]' and idgrupo = '$row1[2]' and idciclo = '$escolar')";
        	$runsql2 = mysqli_query($conn, $sql2);
        while($row2 = mysqli_fetch_row($runsql2)){
            $pdf->Cell(20,5,utf8_decode($row1[2]),1,0,'C');//GRUPO
                    $sql3 = "SELECT * FROM materias WHERE clave = '$row1[4]'";
                    $runsql3 = mysqli_query($conn, $sql3);
                    $row3 = mysqli_fetch_row($runsql3);
            $pdf->Cell(85,5,utf8_decode($row3[1]),1,0,'C');//MATERIA
                    $sql4 = "SELECT * FROM tiporeporte WHERE idreporte = '$row2[0]'";
                    $runsql4 = mysqli_query($conn, $sql4);
                    $row4 = mysqli_fetch_row($runsql4);
            $pdf->Cell(85,5,utf8_decode($row4[1]),1,0,'C');//TIPOREPORTE
            		$pdf->Ln(); 
            }
        }
}else{

}
/*--------------------------------------------------------------------------------------------------------------------------*/
if($cont2 > 0){
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 15);
        $html='<b></b>Le quedan por subir los siguientes archivos de apoyo a la docencia:';
        /*
        $html='<b></b>El docente <b>'.$rowdata[2].' '.$rowdata[3].' '.$rowdata[1].'</b>, <i>itálica</i>,
        <u>subrayado</u>, o ¡ <b><i><u>todos a la vez</u></i></b>!<br><br>También puede incluir enlaces en el
        texto, como <a href="http://www.fpdf.org">www.fpdf.org</a>, o en una imagen: pulse en el logotipo.';
        */
        $pdf->WriteHTML($html);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(68, 141, 0);//121, 198, 49
            $pdf->Cell(190,10,'APOYO DOCENCIA',1,0,'C', true);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(121, 198, 49);
            $pdf->Cell(20,5,'GRUPO',1,0,'C', true);
            $pdf->Cell(85,5,'TIPO DE APOYO',1,0,'C', true);
            $pdf->Cell(85,5,'REPORTE POR SUBIR',1,0,'C', true);
        $pdf->Ln();

        $sql5 = "SELECT * FROM asignarapoyo WHERE matricula = '$matricula' and idciclo = '$escolar'";
        $runsql5 = mysqli_query($conn, $sql5);

        while($row5 = mysqli_fetch_row($runsql5)){
            $sql6 = "SELECT idreportedocencia FROM reportedocencia WHERE reportedocencia.idreportedocencia not in (select idreportedocencia from archivosapoyodocencia where matricula = '$matricula' and apoyo = '$row5[2]' and idgrupo = '$row5[4]' and idciclo = '$escolar')";
            $runsql6 = mysqli_query($conn, $sql6);
            while($row6 = mysqli_fetch_row($runsql6)){
        if ($row5[4] == '1111') {
            $pdf->Cell(20,5,'N/A',1,0,'C');//GRUPO    
        }else{
            $pdf->Cell(20,5,$row5[4],1,0,'C');//GRUPO
        }  
        $pdf->Cell(85,5,$row5[2],1,0,'C');//APOYO
                $sql7 = "SELECT * FROM reportedocencia WHERE idreportedocencia = '$row6[0]'";
                $runsql7 = mysqli_query($conn, $sql7);
                $row7 = mysqli_fetch_row($runsql7);
        $pdf->Cell(85,5,$row7[1],1,0,'C');//TIPOREPORTE
                $pdf->Ln(); 
            }
        }
}else{

}
/*--------------------------------------------------------------------------------------------------------------------------*/
if($cont3 > 0){
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 15);
        $html='<b></b>Le quedan por subir los siguientes archivos de su(s) visita(s):';
        /*
        $html='<b></b>El docente <b>'.$rowdata[2].' '.$rowdata[3].' '.$rowdata[1].'</b>, <i>itálica</i>,
        <u>subrayado</u>, o ¡ <b><i><u>todos a la vez</u></i></b>!<br><br>También puede incluir enlaces en el
        texto, como <a href="http://www.fpdf.org">www.fpdf.org</a>, o en una imagen: pulse en el logotipo.';
        */
        $pdf->WriteHTML($html);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(68, 141, 0);//121, 198, 49
            $pdf->Cell(190,10,'VISITAS',1,0,'C', true);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(121, 198, 49);
            $pdf->Cell(20,5,'GRUPO',1,0,'C', true);
            $pdf->Cell(85,5,'TIPO DE APOYO',1,0,'C', true);
            $pdf->Cell(85,5,'REPORTE POR SUBIR',1,0,'C', true);
        $pdf->Ln();

        $sql8 = "SELECT * FROM visitas WHERE matricula = '$matricula' and idciclo = '$escolar'";
        $runsql8 = mysqli_query($conn, $sql8);

        while($row8 = mysqli_fetch_row($runsql8)){
            $sql9 = "SELECT idreportevisita FROM reportevisita WHERE reportevisita.idreportevisita not in (select idreportevisita from archivosvisitas where matricula = '$matricula' and lugar = '$row8[1]' and idgrupo = '$row8[6]' and idciclo = '$escolar')";
            $runsql9 = mysqli_query($conn, $sql9);
            while($row9 = mysqli_fetch_row($runsql9)){
        if ($row8[6] == '1111') {
            $pdf->Cell(20,5,'N/A',1,0,'C');//GRUPO    
        }else{
            $pdf->Cell(20,5,$row8[6],1,0,'C');//GRUPO
        }   
        $pdf->Cell(85,5,$row8[1],1,0,'C');//LUGAR
                $sql10 = "SELECT * FROM reportevisita WHERE idreportevisita = '$row9[0]'";
                $runsql10 = mysqli_query($conn, $sql10);
                $row10 = mysqli_fetch_row($runsql10);
        $pdf->Cell(85,5,$row10[1],1,0,'C');//TIPOREPORTE
        $pdf->Ln(); 
            }
        }
}else{

}
/*--------------------------------------------------------------------------------------------------------------------------*/

$modo="I";
$nombre_archivo="reporte faltante $nombre $time.pdf"; 
$pdf->Output($nombre_archivo, $modo);
}
?>