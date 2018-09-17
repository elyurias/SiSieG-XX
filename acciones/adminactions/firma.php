<?php
	include_once "../../vista/conexion.php";
	if(!isset($_SESSION['matricula'])) die();
	$sql = "SELECT * FROM firmas_digitales WHERE fk_docente = ".$_SESSION['matricula']." LIMIT 1;";
	$pps = mysqli_query($conn, $sql);
	$rows = mysqli_num_rows($pps);
	if($rows[0]==0){
		$msg = array(
			'status'=>false,
			'message'=>'El usuario no tiene una firma registrada en el sistema'
		);
	}
	while($let=mysqli_fetch_row($pps)){
		$msg = array(
			'status'=>true,
			'message'=>'Firma registrada en el sistema',
			'fecha_de_emision'=>$let[2],
			'dirreccion'=>$let[1],
			'referencia'=>$let[0]
		);
	}
	if(isset($_POST['OPERACION'])){
		$carpeta = '../../FIRMAS/'.$_SESSION['matricula'];
		if (!file_exists($carpeta)) {
		    mkdir($carpeta, 0777, true);
		}
		$nameVal1 = explode(".",  $_FILES['foto']['name']);
        $nameVal1 = end($nameVal1);
        $formatos_validos = [
            'gif','jpg','jpe','jpeg','png'
        ];
        if(!in_array($nameVal1, $formatos_validos)){
        	$statusNormi = 4;
            $msg = array(
            	'status'=>false,
            	'stats' =>$_FILES['foto']['name'],
            	'message'=>'El archivo no tiene un formato valido'
            );
        }else{
        	$carpeta = $carpeta."/".$_SESSION['matricula'].".".$nameVal1;
        	if(move_uploaded_file($_FILES['foto']['tmp_name'], $carpeta)){
        		$sql_fn = "
        				INSERT INTO  
        					firmas_digitales   
        						(fk_docente, Vnombre) 
        						VALUES (".$_SESSION['matricula'].",'".$carpeta."')  
        				ON DUPLICATE KEY UPDATE 
        					fk_docente = ".$_SESSION['matricula'].", 
        					Vnombre = '".$carpeta."';
        		";
        		$op = mysqli_query($conn, 
        			$sql_fn
        		);
        		if($op){
        			$statusNormi = 1;
        			$msg = array(
	            		'status'=>true,
	            		'message'=>'Archivo almacenado en el servidor de manera satisfactoria'
            		);	
        		}else{
        			$statusNormi = 2;
           			$msg = array(
	            		'status'=>false,
	            		'sql'=>$sql_fn,
	            		'message'=>'Error al actualizar la base de datos, Verifique la informacion'
            		);	
        		}
        	}else{
        		$statusNormi = 3;
        		$msg = array(
	            	'status'=>false,
	            	'message'=>'El archivo no puede ser almacenado en el servidor'
            	);	
        	}
        }
        header('Location: ../../vista/administrador/nuevoeditar.php?variable=0&variable2=no&carrera='.$_SESSION['id_Carrera'].'&statusFoto='.$statusNormi);
	}
	echo fun_json($msg);