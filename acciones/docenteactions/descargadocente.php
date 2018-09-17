<?php 
include_once "../../vista/conexion.php";
$idarchivo = $_GET['archivo']; 

$archsql = "SELECT * FROM archivos WHERE idarchivo = '$idarchivo'";
$archquery = mysqli_query($conn, $archsql);
$filerow = mysqli_fetch_row($archquery);

$archivo = $filerow[8];

$ruta = $filerow[2];

if (is_file($ruta))
{
   header('Content-Type: application/force-download');
   header('Content-Disposition: attachment; filename='.$archivo);
   header('Content-Transfer-Encoding: binary');
   header('Content-Length: '.filesize($ruta));

   readfile($ruta);
}
else
   exit();
?>