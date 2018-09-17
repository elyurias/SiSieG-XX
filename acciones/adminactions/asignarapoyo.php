<?php
include_once "../../vista/conexion.php";
$docente = $_POST['docente'];
$apoyo = $_POST['apoyo'];
$otroapoyo = $_POST['otroapoyo'];
$otroapoyo = ucwords($otroapoyo);
$ciclo = $_POST['ciclo'];
$grupo = $_POST['grupo'];
$carrera = $_POST['id_carrera'];

if(empty($apoyo) && empty($otroapoyo)){
	?>
	<script language='javascript'>
    	alert('Seleccione o escriba un tipo de apoyo');   
    	var pagina = '/SAER/vista/administrador/apoyodocencia';
        function redireccionar(){
        location.href=pagina
        }
        setTimeout('redireccionar()', 500);
    </script>
    <?php
}elseif(empty($apoyo)){

	$insertar = "INSERT INTO asignarapoyo(matricula, apoyo, idciclo, idgrupo, id_carrera) VALUES('$docente', '$otroapoyo', '$ciclo', '$grupo', $carrera)";
    $run = mysqli_query($conn, $insertar);
    header('Location:/SAER/vista/administrador/apoyodocencia.php');

}elseif(empty($otroapoyo)){
	$insertar = "INSERT INTO asignarapoyo(matricula, apoyo, idciclo, idgrupo, id_carrera) VALUES('$docente', '$apoyo', '$ciclo', '$grupo', $carrera)";
    $run = mysqli_query($conn, $insertar);
    header('Location:/SAER/vista/administrador/apoyodocencia.php');
}elseif(!empty($apoyo) && !empty($otroapoyo)){
	?>
	<script language='javascript'>
    	alert('Seleccione solo un tipo de apoyo');   
    	var pagina = '/SAER/vista/administrador/apoyodocencia.php';
        function redireccionar(){
        location.href=pagina
        }
        setTimeout('redireccionar()', 500);
    </script>
	<?php
}
 
 echo $insertar;   
?>