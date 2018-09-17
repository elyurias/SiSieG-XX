<?php
// Get real path for our folder
include_once "../../vista/conexion.php";
set_time_limit(3600);
date_default_timezone_set('UTC');

$ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
$cicl = mysqli_query($conn, $ci);
$raa = mysqli_fetch_row($cicl);
$escolar = $raa[0];
$cies = preg_replace('([^A-Za-z0-9])', '', $escolar);

$idzip = $_GET['idzip'];

$time = time();

$docsql = "SELECT * FROM docente WHERE matricula = '$idzip'";
$docquery = mysqli_query($conn, $docsql);
while($row = mysqli_fetch_row($docquery)){
        $rootPath = realpath('../../archivos/'.$cies.'/'.$_SESSION['id_carrera']."/".$row[2].' '.$row[3].' '.$row[1].' '.$row[0].'/');
        // Initialize archive object
        if(file_exists($rootPath)){
            $zip = new ZipArchive();
            $zip->open('archivos '.$escolar.' '.$row[1].' '.$time.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Zip archive will be created only after closing object
            $zip->close();

            header("Content-type: application/octet-stream");
            header("Content-disposition: attachment; filename=archivos ".$escolar." ".$row[1]." ".$time.".zip");
            readfile('archivos '.$escolar.' '.$row[1].' '.$time.'.zip');
            // Por último eliminamos el archivo temporal creado
            unlink('archivos '.$escolar.' '.$row[1].' '.$time.'.zip');//Destruyearchivo temporal
        }else{
            ?>
            <script language='javascript'>
            alert('El docente no ha subido archivos en el ciclo actual');   
            var pagina = '../../vista/administrador/nuevoeditar.php?variable=0&variable2=no';
            function redireccionar(){
            location.href=pagina
            }
            setTimeout('redireccionar()', 500);
            </script>
            <?php
        }
        
}
?>