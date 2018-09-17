<?php
		session_start();
        // comprobamos que se haya iniciado la sesión
        if(isset($_SESSION['nombre']) && $_SESSION['permisos'] == 777) {
        $nombre = $_SESSION['nombre'];
        $matricula = $_SESSION['matricula'];
        include_once "../conexioninsertar.php";
        $ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
        $cicl = mysqli_query($conn, $ci);
        $raa = mysqli_fetch_row($cicl);
        $escolar = $raa[0];
?>
<!DOCTYPE html>
<html>
<header>
<title><?php echo $sistema." ".$nombre?></title>
<link rel="stylesheet" type="text/css" href="../estilos/menu.css">
<link rel="stylesheet" href="../estilos/tablas.css">
<link rel="stylesheet" href="../estilos/formulario.css">
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="../js/menu.js"></script>
	<form action="/SAER/acciones/adminactions/completarmaterias.php" method="POST">
		<h2>Seleccionar Docente</h2>
            <select name='docente' required>
                <option value="">Seleccionar Docente</option>
                <?php
                $profe = "SELECT * FROM docente WHERE idstatus = 1 AND permisos = 600 ORDER BY paterno";
                $runprofe = mysqli_query($conn, $profe);

                while($raw = mysqli_fetch_row($runprofe)){
                  ?>
                  <option value="<?php echo $raw[0]?>"><?php echo $raw[2].' '.$raw[3].' '.$raw[1]?></option>
                  <?php
                }
                ?>
            </select>
            <input type="text" name="ciclo" value="<?php echo $escolar ?>" readonly />
            <br>
            <input type="submit" name="submit" id="botonazo" value="Completar Archivos"/>
	</form>
</html>
<?php
		}else{
			header('location: /SAER/index.php');
		}
?>

<?php

?>