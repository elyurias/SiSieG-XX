<?php
    session_start();
    include_once "../../vista/conexion.php";
    $nombre = $_SESSION['nombre'];
    $matricula = $_SESSION['matricula'];

    $ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
    $cicl = mysqli_query($conn, $ci);
    $raa = mysqli_fetch_row($cicl);
    $escolar = $raa[0];
    $cies = preg_replace('([^A-Za-z0-9])', '', $escolar);
                # SE RECIBEN LAS VARIABLES
            $idarchivo = $_POST['idarchivo'];
            $clave = $_POST['clave'];
            $url = $_POST['url'];
            //$matricula = $_POST[''];
            $idrubro = $_POST['idrubro'];
            //$idciclo = $_POST[''];
            $idgrupo = $_POST['idgrupo'];
            $archivonombre = $_POST['nombrearchivo'];
#BUSCAR NOMBRE COMPLETO PROFESOR
$nc = "SELECT * FROM docente WHERE matricula = '$matricula'";
$ncq = mysqli_query($conn, $nc);
$ncrow = mysqli_fetch_row($ncq);
/*#BUSCAR NOMBRE DEL RUBRO
$nr = "SELECT * FROM rubros WHERE idgrupo = '$idrubro'";
$nrquery = mysqli_query($conn, $nr);
$nrrow = mysqli_fetch_row($nrquery);*/
#BUSCAR ASIGNATURA DATOS
$ad = "SELECT * FROM materias WHERE clave = '$clave'";
$adquery = mysqli_query($conn, $ad);
$adrow = mysqli_fetch_row($adquery);
#BUSCAR DATOS REPORTE
$dr1 = "SELECT idreporte FROM archivos WHERE idarchivo = '$idarchivo'";
$drquery1 = mysqli_query($conn, $dr1);
$drrow1 = mysqli_fetch_row($drquery1);
$idreporte = $drrow1[0];

$dr = "SELECT * FROM tiporeporte WHERE idreporte = '$idreporte'";
$drquery = mysqli_query($conn, $dr);
$drrow = mysqli_fetch_row($drquery);

unlink($url);

$carpetaDestino = "../../archivos/".$cies."/".$_SESSION['id_carrera']."/".$ncrow[2]." ".$ncrow[3]." ".$ncrow[1]." ".$ncrow[0]."/".$nrrow[1]."/".$adrow[1]."/".$idgrupo."/".$drrow[1]."/";

    # si hay algun archivo que subir
    if($_FILES["archivo1"]["name"][0])
    {
 
        # recorremos todos los arhivos que se han subido
        for($i=0;$i<count($_FILES["archivo1"]["name"]);$i++)
        {
 
            # si es un formato pdf
            if($_FILES["archivo1"]["type"][$i]=="application/pdf")
            {
 
                # si exsite la carpeta o se ha creado
                if(@mkdir($carpetaDestino, 0777, true) || file_exists($carpetaDestino))
                {
                    $namearchivo = $idgrupo.'_'.$drrow[1];
                    $origen=$_FILES["archivo1"]["tmp_name"][$i];
                    $destino=$carpetaDestino.$namearchivo.'.pdf';
 
                    # movemos el archivo
                    if(@move_uploaded_file($origen, $destino))
                    {
                        include_once "../../vista/conexion.php";
                        $destinofinal = ($destino);
                        
                        $actualizararchivomateria = "UPDATE archivos SET nombrearchivo = '$namearchivo', url = '$destinofinal' WHERE idarchivo = '$idarchivo'";
                        $queryarchivo = mysqli_query($conn, $actualizararchivomateria);
                        header('Location:/SAER/vista/docente/vermaterias.php');                
                    }else{
                        ?>
                        <script language='javascript'>
                        alert('No se ha podido subir el archivo');   
                        var pagina = '/SAER/vista/docente/vermaterias.php';
                        function redireccionar(){
                        location.href=pagina
                        }
                        setTimeout('redireccionar()', 500);
                        </script>
                        <?php
                    }
                }else{
                    ?>
                        <script language='javascript'>
                        alert('No se ha podido crear la carpeta');   
                        var pagina = '/SAER/vista/docente/vermaterias.php';
                        function redireccionar(){
                        location.href=pagina
                        }
                        setTimeout('redireccionar()', 500);
                        </script>
                        <?php
                }
            }else{
                ?>
                        <script language='javascript'>
                        alert('El archivo que intenta subir no es PDF');   
                        var pagina = '/SAER/vista/docente/vermaterias.php';
                        function redireccionar(){
                        location.href=pagina
                        }
                        setTimeout('redireccionar()', 500);
                        </script>
                        <?php
            }
        }
    }else{
        ?>
            <script language='javascript'>
            alert('No se ha subido ningun archivo');   
            var pagina = '/SAER/vista/docente/vermaterias.php';
            function redireccionar(){
            location.href=pagina
            }
            setTimeout('redireccionar()', 500);
            </script>
        <?php
    }
    ?>