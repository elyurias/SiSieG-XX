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
    # definimos la carpeta destino
$sql = "SELECT * FROM docente WHERE matricula = '$matricula'";
$execsql = mysqli_query($conn, $sql);
$nom = mysqli_fetch_row($execsql);
$lugar = $_POST['lugar'];
$fecha = $_POST['fecha'];
$numalumnos = $_POST['numalumnos'];
$tiporeporte = $_POST['tiporeporte'];
    $re = "SELECT * FROM reportevisita WHERE idreportevisita = '$tiporeporte'";
    $repo = mysqli_query($conn, $re);
    $repor = mysqli_fetch_row($repo);
    $reporte = $repor[1];
$grupo = $_POST['grupo'];
$carpetaDestino = "../../archivos/".$cies."/".$_SESSION['id_carrera']."/".$nom[2]." ".$nom[3]." ".$nom[1]."  ".$nom[0]."/VISITAS/".$lugar."/".$grupo."/".$reporte."/";
    # si hay algun archivo que subir
    if($_FILES["archivo3"]["name"][0])
    {
 
        # recorremos todos los arhivos que se han subido
        for($i=0;$i<count($_FILES["archivo3"]["name"]);$i++)
        {
 
            # si es un formato pdf
            if($_FILES["archivo3"]["type"][$i]=="application/pdf")
            {
 
                # si exsite la carpeta o se ha creado
                if(@mkdir($carpetaDestino, 0777, true) || file_exists($carpetaDestino))
                {
                    $namearchivo = $grupo.'_'.$reporte;
                    $origen=$_FILES["archivo3"]["tmp_name"][$i];
                    $destino=$carpetaDestino.$namearchivo.'.pdf';
 
                    # movemos el archivo
                    if(@move_uploaded_file($origen, $destino))
                    {
                        include_once "../../vista/conexion.php";
                        $destinofinal = ($destino);
                        $insertararchivo = "INSERT INTO archivosvisitas(url, fecha, numalumnos, lugar, idreportevisita, matricula, idciclo, idgrupo, nombrearchivo) VALUES('$carpetaDestino', '$fecha', '$numalumnos', '$lugar', '$tiporeporte', '$matricula', '$escolar', '$grupo', '$namearchivo')";
                        $queryarchivo = mysqli_query($conn, $insertararchivo);
                        header('Location:/SAER/vista/docente/subirarchivos.php');                
                    }else{
                        ?>
                        <script language='javascript'>
                        alert('No se ha podido subir el archivo');   
                        var pagina = '/SAER/vista/docente/subirarchivos.php';
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
                        var pagina = '/SAER/vista/docente/subirarchivos.php';
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
                        var pagina = '/SAER/vista/docente/subirarchivos.php';
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
            var pagina = '/SAER/vista/docente/subirarchivos.php';
            function redireccionar(){
            location.href=pagina
            }
            setTimeout('redireccionar()', 500);
            </script>
        <?php
    }
    ?>