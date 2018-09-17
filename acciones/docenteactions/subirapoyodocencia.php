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
$apoyo = $_POST['apoyo'];

$tiporeporte = $_POST['tiporeporte'];
    $re = "SELECT * FROM reportedocencia WHERE idreportedocencia = '$tiporeporte'";
    $repo = mysqli_query($conn, $re);
    $repor = mysqli_fetch_row($repo);
    $reporte = $repor[1];
$grupo = $_POST['grupo'];
$carpetaDestino = "../../archivos/".$_SESSION['id_carrera']."/".$nom[2]." ".$nom[3]." ".$nom[1]." ".$nom[0]."/".$cies."/APOYO DOCENCIA/".$apoyo."/".$grupo."/".$reporte."/";

    # si hay algun archivo que subir
    if($_FILES["archivo2"]["name"][0])
    {
 
        # recorremos todos los arhivos que se han subido
        for($i=0;$i<count($_FILES["archivo2"]["name"]);$i++)
        {
 
            # si es un formato pdf
            if($_FILES["archivo2"]["type"][$i]=="application/pdf")
            {
 
                # si exsite la carpeta o se ha creado
                if(@mkdir($carpetaDestino, 0777, true) || file_exists($carpetaDestino))
                {
                    $namearchivo = $grupo.'_'.$reporte;
                    $origen=$_FILES["archivo2"]["tmp_name"][$i];
                    $destino=$carpetaDestino.$namearchivo.'.pdf';
                    # movemos el archivo
                    if(@move_uploaded_file($origen, $destino))
                    {
                        include_once "../../vista/conexion.php";
                        $destinofinal = ($destino);
                        $insertararchivo = "INSERT INTO archivosapoyodocencia(url, apoyo, idreportedocencia, matricula, idciclo, idgrupo, nombrearchivo) VALUES('$destinofinal', '$apoyo','$tiporeporte', '$matricula', '$escolar', '$grupo', '$namearchivo')";
                        
                        if(mysqli_query($conn, $insertararchivo)){
                            header('Location:/SAER/vista/docente/subirarchivos.php');
                        }else{
                            echo 'No se ha generado el sql';
                        }
                                        
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