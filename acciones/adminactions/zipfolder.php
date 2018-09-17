<?php
// Get real path for our folder
include_once "../../vista/conexion.php";
set_time_limit(3600);
date_default_timezone_set('UTC');

$ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
$cicl = mysql_query($ci);
$raa = mysql_fetch_row($cicl);
$escolar = $raa[0];
$cies = preg_replace('([^A-Za-z0-9])', '', $escolar);

$time = time();

$rootPath = realpath('../../archivos');
// Initialize archive object
$zip = new ZipArchive();
$zip->open('archivos '.$escolar.' '.$time.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

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
header("Content-disposition: attachment; filename=archivos ".$escolar." ".$time.".zip");
readfile('archivos '.$escolar.' '.$time.'.zip');
// Por último eliminamos el archivo temporal creado
unlink('archivos '.$escolar.' '.$time.'.zip');//Destruyearchivo temporal
?>