//-----------------------------------------------------------------------------------
// 	Autor: Ramón Sánchez;
// 	Para:
//-----------------------------------------------------------------------------------
//	Contenido:
//	Controles, Efectos y funciones dinámicas de la aplicación.
//-----------------------------------------------------------------------------------
$(document).ready( function() {
	$('.main.oculto').fadeIn(500, function(){	});


	if ($('#particulas').length > 0) {
		particlesJS.load('particulas', '/scripts/particles-light.json');
	}//end if


	$(document).on('click', 'body *', function(){
		var _act = $(this);
		switch ($(this).attr('id')) {
			case 'logout':
				window.location=($('#logout a').attr('href'));
				return;
				break;
			case 'sesion-panel':
				return;
				break;
			default:
				if($('#sesion-panel').hasClass('visible')){
					$('#sesion-panel').slideUp(300, function(){
						$('#sesion-panel').removeClass('visible').addClass('oculto');
					});
				}else{
					return;
				}//end if
				break;
		}//end switch
	});

	$(document).on('click', '#sesion', function(){
		if($('#sesion-panel').hasClass('oculto')){
			$('#sesion-panel').slideDown(300, function(){
				$('#sesion-panel').removeClass('oculto').addClass('visible');
			});
		}else{
			return;
		}//end if
	});
	$(document).on('mouseover', '.tab', function(){
		var _act = $(this);
		if (!_act.hasClass('active')){
			var _id = _act.attr('data-tab');
			var _title = _act.attr('data-title');
			if($('#tip-'+_id).length == 0){
				if (_title == undefined || _title.length == 0) return;
				var tip = "<div class='fs oculto tip boxshadow-low'></div>";
				var _tip = $(tip);
				var attrs ={position: 'absolute'};
				_tip.attr('id','tip-'+_id);
				_tip.html(_title);
				_tip.css(attrs);
				_tip.insertAfter(".tab[data-tab='" + _id + "']");
				_tip.fadeIn(100);
			}else return;
		}else return;

	});
	$(document).on('mouseleave', '.tab', function(){
		var _act = $(this);
		var _id = _act.attr('data-tab');
		$('#tip-'+_id).fadeOut(100, function(){
			$(this).remove();
		});
	});

	$(document).on('click', '.frm #frm-table .action', function(){
		var div = $(this);
		var action = div.attr('data-action');
		var id_col = div.attr('data-id-col');

		var id = div.attr('data-id');
		var dad = $("#"+id);
		var col = div.attr('data-col');
		var nav = $('#frm_wrapper .frm').attr('data-nav');
		var op = $('#frm_wrapper .frm').attr('data-op');

		switch (action) {
			case 'remove':
				tabla_action_remove(dad,id_col, id, nav, op);
				break;
			case 'active':

				break;
			default:
				break;
		}//end switch
		div= null;
	});
	$(document).on('click', '.frm #frm-add #frm-buttons .btn',function(e){
		e.preventDefault();
		var id = $(this).attr('id');
		switch (id) {
			case 'btn-add':
				btn_add_click();
				break;
			case 'btn-can':
				btn_can_click();
				break;
			default:
				return;
		}//end switch
		//console.log(1);
	});
	$(document).on('click', '.frm #frm-tabs .tab.isfrm', function(){
		//alert(1);
		if(!$(this).hasClass('active')){
			var div = $(this);
			var tab = div.attr('data-tab');
			$('.frm #frm-tabs .tab').removeClass('active');
			div.addClass('active');
			$('.istab.visible').fadeOut(300, function(){
				$(this).removeClass('visible').addClass('oculto');
				$("#"+tab).fadeIn(300).removeClass('oculto').addClass('visible');
			});
		}//end if
	});

	$(document).on('click', '#notificaciones .notif-item .btn', function(e){
		e.preventDefault();
		var div = $(this).parent();
		remove_notif(div);
	});

	$(window).resize(function () {
		if($('body').width() <= 720 && $('#sidebar').width()> 45){
			$('#brand #brand-collp').attr('data-collp',1);
			$('#sidebar .hide').fadeOut(100,function(){
				$('#sidebar').css('width','45px');
				$('#board').css('width', 'calc(100% - 45px)');
				$('#board').css('margin-left', '45px');
				$('#brand #brand-collp').fadeIn(100);
			});
		}//end if

		if($('body').width() > 720){
			$('#brand #brand-collp').fadeIn(100);
		}else{
			$('#brand #brand-collp').fadeOut(100);
		}//end if
	});

	//console.log(1);
	$('#brand #brand-collp').click( function(){
		var btn = $(this);
		resize_sidebar(btn);

	});

	$('#menu > .nav-father').click(function(){
		var act = $(this);

		if(!act.hasClass('active') && $('#sidebar').width() > 45){
			unactive_menu_item();
			act.addClass('active');
			act.children('.nav-children').slideDown(300);
			$(act.attr('data-icon-selector') + " i.fa-angle-right").removeClass('fa-angle-right').addClass('fa-angle-down');
			//act.children('i.fa.fa-angle-right').removeClass('fa-angle-right').addClass('fa-angle-down');

		}else{
			if(!act.hasClass('active') && $('#sidebar').width() == 45){
				unactive_menu_item();
				act.addClass('active');
				act.children('.nav-children').css('display','inline-block')
				act.children('.nav-children').addClass('showed');
				act.children('.nav-children').children('.child-item').css({
					'height':'45px'
					,'line-height': '45px'
					,'padding-left': '20px'
				});

			}else{
				unactive_menu_item();
			}//end if
		}//endif
	});
	$(document).on('click',"#sidebar #menu div[data-open='0']", function(e){
		//console.log(1);
		var act = $(this);
		open_frm(act);
	});

	$('#head #frm_wrapper .item').on('click',function(e){
		//console.log($(this).attr('id'));
		var item = $(this);

		switch (item.attr('id')){
			case 'sesion':
				//_show_notif('Prueba de click','fa-info','info');
				break;
			case 'extras':
				//console.log(1);
				if($('#content #tools_wrapper').hasClass('is-visible')){
					item.removeClass('active');
					item.children('i.fa').removeClass('fa-ellipsis-h').addClass('fa-ellipsis-v');
					$('#content #tools_wrapper').removeClass('is-visible').addClass('no-visible');
				}else{
					item.addClass('active');
					item.children('i.fa').removeClass('fa-ellipsis-v').addClass('fa-ellipsis-h');
					$('#content #tools_wrapper').removeClass('no-visible').addClass('is-visible');
				}//end if

				break;
			default:
				e.preventDefault();
				return;
		}//end switch
		//$('#content #tools_wrapper').slideToggle(500);
		//$('#content #tools_wrapper').css('width', '0px');

	});

});//end ready
//----------------------------------------------------------------------------------------------------------------------
function open_frm(act){

	var _url = act.attr('data-url');

	if (act.hasClass('child-item')) {
		var _ico = act.parent().parent().find('.dad-icon i.fa');
		var _ico_class = act.attr('data-icon');
	}else{
		var _ico = act.find('.sin-icon i.fa');
		var _ico_class = act.attr('data-icon');
	}//end if

	_ico.removeClass(_ico_class).addClass('fa-spinner fa-pulse');
	$("#sidebar #menu div[data-open='1'].active").removeClass('active');
	$("#sidebar #menu div[data-open='1']").attr('data-open', 0);
	$('body script').remove();

	act.attr('data-open', 1);
	act.addClass('active');
	var dest = $('#content #frm_wrapper');
	var titu = $('#board #head #frm_wrapper #titulo').html(act.attr('title'));
	var _id_nav = act.attr('id');
	var data = {id_nav: _id_nav};
	//var _id_op = $('#board #head #frm_wrapper #sesion ').attr('data-id');
	_call_ajax( _url , 'POST', 'json',
		data
		,function(result){ /*done*/
			if(result.status == 'ok'){
				dest.html(result.html);

				_ico.removeClass('fa-spinner fa-pulse').addClass(_ico_class);
				ajustar_widths(function(){
					$('div.frm.oculto #frm-tabs').hide();
					$('div.frm.oculto #frm-table').hide();
					$('div.frm.oculto').fadeIn(300,function(){
						$('div.frm.oculto #frm-tabs').fadeIn(100);
						$('div.frm.oculto #frm-table').fadeIn(100);
						$(this).removeClass('oculto');
					});
				});

				//console.log(data);
				result.html = null;
				result = null;
				delete window.result;

			}else{
				if (result.status == 'login'){
					window.location = result.url;
					//console.log(data);
				}else{
					if(result.responseText != undefined && result.responseText != '' ){
						dest.html(result.responseText);
						_ico.removeClass('fa-spinner fa-pulse').addClass(_ico_class);
					}else{
						console.log(result);
						_ico.removeClass('fa-spinner fa-pulse').addClass(_ico_class);
					}//end if;

				}// end if
			}//end if
		}
		,function(result){ /*fail*/
			if(result.responseText != undefined && result.responseText != '' ){
				dest.html(result.responseText);
				console.log(result);
			}else{
				console.log(result);
			}//end if;
		}, null, false);

	act = null;
}
function tabla_action_remove(div, jcol, jid, jnav, jop){
	$('#dialog-confirm').dialog({
		resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Eliminar": function() {
					$( this ).dialog( "close" );
					var _params = {type: 'del', col: jcol, id: jid,  nav: jnav, op: jop };
					_call_ajax('frm.default.proc.php'
										,'POST'	,'json', _params
										,function(data){
											_show_notif(data.msg, data.ico ,'info');
											console.log(data);
										},function(data){
											_show_notif(data.msg, data.ico ,'alert');
											console.log(data);
										}, null , false);
					div.remove();
        },
        "Cancelar": function() {
					$( this ).dialog( "close" );
        }
      }
	});
	/**/
}//end function


function btn_can_click(){
	$('#frm-add input.read').val('');
	$("#frm-add select.read").val('');
	var div = $(".frm #frm-tabs .tab[data-tab='frm-table']");
	var tab = div.attr('data-tab');
	$('.frm #frm-tabs .tab').removeClass('active');
	div.addClass('active');
	$('.istab.visible').fadeOut(300, function(){
		$(this).removeClass('visible').addClass('oculto');
		$("#"+tab).fadeIn(300).removeClass('oculto').addClass('visible');
	});

}//end function
function btn_add_click(){
	var _error = 0;
	var _msg = '';
	var _arry = {};
	$(".frm #frm-add [required='1']").each(function(){
		if($(this).val() == '' || $(this).val() == undefined){
			_msg = 'Datos Incompletos...'
			_error = 1;
		}//end if
	});

	if (_error === 1){
			_show_notif(_msg,'fa-warning','alert');
			return;
	}else{
		$(".frm #frm-add .read").each(function(){
			var asoc = $(this).attr('id');
			//console.log(asoc);
			var valu;
			if ($(this).attr('type')=='checkbox'){
				valu = 0
				if ($(this).is(":checked"))
					valu = 1;
			}else{
				valu = $(this).val();
			}//end if
			_arry[asoc]=valu;
		});
		//console.log(_arry);

		var jnav = $('.frm').attr('data-nav');
		var jop = $('.frm').attr('data-op');
		//console.log(jnav);
		//console.log(jop);
		var _params = {arry: _arry, type: 'add', nav: jnav , op: jop };
		_call_ajax('frm.default.proc.php'
							,'POST'	,'json', _params
							,function(data){
								_show_notif(data.msg, data.ico ,'add');
								console.log(data);
							},function(data){
								_show_notif(data.msg, data.ico ,'alert');
								console.log(data);
							}, null , false);
	}//endif
}//end function

function ajustar_widths(callback){
	var c = 0;
	//var a = [];
	var l = [];
	var i = $("#rows-head div.colm[data-ajustar='1']").length;
	var no = $("#rows-head div.colm[data-ajustar='0']").length;
	$("#rows-head div.colm").each(function(){
		//a[c] = $(this).attr("data-ajustar") != undefined ? 1 : 0;
		l[c] = $(this).attr("data-width")!= undefined ? parseInt($(this).attr("data-width")): 1;
		c++;
	});
	var m = Math.max(...l);
	var mn = 0;
	for (var x = c - 1; x >= 0; x--) {
		if (l[x] == m) mn++;
	}//end if
	var wm = 0;
	wm = (60 / mn);

	var wo = (100 - (wm*mn)) / (i - mn);
	var dm = 0;
	var di = 0;
	if (i>4) di = 64; else di = 63;
	if (no==2) di = 63.8;

	if (mn == 1){
		dm = ((c - i) * di) / (mn + 1);
	}else{
		dm = ((c - i) * di) / (mn) ;
	}//end if
	//console.log(dm);
	var od = (((c - i) * di) - (dm*mn)) / (i - mn);
	//var f = ((c - i) * 63)/ i;
	//console.log(od);
	$("#frm-table [data-ajustar='1']").each(function(){

		var div = $(this);
		div.removeAttr('style');
		if(div.attr('data-width') == m){
			//console.log(1);
			div.css('width', "calc("+wm+"% - "+dm+"px)");
		}else{
			//console.log(0);
			div.css('width', "calc("+wo+"% - "+od+"px)");
		}//end if
	});

	//console.log((od*(i - mn)) + dm);
	if(callback) callback();
}//end function
 function get_width_m(c,i,mn){
 	var wm = 0;
 	if(mn == i || i > 4) {
 		wm = 100/i;
 		return wm;
 	}//end if
 	return 60 / mn;
 }//end function


function resize_sidebar(btn){
	unactive_menu_item();
	$('.nav-children').css('display','block');
	$('.nav-children').css('display','none');
	$('.nav-children.showed').removeClass('showed');
	$('.nav-children .child-item').css({
		'height':'30px'
		,'line-height': '30px'
		,'padding-left': '50px'
	});
	//console.log(1);
	if(btn.attr('data-collp')==0){
		btn.attr('data-collp',1);
		$('#sidebar .hide').fadeOut(200, function() {
			$('#sidebar').css('width','45px');
			$('#board').css('width', 'calc(100% - 45px)');
			$('#board').css('margin-left', '45px');

		});

	}else{
		if($('body').width() > 720){
			btn.attr('data-collp',0);
			$('#board').css('width', 'calc(100% - 250px)');
			$('#board').css('margin-left', '250px');
			$('#sidebar').css('width', '250px');

			setTimeout(function(){
			  $('#sidebar .hide').fadeIn(200);
			}, 500);

		}//end if
	}//end if

}//end function

function unactive_menu_item(){

	$('#menu > .nav-father').each(function() {
		var th = $(this);
		th.removeClass('active');
		th.children('.nav-children').slideUp(300);
		$('#menu > .nav-father i.fa.fa-angle-down').removeClass('fa-angle-down').addClass('fa-angle-right');
	});


}//end function

function _call_ajax( _url , _type, _data_type, _params ,done, fail, always , verbose){
	//-----------------------------------
	_type = _type || 'POST';
	_data_type = _data_type || 'json'
	done = done || false;
	fail = fail || false;
	always = always || false;
	verbose = verbose || false;
	//-----------------------------------
	$.ajax({
		url: _url,
		type: _type,
		dataType: _data_type,
		data: _params
	})
	.done(function(data) {
		if (verbose) console.log(data);
		if (done) done(data);
	})
	.fail(function(data) {
		if (verbose) console.log(data);
		if (fail) fail(data);
	})
	.always(function(data) {
		if (verbose) console.log(data);
		if (always) always(data);
	});

}//end function


function _show_notif(_msg,_ico,_tipo){
	var data = {proc: 'show',msg: _msg, ico: _ico, tipo: _tipo }
	_call_ajax(
		'proc.notif.php'
		,'POST'
		,'json'
		,data
		,function(data){
			//console.log(data);
			var c = $('#notificaciones .notif-item').length;
			c = c * 45;
			$('#notificaciones').addClass(data.ubi);
			$('#notificaciones').append(data.div);
			proc_show_notif($("#"+data.id), data.delay, c);
		}
		,function(data){
			console.log(data);
		}
		,null,false
		);
}// end function

function proc_show_notif(div, _delay, c){
	div.css('left', 1000);
	div.removeClass('oculto');
	div.css('top', c);
	div.animate({
		left: 0},
		300, function() {
	}).delay(_delay).fadeOut(300, function(){
		remove_notif(div);
	});
}// end function

function remove_notif(div){
	div.remove();
	var c = 0;
	var i = 0;
	$('#notificaciones .notif-item').each(function(){
		c = i * 45;
		i++;
		$(this).css('top', c);
	});
}//end function

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}
