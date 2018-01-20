$(document).ready( function() {
	sizing();
	particlesJS.load('particulas', '/scripts/particles.json');

	$( window ).resize(function() {
		sizing()
	});


	$(document).on('click','.grp_mn.enabled',function(e){
		var p = $(this);
		//$("script[data-drop='1']").remove();
		$('.grp_mn').each(function(){
			$(this).removeClass('disabled').addClass('enabled');
		});
		p.removeClass('enabled').addClass('disabled');
		var jrefresh = parseInt(p.attr('data-refresh')) / 1000;
		var new_i = p.attr('data-icon');
		var old_i = $('#frm_wrapper #icon').attr('data-icon');
		var new_t = p.attr('data-titulo');
		var old_t = $('#frm_wrapper #titulo').attr('data-icon');
		var funct = p.attr('data-function');

		$('#frm_wrapper #icon i.fa').removeClass(old_i).addClass(new_i);
		$('#frm_wrapper #icon').attr('data-icon',new_i);

		$('#frm_wrapper #titulo').html(new_t);
		$('#frm_wrapper #titulo').attr('data-titulo',new_t);

		$('#content #frm_wrapper').html('');

		var _url = p.attr('data-url');

		switch(_url){
			case 'frm.parameters.php':
				//console.log(window.timer_tcp);
				if (window.timeHDD != undefined && window.timer_hdd != undefined) {window.timeHDD(0,true)};
				if (window.timeTCP != undefined && window.timer_tcp != undefined) {window.timeTCP(0,true)};
				break;
			case 'frm.tcp.php':
				if (window.timeHDD != undefined && window.timer_hdd != undefined) {window.timeHDD(0,true)};
				break;
			case 'frm.hdd.php':
				if (window.timeTCP != undefined && window.timer_tcp != undefined) {window.timeTCP(0,true)};
				break;
		}//end switch

		$.ajax({
			url: _url,
			type: 'POST',
			dataType: 'html',
			data: {user: 'rsanchez'
					,refresh: jrefresh},
		})
		.done(function(data) {
			//console.log(data);

			$('#content #frm_wrapper').html(data)
			//setTimeout(funct, 100);

		})
		.fail(function(data) {
			console.log(data);
			$('#content #frm_wrapper').html(data);
		})

	});


	$(document).on('click','.gpo.add',function(){
		show_campos($(this), true)
	});


	$(document).on('click', '.gpo.add #buttons .btn', function(){
		var p = $(this);
		switch (p.attr('id')){
			case 'btn_save':
				var url = p.attr('data-url')
				click_btn_save(url);
				break;
			case 'btn_cancel':
				click_btn_cancel($('.gpo.add'));
				break;
			default:
				return;
				break;
		}//end switch

	});
});

function click_btn_save(_url){
	var _arry = {};
	_arry['add'] = 1;
	$('.gpo.add #add_wrapper #campos input').each(function(){
		if($(this).attr('type') == 'number'){
			_arry[$(this).attr('name')] = parseInt($(this).val());
		}else{
			_arry[$(this).attr('name')] = $(this).val();
		}//end if
	});
	//console.log(_arry);
	var parametros = JSON.stringify(_arry);
	//var parametros = {"add":1,"name":"qw","server":"qw","port":12,"user":"qw","pass":"qw","insert_user":"rsanchez"}
	console.log(parametros);
	$.ajax({
		url: _url,
		type: 'POST',
		dataType: 'JSON',
		contentType: 'application/json',
		data: parametros,
	})
	.done(function(data) {
		console.log(data);
	})
	.fail(function(data) {
		console.log(data);
	});

}//end fucntion

function click_btn_cancel(p){
	show_campos(p, false)
}//end fucntion


function show_campos(p, b){
	if(b === true){
		if(p.children('i.fa').hasClass('oculto')==false){
			p.children('i.fa').fadeOut(300, function() {
				p.children('i.fa').addClass('oculto');
				p.children('#add_wrapper').fadeIn(300,function(){
					p.children('#add_wrapper').removeClass('oculto');
				});
			});
		}//end if
	}else{
		if(p.children('i.fa').hasClass('oculto')==true){
			p.children('#add_wrapper').fadeOut(300, function() {
				p.children('#add_wrapper').addClass('oculto');
				p.children('i.fa').fadeIn(300,function(){
					p.children('i.fa').removeClass('oculto');

				});
			});
		}//end if
	}//end if
}//end function
function sizing(){
	switch (true) {
		case ($('#sidebar').width() < 230):
			//$('.grp_mn .txt').removeClass('enlinea').addClass('oculto');
		break;
		case ($('#sidebar').width() > 230):
			//$('.grp_mn .txt').removeClass('oculto').addClass('enlinea');
		break;
	}//end switch
}//end function

function show_mensaje(time, type, msg){
	var uuid = guid();
	var div = "<div id='mensajes' data-id='"+uuid+"' class='"+type+" fn oculto'>"+msg+"</div>";
	var pos = 0;

	$('#content').append(div);
	$("div#mensajes").each(function(){
		pos = pos + 50;
	});
	$("div#mensajes.oculto[data-id='"+uuid+"']").css('top',"calc(100% - "+pos+"px)");
	$("div#mensajes.oculto[data-id='"+uuid+"']").fadeIn(time, function() {
		$("div#mensajes.oculto[data-id='"+uuid+"']").removeClass('oculto');
		$("div#mensajes[data-id='"+uuid+"']").delay(time*4).fadeOut(time*2, function() {
			$("div#mensajes[data-id='"+uuid+"']").remove();
		});
	});

}//end function

function guid() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }
  return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
    s4() + '-' + s4() + s4() + s4();
}
