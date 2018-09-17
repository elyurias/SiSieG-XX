<?php
include_once "../../vista/conexion.php";
@session_start();
$matricula_identidad = 0;
if(isset($_GET['identidad'])){
	$id = $_POST['id'];
	$clave = $_POST['clave'];
	$escolar = $_POST['escolar'];
	$grupo = $_POST['grupo'];
	include_once "extraer_firma.php";
	$squere = "SELECT * FROM tiporeporte";
    $subido = mysqli_query($conn, $squere);
    $tabla = "<table>
					<thead>
						<tr>
							<th>TIPO DE ARCHIVO</th>
							<th>ARCHIVO</th>
							<th>ESTADO</th>
							<th>VER ARCHIVO</th>
							<th>FIRMAR</th>
							<th>ESTADO DE LA FIRMA</th>
							<th>VER DOCUMENTO CON FIRMAS</th>
						</tr>
					</thead
					<tbody>";
    while($subidorow = mysqli_fetch_row($subido)){
    	$datalib = "SELECT a.*, tmp.* FROM archivos a
					RIGHT JOIN (
						SELECT * FROM tiporeporte WHERE nombrereporte != ''
					) as tmp
					 on a.idreporte = tmp.idreporte	  
					  WHERE a.MATRICULA = '$id' 
						AND a.IDGRUPO = '$grupo' 
						AND a.IDCICLO = '$escolar' 
						AND a.CLAVE = '$clave'
						AND a.idreporte = {$subidorow[0]} 
						AND tmp.nombrereporte != ''
						LIMIT 1;";	
    	$subidodos = mysqli_query($conn, $datalib);
    	$nariz = "";
    	$status = "ledred.png";
    	$td = "vermateriagray.png";
    	$url = "#";
    	$btn = "no_encontrado.png";
    	$firmar = "vermateriagray.png";
    	$estilo = "style='cursor:not-allowed'";
    	$eleven = " href='#' ";
    	$evento = "";
    	$firmado = "El Documento Aun Sin Firmar";
    	$btn_ver_Firmado = "style='cursor:not-allowed'";
    	while($existeno = mysqli_fetch_row($subidodos)){
			$nariz = $existeno[8];
			$status = "ledgreen.png";
			$url = $existeno[2];
			$btn = "ACA-0909.png";
			$firmar = "vermateria.png";
			$estilo = "";
			$eleven = <<<EOT
				onclick="docs.ver_documento_crudo('$url')" 
EOT;
			$evento = $validacion_de_firma!=0 ? 'docs.firmarDocumento('.$existeno[0].');':'docs.MensajeNoFirma();';
			$firmado = $existeno[10]==0?'El Documento Aun Sin Firmar':'El documento Ya Fue Firmado';
			$evento = $existeno[10]==0?$evento:'docs.MensajeSiFirma();';
			$poder_firmar_docente = 0;
			if($_SESSION['permisos']==600){
				if($existeno[10]==1){
					$evento = $validacion_de_firma!=0 ? 'docs.firmarDocumento('.$existeno[0].');':'docs.MensajeNoFirma();';
				}else if($existeno[10]==0){
					$evento = 'docs.Mensaje("El jefe de carrera necesita firmar el documento primero!","warning");';
				}else if($existeno[10]==2){
					$evento = 'docs.Mensaje("El documento ya ha sido firmado!","success");';
				}
				//$evento = $validacion_de_firma!=0 ? 'docs.firmarDocumento('.$existeno[0].');':'docs.MensajeNoFirma();';
			}
			switch ($existeno[10]) {
				case 0:
					$firmado = "Nadie lo ha firmado";
				break;
				case 1:
					$firmado = "El Jefe de Carrera lo ha firmado";
				break;
				case 2:
					$firmado = "El Jefe de Carrera y el Docente lo han Firmado";
					$btn_ver_Firmado = " target='_blank' href='../../acciones/adminactions/ver_pdf_firmado.php?id_archivo=".$existeno[0]."' ";
					$td = "vermateria.png";
				break;
			}
	    }
	    if($subidorow[1]!=''){
		    $tabla.="
					<tr>
						<td data-label='TIPO DE ARCHIVO'>{$subidorow[1]}</td>
						<td data-label='ARCHIVO'>{$nariz}</td>
						<td data-label='ESTADO'><img border='0' src='/SAER/vista/img/{$status}' width='39' height='39'></td>
						<td data-label='VER ARCHIVO'>
							<a $eleven>
								<img border='0' $estilo src='/SAER/vista/img/{$btn}' width='39' height='39'>
							</a>
						</td>
						<td data-label='FIRMAR'>
							<a onclick='$evento'>
								<img border='0' $estilo src='/SAER/vista/img/{$firmar}' width='39' height='39'>
							</a>
						</td>
						<td data-label='ESTADO DE LA FIRMA'>
							$firmado
						</td>
						<td data-label='VER DOCUMENTO CON FIRMAS'>
							<a $btn_ver_Firmado>
								<img border='0' $btn_ver_Firmado src='/SAER/vista/img/{$td}' width='39' height='39'>
							</a>
						</td>
					</tr>";
		}
    }
    $tabla.="</tbody>
			</table>";
	echo $tabla;
	exit();
}
if(isset($_REQUEST['id_docente'])){
 	$matricula_identidad = $_REQUEST['id_docente'];
}else{
	exit();
}
$ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
$cicl = mysqli_query($conn, $ci);
$raa = mysqli_fetch_row($cicl);
$escolar = $raa[0];
$cadenaparati = "SELECT * FROM asignarmateria a 
					INNER JOIN materias m on m.clave = a.clave 
						WHERE a.MATRICULA = $matricula_identidad 
						AND a.IDCICLO = '$escolar';";
$materiaquery = mysqli_query($conn, $cadenaparati);
$tabla = "<table>
			<thead>
				<tr>
					<th>Grupo</th>
					<th>Clave</th>
					<th>Materia</th>
					<th>Creditos</th>
					<th>Ver Mas</th>
				</tr>
			</thead
			<tbody>";
while ($materiadocrow = mysqli_fetch_row($materiaquery)) {
	$clave_prima = substr($materiadocrow[4],0,8);
	$tabla.="
		<tr>
			<td data-label='GRUPO'>{$materiadocrow[2]}</td>
			<td data-label='CLAVE'>{$clave_prima}</td>
			<td data-label='MATERIA'>{$materiadocrow[6]}</td>
			<td data-label='CREDITOS'>{$materiadocrow[7]}</td>
			<td data-label='VER MAS'><img border='0' onclick=\"docs.ver_documentos_de_verdad({$materiadocrow[1]}, {$materiadocrow[2]}, '{$materiadocrow[4]}', '{$escolar}')\" src='/SAER/vista/img/ACA-0909.png' width='39' height='39'></td>
		</tr>
	";
}
$tabla.="</tbody>
		</table>";
echo $tabla;