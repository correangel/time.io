//-----------------------------------------------------------------------------------
// 	Autor: Ramon Sanchez;
// 	Para: RCD Resorts;
// 	Ultima Modificación: 25/Julio/2014;
//-----------------------------------------------------------------------------------
//	Contenido: 
//	Controles, Efectos y funciones dinámicas para el sistema de login.
//-----------------------------------------------------------------------------------
//	Si algo te sirve, tomalo. 
//-----------------------------------------------------------------------------------
$(document).ready( function() { 	

	//-------------------------------------------------------------------------------
	//Eventos que llaman a hacer login en la aplicación
	//-------------------------------------------------------------------------------
	$('#btn_login').click(function(){
		$('#frm_login .visible').slideUp(300,function(){
			$(this).removeClass('visible').addClass('oculto');
			$('#frm_cargador.oculto').slideDown(300, function() {
				$(this).removeClass('oculto').addClass('visible');
				//do_login();
				do_login();
			});
			
		});
	});
	//-------------------------------------------------------------------------------
	$("#frm_login #password" ).keypress(function( event ) {
		if(event.which == 13){			
			$('#frm_login .visible').slideUp(300,function(){
				$(this).removeClass('visible').addClass('oculto');
				$('#frm_cargador.oculto').slideDown(300, function() {
					$(this).removeClass('oculto').addClass('visible');
					//do_login();
					do_login();
				});
			});			
			return false;
		}//end if	  	
	});	
	//-------------------------------------------------------------------------------
	//Función (Ajax) para hacer login en la aplicación.
	//-------------------------------------------------------------------------------
	

	function do_login(){
		$('#msg').slideUp(100, function(){
			$(this).html("");
			if ((!usuario.value == '') && (!password.value == '')) {
				
				juser = usuario.value;
				jpass = hex_sha512(password.value);
				jbandera = $('#btn_login').attr('data-bandera');
				//alert(jbandera);
				password.value = '';
				usuario.value = '';
				//alert(jpass);
				//do_cargar();
				var request = $.ajax({
					url:'login.proc.php',
					type:'POST',
					data:{user: juser, pass:jpass, ban: jbandera},
					dataType:'json'
				});
				request.done(function(data){
					if(data.status == 'error'){
						$('#msg').slideUp(100, function(){
							$('#msg').slideDown(300, function(){
								$(this).html("<p class='error'>"+data.msg+"</p>");
							});
						});	
							
						$('#frm_cargador.visible').slideUp(300, function() {
							$(this).removeClass('visible').addClass('oculto');					
							$('#frm_login #frm_campos.oculto').slideDown(300,function(){
								$(this).removeClass('oculto').addClass('visible');	

							});							
						});					
							
						
					}else if(data.status == 'success'){
						window.location.href = data.url;
					}//end if
				});
				request.error(function(msg){
					console.log(msg);
				});
			}else{				
				

				$('#msg').slideUp(100, function(){
					$('#msg').slideDown(300, function(){
						$(this).html("<p class='error'>Campos de Usuario y/o Contraseña están vacios!</p>");
					});
				});	
					
				$('#frm_cargador.visible').slideUp(300, function() {
					$(this).removeClass('visible').addClass('oculto');					
					$('#frm_login #frm_campos.oculto').slideDown(300,function(){
						$(this).removeClass('oculto').addClass('visible');	

					});							
				});	
			}//end if

		});
	}//end function
	//-------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------
	//Función para mostrar efecto de cargando al ejecutarse la funcion do_login
	//-------------------------------------------------------------------------------
	function do_cargar(){		
		var vis;
		var ocu;
		$('#frm_login .visible').slideUp(300,function(){
			vis = $(this);
			$('#frm_login .oculto').slideDown(300,function(){
				ocu = $(this);
				vis.removeClass('visible').addClass('oculto');
				ocu.removeClass('oculto').addClass('visible');
			});
		});		
	}//end function
	//-------------------------------------------------------------------------------
});