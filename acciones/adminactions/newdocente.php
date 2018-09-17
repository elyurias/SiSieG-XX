<?php
include_once "../../vista/conexioninsertar.php";

$matricula = $_POST['matricula'];
$no = strtolower($_POST['nombre']);
$pa = strtolower($_POST['paterno']);
$ma = strtolower($_POST['materno']);
$correo = $_POST['correo'];
$permisos = $_POST['permisos'];
$status = $_POST['estado'];
$carrera = $_POST['id_carrera'];
$nom = strtoupper($no);
$nombre = strtr($nom, "àèìòùáéíóúçñäëïöü", "ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");

$pat = strtoupper($pa);
$paterno = strtr($pat, "àèìòùáéíóúçñäëïöü", "ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");

$mat = strtoupper($ma);
$materno = strtr($mat, "àèìòùáéíóúçñäëïöü", "ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");

$sql1 = "SELECT matricula FROM docente WHERE matricula = ".$matricula;
$conteo = 0;
$runsql1 = mysqli_query($conn, $sql1);
while ($row = mysqli_fetch_row($runsql1)) {
	$conteo++;
}

if($conteo > 0 || $permisos==777){
?>
		<script language='javascript'>
    	alert('Matricula Ya Registrada');   
    	var pagina = '/SAER/vista/administrador/nuevoeditar.php?variable=0&variable2=no';
        function redireccionar(){
        location.href=pagina
        }
        setTimeout('redireccionar()', 500);
    	</script>
<?php
}else{
	$insertar = "INSERT INTO docente VALUES('$matricula', '$nombre', '$paterno', '$materno', '$correo', '$matricula', '$matricula', '$permisos', '$status', $carrera)";
	$run = mysqli_query($conn, $insertar);
	?>
		<script language='javascript'>
    	alert('Su matricula es su nuevo usuario y contrase\u00f1a');
    	var pagina = '/SAER/vista/administrador/nuevoeditar.php?variable=0&variable2=no&carrera=<?php echo $carrera;?>';
        function redireccionar(){
        location.href=pagina
        }
        setTimeout('redireccionar()', 500);
    	</script>
	<?php
}
?>