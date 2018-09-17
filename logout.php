<?php
    session_start();
        
    // comprobamos que se haya iniciado la sesión
    if(isset($_SESSION['nombre'])) {
        session_destroy();
        header("Location: index.php");
    }
?> 