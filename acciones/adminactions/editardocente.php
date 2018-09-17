<?php
include_once "../../vista/conexion.php";

$matricula = $_POST['matricula'];
$no = strtolower($_POST['nombre']);
$pa = strtolower($_POST['paterno']);
$ma = strtolower($_POST['materno']);
$correo = $_POST['correo'];
$permisos = $_POST['permisos'];
$status = $_POST['estado'];

$nom = strtoupper($no);
$nombre = strtr($nom, "àèìòùáéíóúçñäëïöü", "ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");

$pat = strtoupper($pa);
$paterno = strtr($pat, "àèìòùáéíóúçñäëïöü", "ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");

$mat = strtoupper($ma);
$materno = strtr($mat, "àèìòùáéíóúçñäëïöü", "ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");

    $actualizar = "UPDATE DOCENTE SET nombre = '$nombre', paterno = '$paterno', materno = '$materno', correo = '$correo', idstatus = '$status' WHERE matricula = '$matricula'";
	$run = mysqli_query($conn, $actualizar);
?>
        <script language='javascript'>
        alert('Datos Cambiados');   
        var pagina = '/SAER/vista/administrador/nuevoeditar.php?variable=0&variable2=no';
        function redireccionar(){
        location.href=pagina
        }
        setTimeout('redireccionar()', 500);
        </script>
        <?php
?>