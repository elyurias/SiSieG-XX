var id_ = 0;
var grupo_ = 0;
var clave_ = 0;
var escolar_ = 0;
class docente{
	ver_documentos($id){
		$.post('../../acciones/adminactions/obtener_documentos_docente.php'
			,{id_docente:$id}
			,function(data){
				document.getElementById('id01').style.display='block';
				$("#documentos_totales_torales").html(data);
		});
	}
	ver_documentos_de_verdad($id, $grupo, $clave, $escolar){
		id_ = $id;
		grupo_ = $grupo;
		clave_ = $clave;
		escolar_ = $escolar;
		$.post('../../acciones/adminactions/obtener_documentos_docente.php?identidad=1'
			,{
				id: id_,
				grupo: grupo_,
				clave: clave_,
				escolar: escolar_
			}
			,function(data){
				document.getElementById('id01').style.display='block';
				$("#documentos_totales_torales").html(data);
		});	
	}
	ver_documento_crudo($url_to){
		var stringti = $url_to;
		var cadena = "<object width='100%' height='500px' trusted='yes' application='yes' id='obj' data='"+$url_to+"?#zoom=80&scrollbar=1&toolbar=1&navpanes=1'>";
		$("#documento_general_maximus").html(cadena);
		document.getElementById('id02').style.display='block';
	}
    firma(){
    	$.post('../../acciones/adminactions/firma.php'
			,{ }
			,function(data){
				var cuerpo = JSON.parse(data);
				$('#message').html(cuerpo.message);
				var mensaje = "";
				var btn = "";
				var img = "";
				if(cuerpo.status){
					$("#fecha_de_emision").html(cuerpo.fecha_de_emision);
					mensaje = 'Actualizar Firma';
					btn = 'onclick="docs.subirFirma()"';
					img = '<img src="'+cuerpo.dirreccion+'" height="100" width="200" border="0">';
					$("#dirreccion").html(img);
				}else{
					mensaje = 'Cargar Primera Firma';
					btn = 'onclick="docs.subirFirma()"';
				}
				$('#NuevaModificar').html('<button class="botonn" '+btn+' height="1px">'+mensaje+'</button>');
				document.getElementById('id01_t').style.display='block';
		});	
    }
    subirFirma(){
    	document.getElementById('id01_t').style.display='none';
    	document.getElementById('id01_tad').style.display='block';
    }
    subirFirmaFinal(){
    	$.post(
    		'../../acciones/adminactions/firma.php',
    		$('#foto_subida_total').serialize(),
    		function(data){
    		}
    	)
    }
    firmarDocumento(id){
    	swal({
			  title: "¿Estas seguro?",
			  text: "Firmar el documento!",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Si, Firmar el documento!",
			  closeOnConfirm: false,
			  html: false
		}, function(isConfirm){
			//window.location = '../../acciones/adminactions/firmar_documento.php?id_archivo='+id;
			//return;
			if(isConfirm){
				setTimeout(function() {
					$.post(
		    			'../../acciones/adminactions/firmar_documento.php',
			    		{
			    			firmar:'NewFirma',
			    			id_archivo: id
			    		},
			    		function(data){
			    			var json = JSON.parse(data);
			    			var msg = "SYSTEM_ERROR";
			    			switch(json.status){
			    				case '1':
			    					msg="Archivo Firmado";
							    break;
							    case '-1':
							    	msg="Archivo no Preparado para Firmar";
							    break;
							    case '0':
							    	msg="Error al firmar el Archivo";
							    break;
			    			}
			    			swal(msg,"","warning")
			    		}
    				);
    				docs.ver_documentos_de_verdad(id_, grupo_, clave_, escolar_);
  				}, 200);	
			}
		});
    }
    MensajeNoFirma(){
		swal("Necesitas subir una firma!",
		 "Necesitas subir una firma valida al Sistema.",
		 "error");
    }
    MensajeSiFirma(){
    	swal("El documento ya ha sido Firmado!",
		 "",
		 "success");	
    }
    Mensaje(msg, type){
    	swal(msg,
		 "",
		 type);	
    }
}
var docs = new docente();