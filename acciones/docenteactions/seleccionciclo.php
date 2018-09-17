<?php
    session_start();
    include_once "../../vista/conexion.php";
    $ci = "SELECT * FROM ciclos ORDER BY idciclo DESC LIMIT 1";
    $cicl = mysqli_query($conn, $ci);
    $raa = mysqli_fetch_row($cicl);
    $escolar = $raa[0];

    $actciclo = $_GET['varciclo'];
    $cicloescolar = array(                                
        0 => "No Existe Ciclo Anterior",     
        1 => "2017-1", 
        2 => "2017-2", 
        3 => "2018-1", 
        4 => "2018-2",
        5 => "2019-1", 
        6 => "2019-2", 
        7 => "2020-1", 
        8 => "2020-2",
        9 => "2021-1", 
        10 => "2021-2", 
        11 => "2022-1", 
        12 => "2022-2",
        13 => "2023-1", 
        14 => "2023-2", 
        15 => "2024-1", 
        16 => "2024-2",
        17 => "2025-1", 
        18 => "2025-2", 
        19 => "2026-1", 
        20 => "2026-2",
        21 => "2027-1", 
        22 => "2027-2", 
        23 => "2028-1", 
        24 => "2028-2",
        25 => "2029-1", 
        26 => "2029-2", 
        27 => "2030-1", 
        28 => "2030-2",
        29 => "2031-1", 
        30 => "2031-2", 
        31 => "2032-1", 
        32 => "2032-2",
        33 => "2033-1", 
        34 => "2033-2", 
        35 => "2034-1", 
        36 => "2034-2",
        37 => "2035-1", 
        38 => "2035-2", 
        39 => "2036-1", 
        40 => "2036-2",
        41 => "2037-1", 
        42 => "2037-2", 
        43 => "2038-1", 
        44 => "2038-2",
        45 => "2039-1", 
        46 => "2039-2",  
        47 => "2040-1", 
        48 => "2040-2",
        49 => "2041-1", 
        50 => "2041-2", 
        51 => "2042-1", 
        52 => "2042-2",
        53 => "2043-1", 
        54 => "2043-2", 
        55 => "2044-1", 
        56 => "2044-2",
        57 => "2045-1", 
        58 => "2045-2", 
        59 => "2046-1", 
        60 => "2046-2",
        61 => "2047-1", 
        62 => "2047-2", 
        63 => "2048-1", 
        64 => "2048-2",
        65 => "2049-1", 
        66 => "2049-2"
        );  
    $bdregistro = array_search($escolar, $cicloescolar); 

    if($actciclo == 0){
        ?>
        <script language='javascript'>
            alert('Este es el último ciclo');   
            var pagina = '/SAER/vista/docente/docente.php';
            function redireccionar(){
            location.href=pagina
            }
            setTimeout('redireccionar()', 500);
        </script>
        <?php
    }elseif($actciclo > $bdregistro){
        $newciclo = "INSERT INTO ciclos VALUES('$cicloescolar[$actciclo]');";
        $runnewciclo = mysqli_query($conn, $newciclo);
        header('Location:/SAER/vista/docente/docente.php');     
    }elseif($actciclo > $bdregistro){
        $oldciclo = "DELETE FROM ciclos WHERE idciclo = '$escolar'";
        $runoldciclo = mysqli_query($conn, $oldciclo);
        header('Location:/SAER/vista/docente/docente.php');     
    }
?>