<?php
include_once "../../vista/conexion.php";
set_time_limit(3600);
date_default_timezone_set('UTC');

$cicloescolar = $_POST['ciclo'];
$cies = preg_replace('([^A-Za-z0-9])', '', $cicloescolar);

$time = time();

        $rootPath = realpath('../../archivos/'.$cies.'/'.$_SESSION['id_carrera']);
        // Initialize archive object
        if(file_exists($rootPath)){
            $zip = new ZipArchive();
            $zip->open('archivos '.$cicloescolar.' '.$time.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

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
            header("Content-disposition: attachment; filename=archivos ".$cicloescolar." ".$time.".zip");
            readfile('archivos '.$cicloescolar.' '.$time.'.zip');
            // Por último eliminamos el archivo temporal creado
            unlink('archivos '.$cicloescolar.' '.$time.'.zip');//Destruyearchivo temporal
        }else{
            ?>
            <script language='javascript'>
            alert('No existen archivos de este ciclo');   
            var pagina = '../../vista/administrador/descargar.php';
            function redireccionar(){
            location.href=pagina
            }
            setTimeout('redireccionar()', 500);
            </script>
            <?php
        }
?>