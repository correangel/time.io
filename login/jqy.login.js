//--------------------------------------------------------------------------
// 	Autor: Ramon Sanchez;
// 	Para: RCD Resorts;
// 	Última Modificación: 19/Enero/2016;
//--------------------------------------------------------------------------
//	Contenido:
//	Controles, Efectos y funciones dinámicas para el sistema de login.
//--------------------------------------------------------------------------
//	Si algo te sirve, tomalo.
//--------------------------------------------------------------------------

//##########################################################################
// 	Document Ready
//##########################################################################
$(document).ready( function() {
	particlesJS.load('particulas', '/scripts/particles.json');
		//console.log('Iniciado');


		//######################################################################
		// 	Sobrescribo función que inserta título en dialogs
		// 	para que acepte y tranforme tags html.
		//######################################################################
		/*$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
		    _title: function(title) {
		        if (!this.options.title ) {
		            title.html("&#160;");
		        } else {
		            title.html(this.options.title);
		        }// end if
		    }//end
		}));*/

		//######################################################################
		// 	Llamo a Función que verifica si está logeado.
		// 	(Busca attr 'data-logeado' en el body).
		//######################################################################


		//######################################################################
		//	Trigger click btn_login
		//######################################################################
		$(document).on('click','button#btn_login.noerror', function(){
			var btn = $(this);
			do_login(btn);
		});
		//######################################################################
		//	Trigger al presionar la tecla enter sobre tb_pass
		//######################################################################
		$(document).on('keypress','#tb_pass', function(e){
			if( e.which == 13){
				var btn = $('button#btn_login');
				if (btn.hasClass('noerror')){
					btn.focus();
					do_login(btn);
				}//end if

			}//end if

		});

		$(document).on('keypress','#tb_user', function(e){
			if( e.which == 13){
				var tb = $('#tb_pass');
				tb.focus();
			}//end if
		});



});

//######################################################################
// 	Función que se ejecuta al iniciar la interfaz
//######################################################################
function check_login(from_close){
	from_close = from_close || false;

	if($('body').attr('data-logeado') == 0){
		var dum = $('#lelogin');
		var _title = ''
		if (from_close) {
			_title = dum.attr('data-title') + " - [<span class='error'> Requerido </span>]"
		}else{
			_title = dum.attr('data-title');
		}//end if

		var login = dum
			.load(dum.attr('data-href'))
			.dialog({
				autoOpen:false,
				title: _title,
				modal:true,
				resizable: false,
				width: 280,
				height: 225,
				close: function (){
					check_login(true) },
				show: {
		       		effect: "fade",
			        duration: 250
			      },
			      	hide: {
			        effect: "fade",
			        duration: 250
			      }
			});
		login.dialog('open');
		return false;
	}else{
		//console.log('Usuario logeado satisfactoriamente...');
		return false;
	}//end if

}//End Fucntion


function do_login(btn){
	btn.children('i.fa').removeClass('')
							.addClass('fa-1x fa-spinner fa-pulse')
							.delay(500)
							.queue(function(next){
		//$(this).remove();
		next();
		//console.log(1); //compruebo disparo de evento
		//---------------------------------------------
		//var jurl = $('#frm_login').attr('data-url');
		//var jkey = $('#frm_login').attr('data-llave');
		//var jgpo = $('#frm_login').attr('data-grupo');
		var juser = $('#frm_login input#tb_user').val();
		var jpass = $('#frm_login input#tb_pass').val();
		var url = $('#frm_login').attr('data-url');
		//---------------------------------------------
		var request = $.ajax({
			url: '/login/check.login.php',
			type: 'POST',
			dataType: 'json',
			data: {	user: juser,
					pass: jpass	},
		});
		request.done(function(data) {
			//console.log(data);
			if(data.login == "true"){
				var request_logeado = $.ajax({
					url: url,
					type: 'POST',
					dataType: 'json',
					data: {user: data.user, lifetime : data.lifetime, name: data.name, id_operator: data.id_operator}
				});
				request_logeado.done(function(ndata) {
					if(ndata.cook == true){
						console.log(ndata.solicitud);
						if( ndata.solicitud ){
							telon("/app/frm.main?solicitud=" + ndata.solicitud);
						}else{
							telon("/app/frm.main");
						}//endif
					}//end if
					//console.log(ndata);
				});
				request_logeado.fail(function(ndata) {
					console.log(ndata);
				});
			}else{
				_login_err($('#btn_login'), data.msg)
				//$("#frm_login").css("height",225);
				//$("#results").html(data.msg).removeClass('oculto');
			}//end if
			//console.log(data.msg);
			btn.children('i.fa').removeClass('fa-1x fa-spinner fa-pulse')
								.addClass('fa-user fa-1x');
		});
		request.fail(function(data) {
			console.log(data);
			console.log("Error de Programación de lado del Servidor - [PHP]");
		});
	});
}//end if


function telon(href){
	$('#frm_login').fadeOut(300, function() {
		$('#frm_login').addClass('oculto');
		$('canvas').fadeOut(300);
		//$('#particulas').animate({
		  //width: '250'
		  //,opacity: "toggle"
		//}, "slow" ,function(){
			$('body').fadeOut(300, function(){
				window.location.href = href;
			});
		//});
	});
}//end functions

function _login_err(_btn,_msg){
  _login_set_err('bg-azul-login','bg-rojo', 'fa-lock','fa-exclamation-triangle',_msg,_btn, function(){
    _btn.delay(3000).queue( function(next){
      _login_set_err('bg-rojo','bg-azul-login', 'fa-exclamation-triangle','fa-lock','Login',_btn, function(){
        _btn.addClass('noerror');
        delete _btn;
        return;
      });
      next();
    });
  });
};

function _login_set_err(  rem_class
                              , add_class
                              , rem_icon
                              , add_icon
                              , msg
                              , who
                              , callback){
  who.removeClass('noerror');
  who.removeClass(rem_class).addClass(add_class);
  who.children('i.fa').removeClass(rem_icon).addClass(add_icon);
  who.children('span.msg').html(msg);
  if(callback) {
      delete who;
      callback();
  }else{
    delete who;
    return;
  }//end if
};
