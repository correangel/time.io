$(document).ready(function() {
  var pass_input_txt ="<div id='input_password' class='gpo fs bloque oculto'><span style='width:calc(20% - 10px);' class='enlinea fn td'>_password:</span><input id='_password' name='tb_password' type='password' class='read tran-bez-5s fn enlinea' /></div>";
  var pass_input =$(pass_input_txt);

  $(document).on('click','#frm-92579271-5567-441F-944C-8C9E232506AF #frm-add #frm-buttons #btn-add-operator',function(){

      var _id_nav = $('.frm').attr('data-nav');
      var _do_ope = $('.frm').attr('data-ope');
      var _employee_id = $('#employee_id').attr('data-value');
      var __username = $('#_username').val();
      var __domain = $('#_domain').val();
      var __name = $('#_name').val();
      var __lastname = $('#_lastname').val();
      var _id_role = $('#id_role').val();
      var _valid = true;
      if (   _id_nav == undefined
          || _do_ope == undefined
          || _employee_id == undefined
          || __username == undefined
          || __domain == undefined
          || __name == undefined
          || __lastname == undefined
          || _id_role == undefined) _valid = false;
      if (   _id_nav == ''
          || _do_ope == ''
          || _employee_id == ''
          || __username == ''
          || __domain == ''
          || __name == ''
          || __lastname == ''
          || _id_role == '') _valid = false;
      if (_valid === true) {
        var _add = {employee_id: _employee_id
                        ,_username:__username
                        ,_domain:__domain
                        ,_name:__name
                        ,_lastname:__lastname
                        ,id_role:_id_role };
        var _req = $.ajax({
          url: '/app/administrar/frm.usuarios.php',
          type: 'POST',
          dataType: 'json',
          data: {id_nav_proc: _id_nav, add:_add, do_ope: _do_ope }
        });
        _req.done(function(data){
          if(data.status == 'ok'){
            console.log(data);
            $('#frm-table #rows-body').html('');
            $('#frm-table #rows-body').append($(data.inserted));
            $("#frm-tabs div[data-tab='frm-table']").trigger('click');
            _show_notif('Usuario insertado correctamente...', 'fa-info' ,'add');
          }else{
            if (data.status == 'login'){
              window.location = data.url;
              //console.log(data);
            }else{
              if(data.responseText != undefined && data.responseText != '' ){
                dest.html(data.responseText);
              }else{
                console.log(data);
              }//end if
            }// end if
          }//end if
        });
        _req.fail(function(data){
          _show_notif('Usuario no insertado...', 'fa-warning' ,'alert');
          console.log(data);
        });
      }else{
        _show_notif('Datos Incompletos...', 'fa-warning' ,'alert');
      }//end if
  });
  $(document).on('click', '#frm-92579271-5567-441F-944C-8C9E232506AF #frm-add #_ad_user', function(){
    var _adu = $(this);
    if(_adu.attr('data-val')==1) _adu.attr('data-val',0); else _adu.attr('data-val',1);
    if(_adu.attr('data-val')==0){
      //console.log($('#frm-add #869B40AD-4589-4990-BCD1-8656328DB77A'));
      $('#frm-add #869B40AD-4589-4990-BCD1-8656328DB77A').slideUp(300,function(){
        pass_input.insertAfter('#frm-add #EEC596EA-3FE5-4C42-A8A8-4AED463D2845');
        $('#frm-add #input_password').slideDown(300);
      });
    }else{
      $('#frm-add #input_password').slideUp(300, function(){
        $(this).remove();
        $('#frm-add #869B40AD-4589-4990-BCD1-8656328DB77A').slideDown(300);
      });
    }
    //console.log($(this).attr('data-val'));
  });

		$(document).on('click', '.frm #frm-table #rows-body.usuarios .action', function(){
			var div = $(this);
			var action = div.attr('data-action');
			//var id_col = div.attr('data-id-col');

			var _id = div.attr('data-id');
			var _dad = $("#"+_id);
			//var _col = div.attr('data-col');
			var _id_nav = $('#frm_wrapper .frm').attr('data-nav');
			//var _ope = $('#frm_wrapper .frm').attr('data-ope');
      //console.log(1);
			switch (action) {
				case 'remove':
					_action_remove(_dad, _id, _id_nav);
					break;
        case 'tags':
          //console.log('tags');
          break;
				case 'active':

					break;
				default:
					break;
			}//end switch
			div= null;
		});
		var _action_remove = function(div, jid, _id_nav){
			$('#dialog-confirm').dialog({
				  resizable: false,
		      height: "auto",
		      width: 400,
		      modal: true,
					show: { effect: 'drop', direction: 'up' },
					hide: { effect: 'drop', direction: 'up' },
		      buttons: {
		        "Eliminar": function() {
							var _params = {action: 'delete', id: jid ,id_nav_proc: _id_nav };
							_call_ajax('/app/administrar/frm.usuarios.php'
												,'POST'	,'json', _params
												,function(data){
													_show_notif(data.msg, data.ico ,'info');
													console.log(data);
												},function(data){
													_show_notif(data.msg, data.ico ,'alert');
													console.log(data);
												}, null , false);
							div.remove();
							$( this ).dialog( "close" );
		        },
		        "Cancelar": function() {
							$( this ).dialog( "close" );
		        }
		      }
			});
		}//end function
//------------------------------------------------------------------------------------------------------------
// trigger dialog editar en form usuarios
//------------------------------------------------------------------------------------------------------------
$(document).on('dblclick', '#frm-92579271-5567-441F-944C-8C9E232506AF #frm-table #rows-body .row', function(){
	var _act = $(this);
	var _ide = _act.attr('id');
	var _id_nav = $('#frm_wrapper .frm').attr('data-nav');
	var _req = $.ajax({
		url: '/app/administrar/frm.usuarios.php',
		type: 'POST',
		dataType: 'json',
		data: {id_nav_proc: _id_nav , id: _ide , action: 'edit'}
	});
	_req.done(function(data){
		if(data.status ==='ok'){
			$('#frm-edit-dialog').html(data.html);
			$('#frm-edit-dialog').dialog({
				resizable: true,
		      height: 470,
		      width: 530,
		      modal: true,
					show: { effect: 'drop', direction: 'up' },
					hide: { effect: 'drop', direction: 'up' },
					buttons: {
					 "Cancelar": function() {
						 $( this ).dialog( "close" );
					 },
					 "Guardar": function() {
					 }
				 }
			});
		}else{
			if (data.status == 'login'){
				window.location = data.url;
				//console.log(data);
			}else{
				if(data.responseText != undefined && data.responseText != '' ){
					dest.html(data.responseText);
				}else{
					console.log(data);
				}//end if
			}// end if
		}//end if
	});

	_req.fail(function(data){
		console.log(data);
	});
});

//---------------------------------------------------------
// Trigger insert permisos sobre navigator
//---------------------------------------------------------
$(document).on('click', '.row > #btn_set', function(){
  var _act = $(this);
  switch (_act.attr('data-action')) {
    case 'ausentismos':
      console.log(1);
      _click_btn_set_ausentismos(_act);
      break;
    case 'navigator':
      _click_btn_set_navigator(_act);
    default:
      return;
  }//end switch
});
$(document).on('click', ' .result-options-item > #btn-set', function(){
  var _act = $(this);
  //console.log(_act.attr('data-action'));
  switch (_act.attr('data-action')) {
    case 'ausentismos':
      //console.log(1);
      _click_btn_set_ausentismos(_act);
      break;
    case 'navigator':
      _click_btn_set_navigator(_act);
    default:
      return;
  }//end switch
});

  //-------------------------------------------------------------
  //trigger updates sobre permisos tabla navigator
  //-------------------------------------------------------------
  $(document).on('click', '.box.fa', function(){
  	var _box =$(this);
  	var _id_nav = $('.frm').attr('data-nav');
  	var _do_ope = $('.frm').attr('data-ope');
  	var _nav = _box.attr('data-nav');
  	var _ope = _box.attr('data-ope');
  	var _upd = (_box.attr('data-val')==1?0:1);
  	var _for = _box.attr('data-for');
  	var _req = $.ajax({
  		url: '/app/administrar/frm.usuarios.php',
  		type: 'POST',
  		dataType: 'json',
  		data: {id_nav_proc:_id_nav , nav:_nav, ope: _ope, for:_for, do_ope: _do_ope , upd:_upd}
  	});
  	_req.done(function(data){
  		if (data.status =='ok'){
  			if(_upd == 0){
  				_box.attr('data-val', 0);
  				_box.removeClass('fa-check-square-o').addClass('fa-square-o');
  			}else{
  				_box.attr('data-val', 1);
  				_box.removeClass('fa-square-o').addClass('fa-check-square-o');
  			}//end if
  		}else{
  			if (data.status == 'login'){
  				window.location = data.url;
  				//console.log(data);
  			}else{
  				if(data.responseText != undefined && data.responseText != '' ){
  					dest.html(data.responseText);
  				}else{
  					console.log(data);
  				}//end if
  			}// end if
  		}//end if
  	});
  });

  //----------------------------------------------------------
  //Efecto mouseover buscadores
  //----------------------------------------------------------
	$(document).on('mouseover',' .search_wrapper > .results > .rows > .row',function(){
		$(this).children('.for-click').children('i.fa').removeClass('fa-circle-thin').addClass('fa-circle');
	});
	$(document).on('mouseleave',' .search_wrapper > .results > .rows > .row',function(){
		$(this).children('.for-click').children('i.fa').removeClass('fa-circle').addClass('fa-circle-thin');
	});
  //----------------------------------------------------------
  // Click btn.search
  //----------------------------------------------------------
  $(document).on('click','.btn.search', function(){
    var _btn = $(this);
    var _action = _btn.attr('data-action');
    var _source = _btn.attr('data-source');
    //console.log(_source);
    var _conten = $(_source);
    var _dialog;
    var _option = {
        autoOpen:false,
        resizable: true,
        height: 410,
        width: 600,
        modal: true,
        show: { effect: 'drop', direction: 'up' },
        hide: { effect: 'drop', direction: 'up' },
        buttons: {
          'Cancelar': function() {
            _dialog.dialog( 'close');
          }
        },
        close: function(){
          _dialog = 0;
        }
    };
    _dialog = $(_source).dialog(_option);
    _dialog.dialog('open');
  });
  //-------------------------------------------------------------------------
  //Trigger search by first
  //-------------------------------------------------------------------------
  $(document).on('keyup', '.search_wrapper .params input.first', function(){
    //e.preventDefault();
    var _inputa = $(this);
    var _valuea = _inputa.val();
    var _source = _inputa.attr('data-source');
    var _parent = _inputa.attr('data-parent');
    var _callba = _inputa.attr('data-callback');
    var _action  = _inputa.attr('data-action');
    $(_source + " .search_wrapper .params .second").val('');
    if (_valuea.length > 0){
      var _destino = _source + " .search_wrapper .results .rows";
      _search_by_first(_valuea, _source, _destino , _parent, _callba, _action);
      //_search_by_first = undefined;
    }//end if
  });
  $(document).on('keyup', '.search_wrapper .params input.second', function(){
    //e.preventDefault();
    var _inputa = $(this);
    var _valuea = _inputa.val();
    var _source = _inputa.attr('data-source');
    var _parent = _inputa.attr('data-parent');
    var _callba = _inputa.attr('data-callback');
    var _action  = _inputa.attr('data-action');
    $(_source + " .search_wrapper .params .first").val('');
    if (_valuea.length > 1){
      var _destino = _source + " .search_wrapper .results .rows";
      _search_by_second(_valuea, _source, _destino , _parent, _callba, _action);
      //_search_by_first = undefined;
    }//end if
  });

  //Trigger click row en buscadores
  $(document).on('click','.search_wrapper > .results > .rows > .row', function(){
    //console.log(1);
    var _selector = $(this);
    var _source = _selector.attr('data-source');
    var _action = _selector.attr('data-action');
    var _destino = _selector.attr('data-destino');
    var _callback = _selector.attr('data-callback');
    var _diag = _selector.attr('data-diag');
    _send_values(_selector, _source, _destino , _diag,  function(){
      if (_callback == 1) _create_tabla(_selector.attr('id'), _action, _destino + ' #callback-results' );
    });
  });
  $(document).on('click','.options > .row', function(){
    //console.log(2);
    var _selector = $(this);
    var _source = _selector.attr('data-source');
    var _action = _selector.attr('data-action');
    var _destino = _selector.attr('data-destino');
    var _callback = _selector.attr('data-callback');
    _send_values(_selector, _source, _destino , 0,  function(){
      if (_callback == 1) _create_tabla(_selector.attr('id'),_action, _destino + ' #callback-results' );
    });
  });

  $(document).on('keyup', '#generic_source input', function(){
    var _inputa = $(this);
    var _valuea = _inputa.val();
    var _source = _inputa.attr('data-source');
    var _parent = _inputa.attr('data-parent');
    var _callba = _inputa.attr('data-callback');
    var _action  = _inputa.attr('data-action');
    $(_source + " .search_wrapper .params .second").val('');

    if (_valuea.length > 0){
      var _destino = _source + " .options";
      _search_by_first(_valuea, _source, _destino , _parent, _callba, _action, undefined ,function(){
        //console.log($(_destino).children().length);
        if ($(_destino).children().length >0){
          $(_destino).fadeIn(100);
        }else{
          $(_destino).fadeOut(100);
        }// end if
      });
      //var _top = _inputa.offset().top;
      //var _left = _inputa.offset().left;
      //var _attrs = {top:_top, left:_left, position:'absolute'};
      //$(_destino).css(_attrs);
      //_search_by_first = undefined;
    }else{
      $(_destino).fadeOut(100);
    }//end if
  });

  $(document).on('focusout', '#generic_source input', function(){
    var _inputa = $(this);
    var _source = _inputa.attr('data-source');
    var _destino = _source + " .options";
    $(_destino).fadeOut(100);
  });
  $(document).on('click', '#tabla-departamentos .result-options-add .inp .inp-box-options .inp-box-options-more.enabled ', function(e){
    var _inp_val = $('#tabla-departamentos .result-options-add .inp .inp-box').val();
    if (_inp_val.length < 3) return;
    var _ope = $('#frm-dept #operator_id').attr('data-value');
    _buscar_departamentos(_inp_val , 0,_ope, function(data){
      //console.log(data);
      if(data.status =='ok'){
        $('#tabla-departamentos .inp-box-options').html('');
        $('#tabla-departamentos .inp-box-options').html(data.html);
        if($('#tabla-departamentos .inp-box-options-item').length > 10){
          $('#tabla-departamentos .inp-box-options').css('overflow-y','auto');
        }else{
          $('#tabla-departamentos .inp-box-options').css('overflow-y','hidden');
        }//end if
        $('#tabla-departamentos .inp-box-options').slideDown(300);
      }else{
        if (data.status == 'login'){
          window.location = data.url;
          //console.log(data);
        }else{
            //console.log(data);
        }// end if
      }//end if
    });
  });
  $(document).on('keyup', '#tabla-departamentos .result-options-add .inp .inp-box', function(e){
    var _inp_val = $(this).val();
    //console.log(_inp_val);
    if (_inp_val.length < 3) {
      $('#tabla-departamentos .inp-box-options').slideUp(100, function(){
        $('#tabla-departamentos .inp .inp-box').removeAttr('style');
        $('#tabla-departamentos .inp i.fa-times').removeClass('fa-times').addClass('fa-search');

      });
      return;
    }
    switch (e.keyCode) {
      case 13:
        var _item = $('.inp-box-options-item').first();
        _do_set_departamento(_item);
        break;
      default:
        //console.log('do search');
        var _ope = $('#frm-dept #operator_id').attr('data-value');
        _buscar_departamentos(_inp_val , 1, _ope, function(data){
          //console.log(data);
          if(data.status =='ok'){
            $('#tabla-departamentos .inp .inp-box').css('background-color', '#46acea');
            $('#tabla-departamentos .inp .inp-box').css('border-color', '#46acea');

            $('#tabla-departamentos .inp i.fa-times').removeClass('fa-times').addClass('fa-search');
            $('#tabla-departamentos .inp-box-options').html('');
            $('#tabla-departamentos .inp-box-options').css('overflow-y','hidden');
            $('#tabla-departamentos .inp-box-options').html(data.html);
            $('#tabla-departamentos .inp-box-options').slideDown(300);

          }else{
            if (data.status == 'login'){
              window.location = data.url;
              //console.log(data);
            }else{
              $('#tabla-departamentos .inp-box-options').slideUp(100, function(){
                $('#tabla-departamentos .inp-box-options').html('');
                $('#tabla-departamentos .inp-box-options').css('overflow-y','hidden');
                $('#tabla-departamentos .inp .inp-box').css('background-color', '#ea465a');
                $('#tabla-departamentos .inp .inp-box').css('border-color', '#ea465a');
                $('#tabla-departamentos .inp i.fa-search').removeClass('fa-search').addClass('fa-times');
              });
                //console.log(data);
            }// end if
          }//end if
        });
        break;
    };//end switch
  });
  $(document).on('click', '#tabla-departamentos .inp-box-options-item', function(){
    var _item = $(this);
    _do_set_departamento(_item);
  });
  $(document).on('click','#act-departamentos-item-favo', function(){
    var _item = $(this);
    var _dad = _item.parent();
    var _dep = _dad.attr('id');
    //console.log(_dep);
    var _ope = $('#frm-dept #operator_id').attr('data-value');
    _set_departamento_favorito(_dep,_ope, function(data){
      if(data.status =='ok'){
        //console.log(data);//tmp
        $('#act-departamentos-item-favo i.fa-star').removeClass('fa-star gold').addClass('fa-star-o');
        _item.children('i').removeClass('fa-star-o').addClass('fa-star gold');
      }else{
        _usuarios_callback_not_ok(data);
      }//end if
    });
  });
  $(document).on('click','#act-departamentos-item-drop', function(){
    //console.log($(this).parent().attr('id'));
    var _item = $(this).parent();
    $('#dialog-confirm').dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        show: { effect: 'drop', direction: 'up' },
        hide: { effect: 'drop', direction: 'up' },
        buttons: {
          "Eliminar": function() {

              var _ope = $('#frm-dept #operator_id').attr('data-value');
              var _dep = _item.attr('id');
              //var _id_nav = $('.frm').attr('data-nav');
            	//var _do_ope = $('.frm').attr('data-ope');
              _click_btn_delete_departamento(_ope, _dep, function(data){
                if(data.status == 'ok') {
                  _item.fadeIn(300, function(){
                    _item.remove();
                    $('#dialog-confirm').dialog( "close" );
                  });
                }else{
                  _usuarios_callback_not_ok(data);
                }//end if
              });
          },"Cancelar": function(){
            $('#dialog-confirm').dialog( "close" );
          }
        }
    });
  });

  $(document).on('click', '#act-departamentos-item-empl', function(){
    var _dep = $(this).parent().attr('id');
    _buscar_employees_bydep(_dep, function(data){
      var _div = $(data.html);
      _div.dialog({
        resizable: false,
        height: 400,
        width: 800,
        modal: true,
        show: { effect: 'drop', direction: 'up' },
        hide: { effect: 'drop', direction: 'up' },
        buttons: {
          "Cerrar": function() {
            _div.dialog( "close" );
            _div = undefined;
          }
        }
      });
    });
  });


});//ultima llave ready
//-----------------------------------------------------------------------------------------------------------------

function _usuarios_callback_not_ok(data){
  if (data.status == 'login'){
    window.location = data.url;
    //console.log(data);
  }else{
    console.log(data);
  }// end if
}//end function

function _do_set_departamento(_item){

  //console.log(_item);
  var _des =_item.children('#depto-name').html();
  var _dep = _item.children('#depto-id').html();
  var _cod = _item.children('#depto-code').html();
  var _emp = _item.children('#depto-empl').html();
  //var _name
  _click_btn_add_departamento(_dep, function(data){
    if (data.status === 'ok'){
      //console.log(data.post);
      var _html = "<div id=\""+_dep+"\" class=\"act-departamentos-item fn\">";
              _html+= "<div id='act-departamentos-item-favo' class='cur-poi act-departamentos-item-cell bl  w30px fn enlinea floL'><i class='fa fa-1x fa-star-o'></i></div>";
              _html+= "<div id=\"act-departamentos-item-code\" class=\"act-departamentos-item-cell bl  w100px fn enlinea floL\">"+_cod+"</div>";
              _html+= "<div id=\"act-departamentos-item-name\" class=\"act-departamentos-item-cell bl w100po fn enlinea floL\">"+_des+"</div>";
              _html+= "<div id=\"act-departamentos-item-empl\" data-empleados=\""+_emp+"\" class=\" cur-poi act-departamentos-item-cell bl w100px fn enlinea floL\">"+_emp+"</div>";
              //_html+= "<div id=\"act-departamentos-item-cali\" data-califica=\"1\" class=\" act-departamentos-item-cell bl w100px fn enlinea floL\"><i class=\"fa fa-1x fa-check-square-o\"></i></div>";
              _html+= "<div id=\"act-departamentos-item-drop\" class=\" cur-poi act-departamentos-item-cell w100px fn enlinea floL\"><i class=\"fa fa-1x fa-trash\"></i>";
           _html+= "</div></div>";
      $('#act-departamentos .act-departamentos-cont-item').append(_html);
      $.when(_item.remove()).then(function(){
        if($('.inp-box-options-item').length <= 0 ){
          $('.inp-box-options').slideUp(300);
        }// endi if
      });
    }//end if
  });
}// end function

function _set_departamento_favorito(_dep,_ope, callback){
  var _do_ope = $('#tabla-departamentos').attr('data-ope');
  var _id_nav = $('#tabla-departamentos').attr('data-nav');

  var req = $.ajax({
    url: '/app/administrar/frm.usuarios.php',
    type: 'POST',
    dataType: 'json',
    data: {id_nav_proc:_id_nav, action: 'departamento_favorito', do_ope: _do_ope, dep: _dep, ope:_ope}
  });
  req.done(function(data){
    if (callback) callback(data);
  });
  req.fail(function(data){
    if (callback) callback(data);
  });
}//end function
function _buscar_employees_bydep(_dep, callback){
  var req = $.ajax({
    url: '/app/frm.search.proc.php',
    type: 'POST',
    dataType: 'json',
    data: {search:1 , action: 'employees_bydep', dep: _dep}
  });
  req.done(function(data){
    if (callback) callback(data);
  });
  req.fail(function(data){
    if (callback) callback(data);
  });
}// end function
function _buscar_departamentos(_txt, _limit,_ope, callback){
  var req = $.ajax({
    url: '/app/frm.search.proc.php',
    type: 'POST',
    dataType: 'json',
    data: {search:1 , limit: _limit, action: 'departamentos', txt: _txt, ope:_ope}
  });
  req.done(function(data){
    if (callback) callback(data);
  });
  req.fail(function(data){
    if (callback) callback(data);
  });
}//end function

function _click_btn_delete_departamento(_ope, _dep, callback){
  var _id_nav = $('#tabla-departamentos').attr('data-nav');
  var _do_ope = $('#tabla-departamentos').attr('data-ope');

  var req = $.ajax({
    url: '/app/administrar/frm.usuarios.php',
    type: 'POST',
    dataType: 'json',
    data: {id_nav_proc:_id_nav , dep:_dep, ope: _ope, do_ope: _do_ope, action:'delete'}
  });
  req.done(function(data){
    //console.log(data);
    if(data.status == 'ok'){
      if (callback) callback(data);
    }else{
      _usuarios_callback_not_ok(data);
    }//end if
  });
  req.fail(function(data){
    console.log(data);
  });
}
function _click_btn_add_departamento(_dep, callback){

  var _id_nav = $('#tabla-departamentos').attr('data-nav');
  var _do_ope = $('#tabla-departamentos').attr('data-ope');
  var _ope = $('#frm-dept #operator_id').attr('data-value');

  var req = $.ajax({
    url: '/app/administrar/frm.usuarios.php',
    type: 'POST',
    dataType: 'json',
    data: {id_nav_proc:_id_nav , dep:_dep, ope: _ope, do_ope: _do_ope, action:'insert'}
  });
  req.done(function(data){
    //console.log(data);
    if(data.status == 'ok'){
      if (callback) callback(data);
    }else{
      _usuarios_callback_not_ok(data);
    }//end if
  });
  req.fail(function(data){
    console.log(data);
  });
}//end if
//----------------------------------------------
// _click_btn_set_ausentismos
//----------------------------------------------
function _click_btn_set_ausentismos(_act){
  if(!_act.children('i.fa').hasClass('fa-check-square-o') && !_act.children('i.fa').hasClass('fa-square-o')) return;
  var _id_nav = $('#tabla-ausentismos').attr('data-nav');
  var _do_ope = $('#tabla-ausentismos').attr('data-ope');
  var _aus = _act.parent().attr('id');
  var _ope = $('#frm-ause #operator_id').attr('data-value');
  var _ins = (_act.parent().attr('data-val')==1?0:1);
  var req = $.ajax({
    url: '/app/administrar/frm.usuarios.php',
    type: 'POST',
    dataType: 'json',
    data: {id_nav_proc:_id_nav , aus:_aus, ope: _ope, do_ope: _do_ope , ins:_ins}
  });
  req.done(function(data){
    //console.log(data);
    if(data.status == 'ok'){
      _act.parent().attr('data-val',_ins);
      if(_ins == 1){
        _act.children('i.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
      }else{
        _act.children('i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
      }//end if
    }else{
      _usuarios_callback_not_ok(data);
    }//end if
  });
}//end if
function _click_btn_set_navigator(_act){
  if(!_act.children('i.fa').hasClass('fa-circle') && !_act.children('i.fa').hasClass('fa-circle-thin')) return;
  var _id_nav = $('.frm').attr('data-nav');
  var _do_ope = $('.frm').attr('data-ope');
  var _nav = _act.parent().attr('id');
  var _ope = $('#frm-menu #operator_id').attr('data-value');
  var _ins = (_act.parent().attr('data-val')==1?0:1);

  var req = $.ajax({
    url: '/app/administrar/frm.usuarios.php',
    type: 'POST',
    dataType: 'json',
    data: {id_nav_proc:_id_nav , nav:_nav, ope: _ope, do_ope: _do_ope , ins:_ins}
  });
  req.done(function(data){
    //console.log(data);
    if(data.status == 'ok'){
      _act.parent().attr('data-val',_ins);
      if(_ins == 1){
        _act.children('i.fa').removeClass('fa-circle-thin').addClass('fa-circle');
        _act.parent().children('#cell_full').html("<i data-val='0' data-for='_full' data-ope='"+_ope+"' data-nav='"+_nav+"' class='box fa fa-1x fa-square-o'></i>");
        _act.parent().children('#cell_read').html("<i data-val='1' data-for='_read' data-ope='"+_ope+"' data-nav='"+_nav+"' class='box fa fa-1x fa-check-square-o'></i>");
        _act.parent().children('#cell_write').html("<i data-val='0' data-for='_write' data-ope='"+_ope+"' data-nav='"+_nav+"' class='box fa fa-1x fa-square-o'></i>");
        _act.parent().children('#cell_special').html("<i data-val='0' data-for='_special' data-ope='"+_ope+"' data-nav='"+_nav+"' class='box fa fa-1x fa-square-o'></i>");
        if(_act.parent().children('#cell_item_name').children('span').attr('data-is')==1){
          var _clv = _act.parent().children('#cell_item_name').children('span').attr('data-clave');
          $('.anexa_rows .row').each(function(){
            var _row = $(this);
            var _fat = _row.children('#cell_item_name').children('span').attr('data-father');
            if(_fat === _clv){
              _row.children('#btn_set').addClass('cur-poi');
              _row.children('#btn_set').children('i.fa').addClass('fa-circle-thin');
            }//end if
          });
        }else{
          return;
        }
      }else{
        _act.children('i.fa').removeClass('fa-circle').addClass('fa-circle-thin');
        _act.parent().children('#cell_full').html('-');
        _act.parent().children('#cell_read').html('-');
        _act.parent().children('#cell_write').html('-');
        _act.parent().children('#cell_special').html('-');
        if(_act.parent().children('#cell_item_name').children('span').attr('data-is')==1){
          var _clv = _act.parent().children('#cell_item_name').children('span').attr('data-clave');
          $('.anexa_rows .row').each(function(){
            var _row = $(this);
            var _fat = _row.children('#cell_item_name').children('span').attr('data-father');
            if(_fat === _clv){
              _row.children('#btn_set').removeClass('cur-poi');
              _row.children('#btn_set').children('i.fa').removeClass('fa-circle').removeClass('fa-circle-thin');//.addClass('fa-circle-thin');
              _row.attr('data-val',0);
              _row.children('#cell_full').html('-');
              _row.children('#cell_read').html('-');
              _row.children('#cell_write').html('-');
              _row.children('#cell_special').html('-');
            }//end if
          });
        }else{
          return;
        }
      }//end if
    }else{
      if (data.status == 'login'){
        window.location = data.url;
        //console.log(data);
      }else{
        if(data.responseText != undefined && data.responseText != '' ){
          dest.html(data.responseText);
        }else{
          console.log(data);
        }//end if
      }// end if
    }//end if
  });
  req.fail(function(data){
    console.log(data);
  });
}//end function
//----------------------------------------------
// Enviar Valores de busqueda a formulario
//----------------------------------------------
function _send_values(_selector, _source, _destino, _diag, callback){
  var _act = _selector.attr('id');
  var _count = $(_source + ' #' + _act + ' .send').length;
  $(_source + ' #' + _act + ' .send').each(function(){
    var _for = $(this).attr('data-for');
    var _val = $(this).html();
    $(_destino+' #' + _for).attr('data-value', _val);
    if($(_destino+' #' + _for).hasClass('lower')) $(_destino+' #' + _for).val(_val);
      else $(_destino+' #' + _for).val(_val.toLowerCase().capitalize());
    if(!--_count){
      if (!_diag == 0 ) $(_diag).dialog('close');
      if (callback) callback();
    }//end if
  });
}//end function
//----------------------------------------------
// Funcion Search dinamica by first
//----------------------------------------------
function _search_by_first(_value, _source, _destino, _for_destino, _need_callback, _action,_diag, callback){
  //console.log(_source);
  //callback = callback || undefined;
  var _s = $(_source);
  var _cols = JSON.parse(_s.attr('data-cols'));
  var _query = _s.attr('data-query-first'); // cambiar
  var _params = { search:1,
                  first: _value,
                  query_first: _query ,
                  cols: _cols
                  ,source: _destino
                  ,destino: _for_destino
                  ,callback: _need_callback
                  ,action: _action
                  ,diag: _source};
  var _req = $.ajax({
    url: '/app/frm.search.proc',
    type: 'POST',
    dataType: 'json',
    data: _params
  });
  _req.done(function(data) {
    //console.log(data);
    if (data.status === 'ok') {
      $(_destino).html('');
      //console.log(data.html);
      $(_destino).html(data.html);
      if (callback) callback();
    }else{
      $(_destino).html('');
      if (data.status == 'login'){
        window.location = data.url;
        //console.log(data);
      }else{
        if(data.responseText != undefined && data.responseText != '' ){
          dest.html(data.responseText);
        }else{
          $(_destino).html('');
          //console.log(data);
        }//end if
      }// end if
    }//end if
  });
  _req.fail(function(data){
    $(_destino).html('');
    console.log(data);
  });
}//end function
//----------------------------------------------
// Funcion Search dinamica by second
//----------------------------------------------
function _search_by_second(_value, _source, _destino, _for_destino, _need_callback, _action,_diag){
  //console.log(_source);
  var _s = $(_source);
  var _cols = JSON.parse(_s.attr('data-cols'));
  var _query = _s.attr('data-query-second'); // cambiar
  var _vals = [];
  $(_source + ' .second').each(function(){
    var _arry = [$(this).attr('data-col'),$(this).val()];
    _vals.push(_arry);
    _arry = undefined;
  });
  var _params = {  search:1
                  ,vals: _vals
                  ,query_second: _query
                  ,cols: _cols
                  ,source: _destino
                  ,destino: _for_destino
                  ,callback: _need_callback
                  ,action: _action
                  ,diag: _source };
  var _req = $.ajax({
    url: '/app/frm.search.proc',
    type: 'POST',
    dataType: 'json',
    data: _params
  });
  _req.done(function(data) {
    //console.log(data);
    if (data.status === 'ok') {
      $(_destino).html('');
      $(_destino).html(data.html);
    }else{
      $(_destino).html('');
      if (data.status == 'login'){
        window.location = data.url;
        //console.log(data);
      }else{
        if(data.responseText != undefined && data.responseText != '' ){
          dest.html(data.responseText);
        }else{
          console.log(data);
        }//end if
      }// end if
    }//end if
  });
  _req.fail(function(data){
    $(_destino).html('');
    console.log(data);
  });
}//end function

//----------------------------------------------
// crear tabla cuando se tiene callback
//----------------------------------------------
function _create_tabla(_ope, _action, _destino) {
  var _id_nav = $('.frm').attr('data-nav');
  //var _ope = $('#frm-ause #operator_id').attr('data-value');
  //console.log(_ope);
  if (_ope == '' || _ope == undefined) return;
  var req = $.ajax({
    url: '/app/administrar/frm.usuarios.php',
    type: 'POST',
    dataType: 'json',
    data: {id_nav_proc: _id_nav , ope: _ope , action: _action}
  });
  req.done(function(data){
    //console.log(data);
    if(data.status == 'ok'){
      $(_destino).html(data.html);
      _do_actions(_action);
    }else{
      if (data.status == 'login'){
        window.location = data.url;
        //console.log(data);
      }else{
        if(data.responseText != undefined && data.responseText != '' ){
          dest.html(data.responseText);
        }else{
          console.log(data);
        }//end if
      }// end if
    }//end if
  });
  req.fail(function(data){
    console.log(data);
  });
}//end function
//------------------------------------------------------
// Trigger funciones adicionales a partir del action
//------------------------------------------------------
function _do_actions(_action){
  switch (_action) {
    case 'navigator':
      _action_navigator();
      break;
    case 'ausentismos':
      //_action_ausentismos();
      break
    default:
      return;
  }//end if
}//
//------------------------------------------------------
//Funcion adicional al cargar tabla navigator
//------------------------------------------------------
function _action_navigator(){
  $(".anexa_rows .row[data-val='0'] #cell_item_name span[data-is='1']").each(function(){
    var _clv = $(this).attr('data-clave');
    $(".anexa_rows .row[data-val='0'] #cell_item_name span[data-is='0'][data-father='" + _clv + "']").each(function(){
      var _row =$(this).parent().parent();
      _row.children('#btn_set').removeClass('cur-poi');
      _row.children('#btn_set').children('i.fa').removeClass('fa-circle').removeClass('fa-circle-thin');
    });
  });
}//end function
