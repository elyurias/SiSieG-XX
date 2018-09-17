<?php
    session_start();
    include_once "../../vista/conexioninsertar.php";
    $nombre = $_SESSION['nombre'];
    $matricula = $_SESSION['matricula'];

    $ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
    $cicl = mysqli_query($conn, $ci);
    $raa = mysqli_fetch_row($cicl);
    $escolar = $raa[0];
    $cies = preg_replace('([^A-Za-z0-9])', '', $escolar);

    $lugar = $_POST['lugar'];
    $fecha = $_POST['fechavisita'];
    $fecha = date("Y-m-d", strtotime($fecha));
    $numalumnos = $_POST['numalumnos'];
    $grupo = $_POST['grupo'];

    $sqlvisita = "INSERT INTO visitas(lugar, fecha, numalumnos, matricula, idciclo, idgrupo) VALUES('$lugar','$fecha', '$numalumnos', '$matricula', '$escolar', '$grupo')";
    $runvisita = mysqli_query($conn, $sqlvisita);
    header('Location:/SAER/vista/docente/subirarchivos.php');                
?>