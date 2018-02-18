$(document).ready(function() {
	var _shift = false;

	$(document).on('keyup','#rows-body.modo-horas-extras .empl.row .cell.dia',function(e){
		//console.log(e.which);
		var tecla = e.which
		//if (e.which > 57 || e.which < 47 || e.which === 8) return false;

		switch (tecla) {
			case 48:case 49:case 50:case 51:case 52:case 53:case 54:case 55:case 56:case 57:
			case 96:case 97:case 98:case 99:case 100:case 101:case 102:case 103:case 104:case 105:
			case 46:case 8:
				var _cell = $(this);
				var _dad = _cell.parent();
				var _ope = $('.frm-lista').attr('data-op');
				var _cn = _cell.attr('data-cn');
				var _per = _dad.attr('data-periodo');
				var _emp = _dad.attr('data-employee');
				var _val = 0;
				if (tecla === 46 || tecla === 8) _val = 0; else	_val = parseInt(e.key);

				//console.log(e);
				var _valor_anterior = _cell.children('span.border-interno').html();
				var _pulse = $("<i class='fa fa-1x fa-spinner fa-pulse'></i>");
				_cell.children('span.border-interno').hide();
				_cell.append(_pulse);

				var _params ={
					action: 'insert::lista::horas-extras'
					,emp: _emp
					,per: _per
					,ope: _ope
					,cn: _cn
					,val: _val
				};

				_jlista_post_proc(_params, function(data){
					//console.log(data);
					if(data.status === "ok" && data.res === 1){

						_cell.find('span.border-interno').html(((data.post.val === "0")?"-":data.post.val));

						_cell.parent().find('.cell.dia.work span.border-interno').html(data.pagadas);
					}//end if
					if(data.status === "ok" && data.res === 0){
						//alert(data.msg);
						_show_dialog_error(data.msg);
					}//
					if(data.status === "error"){
						_lista_callback_not_ok(data);
					}//end if
					_cell.children('i.fa').remove();
					_cell.find('span.border-interno').show();
				});

				break;
			default:

				return false;
		}//end switch

	});

	$(document).on('click', '.data-item .head .tab.inactive', function(){
		var _tab = $(this);
		_tab.parent().children('.tab.active').removeClass('active').addClass('inactive');
		$('.data-item .tab-cont').fadeOut(300).removeClass('visible').addClass('oculto');
		$(_tab.attr('data-tab')).fadeIn(300).removeClass('oculto').addClass('visible');
		_tab.removeClass('inactive').addClass('active');
	});
	$(document).on('mouseover','.datos .g.change', function(){
		var _item = $(this);
		_item.children('.label').hide();
		var _val = _item.children('.value');
		_val.addClass('active').html(_val.attr('data-name'));
	});
	$(document).on('mouseleave','.datos .g.change', function(){
		var _item = $(this);
		var _val = _item.children('.value');
		_val.removeClass('active').html(_val.attr('data-code'));
		_item.children('.label').show();
	});

	$(document).on('click', '#frm-set-periodo .set-options .periodo-option', function(){
		//console.log(1);
		$('#rows-body').hide();
		$('#dia-cont').hide();
		$('#rows-body-cargando').show();
		$('#rows-body-cargando').removeClass('oculto').addClass('visible');

		var _sel = $(this);
		$('#frm-set-periodo .set-options .periodo-option.selected').removeClass('selected');
		_sel.addClass('selected');
		var _btn = $(_sel.parent().data('btn'));
		var _val = _sel.data('value');
		var _per = _sel.attr('id');
		var _act = _btn.data('actual');
		if(_sel.data('actual')!== 1){
			_btn.addClass('no-actual');
		}else{
			_btn.removeClass('no-actual');
		}//end if
		_btn.html(_val);
		_btn.attr('data-periodo', _per);
		_btn.removeClass('active').addClass('unactive');

		if ($('.frm-lista #rows-body').hasClass('modo-checadas')){
			_get_checadas(1);
			return false;
		}//end if

		if ($('.frm-lista #rows-body').hasClass('modo-horas-extras')){
			_get_horas_extras($('#btn-horas-extras'),1);
			return false;
		}//end if
		/*var _pag = $('#frm-pagination-controls');
		var _ope = _pag.attr('data-ope');
		var _dep = $('#set-departamento').attr('data-departamento');
		var _per = $('#set-periodo').attr('data-periodo');
		var _page = 1;
		var _dias = $('#rows-head').attr('data-dias-periodo');
		var _pages = parseInt(_pag.attr('data-pages'));
		_get_lista_page(_ope, _page, _dep, _per,_dias, function(data){
			if (data.status =='ok' && data.html != undefined){
				$('#rows-body-error.visible').fadeOut(150);
				$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
				$('#rows-body-cargando.visible').fadeOut(150, function (){
					$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
					$('#dia-cont').show();
					$('#rows-body').html('');
					$('#rows-body').html(data.html).fadeIn(300);
					$('#rows-head').html(data.columnas).fadeIn(300);
					$('#rows-head').attr('data-dias-periodo',data.dias);
				});
				_pag.attr('data-actual', _page);
				$('#frm-pagination-lab').html(_page  +"/" + _pages );
			}else{
				_lista_callback_not_ok(data);
			}//end if
		});
		_dad = null;
		_sel = null;
		*/
	});

	$(document).on('click', '#frm-filter.tab.panel',function(){
		//console.log($('#rows-body.paginar .empl.row').length);
		var _tab = $(this);
		if(! _tab.hasClass('active')){
			$('#filter').addClass('visible').removeClass('oculto');
			_tab.addClass('active');
			$('#filter-input').focus();
			_tab.trigger('mouseleave');
		}else{
			$('#filter').addClass('oculto').removeClass('visible');
			_tab.removeClass('active');
		}//end if
	});

	$(document).on('click', '.frm-lista #rows-head .colm', function(){
		//console.log(1);

		var _colm = $(this);
		var _orden_tip = _colm.attr('data-orden-tip');
		var _orden_dir = _colm.attr('data-orden-dir');
		var _orden_sou = _colm.attr('data-orden-sou');
		var _divs = $('.frm-lista #rows-body .empl.row');
		var _dest = '.frm-lista #rows-body';
		//console.log(_orden_dir);
		_ordenar_divs(_divs, _orden_tip, _orden_dir, _orden_sou, _dest, function(){
			$('.frm-lista #rows-head .colm i.fa.removable').remove();
			var _i_asc = $("<i class='fa removable fa-1x fa-angle-down'></i>");
			var _i_desc = $("<i class='fa removable fa-1x fa-angle-up'></i>");
			if(_orden_dir === 'asc'){
				_colm.append(_i_asc);
				_colm.attr('data-orden-dir', 'desc');
			}else{
				_colm.append(_i_desc);
				_colm.attr('data-orden-dir', 'asc');
			}//end if
		});

	});



	$(document).on('click', '#rows-body .empl.row', function(){
		var _row = $(this);
		var _id = _row.attr('data-employee');
		_load_img_col(_id, function(){
			var _pos = _row.children('div.last').html() || _row.children('div.last16').html()|| _row.children('div.last13').html();
			var _col = _row.children('div.id').html() + ' - ' + _row.children('div.name').html()  + ' - ' + _pos;
			$('#dia-wrapper-head #dia-wrapper-title').html(_col);
			_set_focus(_row);
		});
	});

	//-----------------------------------------------------------------
	$(document).on('click','#btn-dia, #bar-photo-item', function(){
		$('#btn-dia').parent().fadeOut(100,function(){
			$('#rows-body').css('max-height', 'calc(100% - 34px - 182px)');
			$('#dia-wrapper').fadeIn(300);

		});
	});
	$(document).on('click', '#dia-wrapper #dia-wrapper-close', function(){
		$('#dia-wrapper').fadeOut(300,function(){
			$('#rows-body').css('max-height', 'calc(100% - 34px)');
			$('#btn-dia').parent().fadeIn(100);
		});
	});

	/*$(window).resize(function(event) {
		//var _i = parseInt($(".empl div.cell.dia[focused='true']").attr('tabindex'));
		//var _r = parseInt($(".empl div.cell.dia[focused='true']").parent().attr('id'));

		if ($('#frm-lista #frm_employees').length == 1 && $(".empl div.cell.dia.focused").length == 1){
			var _side = false;
			//console.log($('#sidebar').width());
			if($('#sidebar').width() <= 249){
				_side = true;
			}//end if;
			_dia_pos($(".empl div.cell.dia.focused",null, _side));
		}//end if

	});*/

	//console.log(1);
	$(document).on('mouseup',function(e){
		var _cont = $('#frm_employees');
		if (!_cont.is(e.target) && _cont.has(e.target).length === 0){ // ... nor a descendant of the container
       		hide_dia_wrapper();
    	}//end if

	});
	$(document).on('mousewheel', '.frm-lista #rows-body',function(){
		$('.frm-lista #rows-body .cell-tooltip').remove();
	});
	$(document).on('scroll', '.frm-lista #rows-body',function(){
		$('.frm-lista #rows-body .cell-tooltip').remove();
	});



	$(document).on('focusout', '.frm-lista #rows-body .empl .cell.dia',function(){
			//console.log($('.frm-lista #rows-body .cell-tooltip'));
			if($('.frm-lista #rows-body .empl .cell.dia:focus').length < 1)
				$('.frm-lista #rows-body .cell-tooltip').remove();
	});
	$(document).on('focus', '.frm-employees #rows-body .empl .cell.dia',function(e){
		e.preventDefault();
		//console.log(1);
		var _cell = $(this);
		_show_tooltip(_cell);

		var _cn = _cell.attr('data-cn');
		if (_cn !== undefined){
			var _per = $('#set-periodo').attr('data-periodo');
			//console.log(_per);
			var _emp = _cell.attr('data-employee');
			var _params = { action: 'get::cn'
										, emp: _emp
										, per: _per
										, cn: _cn};
			_jlista_post_proc(_params, function(data){
				if (data.status === 'ok') {
					var _dia = $('#dia-info > .dia-bar-data');
					_dia.children('#data-fecha').children('span.val').html(data.fec);
					_dia.children('#data-entrada').children('span.val').html(data.ent);
					_dia.children('#data-salida').children('span.val').html(data.sal);
					_dia.children('#data-jornada').children('span.val').html(data.jor);
					_dia.children('#data-ausentismo').children('span.val').html(data.aus);
					_dia.children('#data-ausentismo').attr('title',data.aus);
					var _row = _cell.parent();
					var _id = _row.attr('data-employee');
					_load_img_col(_id, function(){
						var _pos = _row.children('div.last').html() || _row.children('div.last13').html()|| _row.children('div.last16').html();
						var _col = _row.children('div.id').html() + ' - ' + _row.children('div.name').html()  + ' - ' + _pos;
						$('#dia-wrapper-head #dia-wrapper-title').html(_col);
						_set_focus(_row);
					});
					//console.log(data);
				}else{
					_lista_callback_not_ok(data);
				}// end if
			});
		}else return;
		/*$('#frm-lista #frm_employees #rows-body .empl .cell.dia.focused').removeClass('focused');
		act.addClass('focused');
		var row = parseInt(act.parent().attr('id'));
		var ind = parseInt(act.attr('tabindex'));
		if($('#btn_show_dia').attr('data-status') == 1) show_dia_wrapper(row,ind);
		act = null;
		row = null;
		ind = null;*/
	});

	$(document).on('keyup', '.frm-employees > #frm-table > #rows-body.modo-checadas > .empl > .cell.dia',function(e){

		e.preventDefault();
    e.stopPropagation();
		var _which = e.which;
		e = null;
		//console.log(1);
		var act = $(this);
		var row = parseInt(act.parent().attr('data-id'));
		var ind = parseInt(act.attr('tabindex'));

		if($('.frm-employees > #frm-table > #rows-body.modo-checadas > .empl > .cell.dia.procesando').length >0){
			console.log('procesando');
			return false;
		}//end if
		//console.log(_which);
		switch(_which){
			case 13:case 27:
				return false;
				break;
			case 37: //left
				move_cell_dia(row, ind-1);
				break;
			case 38: //up
				move_cell_dia(row-1, ind);
				break;
			case 39: //right
				move_cell_dia(row, ind+1);
				break;
			case 40: //down
				move_cell_dia(row+1, ind);
				break;
			//case 32:
			case 46:
			case 8:
				var _cell = act;
				var _valor_anterior = _cell.children('span').html();
				var _pulse = $("<i class='fa fa-1x fa-spinner fa-pulse'></i>");
				_cell.children('span').hide();
				_cell.append(_pulse);

				var _cn = _cell.attr('data-cn');
				if (_cn !== undefined){
					_cell.addClass('procesando');

					_validar_atras(_cell, function(data){
						//console.log(data);
						if(data.result === 1){
							var _letra = String.fromCharCode(_which);
							var _per = $('#set-periodo').attr('data-periodo');
							//console.log(_per);
							var _emp = _cell.attr('data-employee');
							var _params = { action: 'delete::letra'
														, letra: _letra
														, emp: _emp
														, per: _per
														, cn: _cn};
							_jlista_post_proc(_params, function(data){
								if (data.status === 'ok') {
									if(data.result === 1){
										_cell.children('i').remove();
										_cell.children('span').html(data.letra);
										_cell.children('span').show();
										var _color = _cell.attr('data-color');
										_cell.attr('data-color', data.color);
										_cell.removeClass(_color).addClass(data.color);
										var _row = $('#rows-body .empl.row.selected');
										_set_focus(_row ,1);
										_cell.children('i').remove();
										_cell.removeClass('procesando');
									}else{
										_cell.children('i').remove();
										_cell.removeClass('procesando');
										_cell.children('span').html(_valor_anterior);
										_cell.children('span').show();
										console.log(data.msg);
									}//end if
								}else{
									_cell.children('i').remove();
									_cell.removeClass('procesando');
									_cell.children('span').html(_valor_anterior);
									_cell.children('span').show();
									_lista_callback_not_ok(data);
								}//end if

							});
						}else{
							_show_dialog_error('Límite de días hacia atras alcanzado ...');
							_cell.children('i').remove();
							_cell.removeClass('procesando');
							_cell.children('span').html(_valor_anterior);
							_cell.children('span').show();
							return false;
						}//end if
					});

				}else return;
				break;

			default:
				var _cell = act;
				//----------------------------------
				//transition _letra
				//----------------------------------
				var _valor_anterior = _cell.children('span').html();
				var _pulse = $("<i class='fa fa-1x fa-spinner fa-pulse'></i>");
				_cell.children('span').hide();
				_cell.append(_pulse);
				//----------------------------------

				_validar_letra(_cell, _which, function(data){
					if(data.result === 0 ){
						_show_dialog_error(data.msg);
						_cell.children('i').remove();
						_cell.removeClass('procesando');
						_cell.children('span').html(_valor_anterior);
						_cell.children('span').show();
						return false;
					}//end if

					if(data.result === 1){
						var _cn = _cell.attr('data-cn');
						if (_cn !== undefined){
							_cell.addClass('procesando');
							var _letra = String.fromCharCode(_which);
							var _per = $('#set-periodo').attr('data-periodo');
							var _params = { action: 'causa::letra'
														, per : _per
														, letra: _letra};
							_jlista_post_proc(_params, function(data){
								if (data.status ==='ok'){
									if(data.result === 1){
										//console.log(data);
										var _dialog;
								    var _option = {
								        autoOpen: false,
								        resizable: false,
								        height: 'auto',
												maxHeight: 175,
								        width: 300,
								        modal: true,
								        show: { effect: 'drop', direction: 'up' },
								        hide: { effect: 'drop', direction: 'up' },
								        buttons: {
								          'Cancelar': function() {
								            _dialog.dialog( 'close');
														_cell.children('i').remove();
														_cell.removeClass('procesando');
														_cell.children('span').html(_valor_anterior);
														_cell.children('span').show();
								          },
													'Aceptar': function() {
														var _valid = true;
														var _need_com = $('#sel-causa').children('option:selected').attr('data-comentarios');
														var _need_fec = $('#sel-causa').children('option:selected').attr('data-requiere-fecha');
														var _cau = $('select#sel-causa').val();
														var _com = $('#causa-comentarios').val();
														var _fec = $('#causa-fecha').val();
														//--------------------------------------
														// Descomentar para validar test
														//--------------------------------------
														//console.log(_fec); return false;
														//--------------------------------------
														if(_cau === undefined || _cau == 0 || _cau == '' ){
															_valid = false;
														}//end if
														if (_need_com == 1 && _com.length == 0 ){
															_valid = false;
														}//end if
														if (_need_fec == 1 && _fec.length == 0 ){
															_valid = false;
														}//end if


														if(_valid === true){
															var _per = $('#set-periodo').attr('data-periodo');
															var _emp = _cell.attr('data-employee');

															if(_need_com == 1 && _need_fec == 0){
																var _params = { action: 'insert::letra'
																							, letra: _letra
																							, emp: _emp
																							, per: _per
																							, cn:  _cn
																							, cau: _cau
																							, coment: _com};
															}//end if
															if(_need_com == 0 && _need_fec == 1){
																var _params = { action: 'insert::letra'
																							, letra: _letra
																							, emp: _emp
																							, per: _per
																							, cn:  _cn
																							, cau: _cau
																							, fec: _fec};
															}//end if
															if(_need_com == 1 && _need_fec == 1){
																var _params = { action: 'insert::letra'
																							, letra: _letra
																							, emp: _emp
																							, per: _per
																							, cn:  _cn
																							, cau: _cau
																							, coment: _com
																							, fec: _fec};
															}//end if
															if(_need_com == 0 && _need_fec == 0){
																var _params = { action: 'insert::letra'
																							, letra: _letra
																							, emp: _emp
																							, per: _per
																							, cn:  _cn
																							, cau: _cau };
															}//end if
															//console.log(_params);
															_jlista_post_proc(_params, function(data){
																if (data.status === 'ok') {
																	if(data.result === 1){
																		_cell.children('span').html(data.letra);
																		_cell.children('i').remove();
																		_cell.children('span').show();
																		var _color = _cell.attr('data-color');
																		_cell.attr('data-color', data.color);
																		_cell.removeClass(_color).addClass(data.color);
																		var _row = $('#rows-body .empl.row.selected');
																		_set_focus(_row,1);
																		_cell.removeClass('procesando');
																	}else{
																		_cell.children('i').remove();
																		_cell.removeClass('procesando');
																		_cell.children('span').html(_valor_anterior);
																		_cell.children('span').show();
																		console.log(data.msg);
																	}//end if
																}else{
																	_cell.children('i').remove();
																	_cell.removeClass('procesando');
																	_cell.children('span').html(_valor_anterior);
																	_cell.children('span').show();
																	_lista_callback_not_ok(data);
																}//end if
															});
								            	_dialog.dialog( 'close');
														}else{
															if (_cau === '') $('#sel-causa').css('border-color','crimson');
															if (_com === '') $('#causa-comentarios').css('border-color','crimson');
															if (_fec === '') $('#causa-fecha').css('border-color','crimson');
														}//end if
								          }//end Aceptar
								        },
								        close: function(){
													_cell.children('i').remove();
													_cell.removeClass('procesando');
													//_cell.children('span').html(_valor_anterior);
													_cell.children('span').show();
													_dialog.dialog('destroy').remove();
								          _dialog = 0;
													_dialog = null;
												}//end close
								    };
								    _dialog = $(data.html).dialog(_option);
										_dialog.dialog('open');
									}else{
										var _per = $('#set-periodo').attr('data-periodo');
										var _emp = _cell.attr('data-employee');
										var _params = { action: 'insert::letra'
																	, letra: _letra
																	, emp: _emp
																	, per: _per
																	, cn: _cn};
										_jlista_post_proc(_params, function(data){
											if (data.status === 'ok') {
												if(data.result === 1){
													_cell.children('span').html(data.letra);
													_cell.children('i').remove();
													_cell.children('span').show();
													var _color = _cell.attr('data-color');
													_cell.attr('data-color', data.color);
													_cell.removeClass(_color).addClass(data.color);
													var _row = $('#rows-body .empl.row.selected');
													_set_focus(_row,1);
													_cell.removeClass('procesando');
												}else{
													_cell.children('i').remove();
													_cell.removeClass('procesando');
													_cell.children('span').html(_valor_anterior);
													_cell.children('span').show();
													console.log(data.msg);
												}//end if
											}else{
												_cell.children('i').remove();
												_cell.removeClass('procesando');
												_cell.children('span').html(_valor_anterior);
												_cell.children('span').show();
												_lista_callback_not_ok(data);
											}//end if
										});
									}//end if
								}else{
									_cell.children('i').remove();
									_cell.removeClass('procesando');
									_cell.children('span').html(_valor_anterior);
									_cell.children('span').show();
									_lista_callback_not_ok(data);
								}//end if
							});
						}else	return;
					}//end if
			});
			break;
		}//end switch

		act = null;
		row = null;
		ind = null;
	});

	$(document).on('change','select#sel-causa', function(){
		var _sel = $(this);
		var _opt = _sel.children('option:selected');
		//console.log(_opt);
		//console.log(.attr('data-requiere-fecha'));
		//console.log($(this).children('option:selected').data('comentarios'));
		if(_opt.attr('data-comentarios') == 1){
			//console.log($('#letra-causas-comentario'));
			$('#letra-causas-comentario').slideDown(300).removeClass('oculto');
		}else{
			$('#letra-causas-comentario').slideUp(300).addClass('oculto');
		}//end if
		if(_opt.attr('data-requiere-fecha') == 1){

			$('#letra-causas-fecha').slideDown(300).removeClass('oculto');
		}else{
			$('#letra-causas-fecha').slideUp(300).addClass('oculto');
		}//end if
	});

	/*$(document).on('click', '#frm-lista #frm_employees #rows-body .empl .cell.dia',function(e){
		console.log('aqui');
		e.preventDefault();
		//console.log(1);
		var act = $(this);
		var row = parseInt(act.parent().attr('id'));
		var ind = parseInt(act.attr('tabindex'));
		//console.log(ind);
		if($('#btn_show_dia').attr('data-status') == 1) show_dia_wrapper(row, ind);

		act = null;
		row = null;
		ind = null;
	});*/

	$(document).on('click','#frm-lista #frm-tools div.tool',function(e){
		e.preventDefault();
		var act = $(this);
		switch (act.attr('id')){
			case 'btn_show_dia':
				btn_show_dia_click(act.attr('data-status'), act);
				break;
			case 'btn-tools':
				var val = act.attr('data-val');
				btn_tools_click(act, val);
				break;

			default:
				return;
				break;
		}//end switch
	});
	$(document).on('mouseover','#frm-pagination.pages', function(){
		$('#frm-pagination-controls.oculto').removeClass('oculto');
	});
	$(document).on('mouseleave','#frm-pagination-controls', function(){
		$('#frm-pagination-controls').addClass('oculto');
	});

	$(document).on('click','#frm-tabs #btn-horas-extra.active', function(){
		var _btn = $(this);

		_btn.removeClass('active').addClass('unactive');
		$('#rows-body').hide();
		$('#dia-cont').hide();
		_btn.find('i.fa').removeClass('fa-reply').addClass('fa-clock');
		$('#rows-body-cargando').show();
		$('#rows-body-cargando').removeClass('oculto').addClass('visible');


		var _dad = $('#frm-pagination-controls');
		var _ope = _dad.attr('data-ope');
		var _dep = $('#set-departamento').attr('data-departamento');
		var _per = $('#set-periodo').attr('data-periodo');
		var _page = 1;
		var _dias = $('#rows-head').attr('data-dias-periodo');
		var _pages = parseInt(_dad.attr('data-pages'));

		_get_lista_page(_ope, _page, _dep, _per,_dias, function(data){
			if (data.status =='ok' && data.html != undefined){
				$('#rows-body-error.visible').fadeOut(150);
				$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
				$('#rows-body-cargando.visible').fadeOut(150, function (){
					$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
					$('#dia-cont').show();
					$('#rows-body').html('');
					$('#rows-body').html(data.html).fadeIn(300);
					$('#rows-head').html(data.columnas).fadeIn(300);
					$('#rows-head').attr('data-dias-periodo',data.dias);
					$('#rows-body').removeClass('modo-horas-extras').addClass('modo-checadas');
				});
				_dad.attr('data-actual', _page);
				$('#frm-pagination-lab').html(_page  +"/" + _pages );
			}else{
				_lista_callback_not_ok(data);
			}//end if
		});
	});
	$(document).on('click','#frm-tabs #btn-horas-extra.unactive', function(){
		var _btn = $(this);
		if ($('#rows-body').hasClass('modo-checadas')){
			_get_horas_extras(_btn, 1);
		}//endif
	});

	$(document).on('click','#frm-tabs #btn-refresh-lista', function(){


		if ($('#rows-body').hasClass('modo-checadas')){
			_get_checadas( 1);
		}//end if

		if ($('#rows-body').hasClass('modo-horas-extras')){
			var _btn = $()
			_get_horas_extras(_btn, 1);
			return false;
		}//endif

	});
	$(document).on('click', '#frm-pagination-controls .page-ctrl ', function(e){
		e.preventDefault();

		var _action = $(this).attr('data-action');
		var _dad = $('#frm-pagination-controls');

		var _actual = parseInt(_dad.attr('data-actual'));
		var _pages = parseInt(_dad.attr('data-pages'));

		if ((parseInt(_actual) >= parseInt(_pages))&& (_action ==='next' || _action ==='last')) return false;
		if ((parseInt(_actual) <= 1) && (_action ==='past' || _action ==='first')) return false;

		//-------------------------------------------
		// transicion para cargas lentas
		//-------------------------------------------
		$('#rows-body').hide();
		$('#dia-cont').hide();
		$('#rows-body-cargando').show();
		$('#rows-body-cargando').removeClass('oculto').addClass('visible');
		//-------------------------------------------

		var _ope = _dad.attr('data-ope');
		var _dep = $('#set-departamento').attr('data-departamento');
		var _per = $('#set-periodo').attr('data-periodo');
		var _page = 1;
		var _dias = $('#rows-head').attr('data-dias-periodo');
		switch (_action) {
			case 'first':
				_page = 1;

				if ($('#rows-body').hasClass('modo-horas-extras')) {
					_get_horas_extras($('#btn-horas-extra'),_page);
					return false;
				}//endif
				_get_lista_page(_ope, _page, _dep, _per,_dias, function(data){
					if (data.status =='ok'){
						$('#rows-body-error.visible').fadeOut(150);
						$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
						$('#rows-body-cargando.visible').fadeOut(150, function (){
							$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
							$('#dia-cont').show();
							$('#rows-body').html('');
							$('#rows-body').html(data.html).fadeIn(300);
							$('#rows-head').html(data.columnas).fadeIn(300);
							$('#rows-head').attr('data-dias-periodo',data.dias);
						});
						_dad.attr('data-actual', _page);
						$('#frm-pagination-lab').html(_page  +"/" + _pages );
					}else{
						_lista_callback_not_ok(data);
					}//end if
				});
				break;
			case 'past':
				_page = parseInt(_actual)-1;
				if ($('#rows-body').hasClass('modo-horas-extras')) {
					_get_horas_extras($('#btn-horas-extra'),_page);
					return false;
				}//endif
				_get_lista_page(_ope, _page, _dep, _per,_dias, function(data){
					if (data.status =='ok'){
						$('#rows-body-error.visible').fadeOut(150);
						$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
						$('#rows-body-cargando.visible').fadeOut(150, function (){
							$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
							$('#dia-cont').show();
							$('#rows-body').html('');
							$('#rows-body').html(data.html).fadeIn(300);
							$('#rows-head').html(data.columnas).fadeIn(300);
							$('#rows-head').attr('data-dias-periodo',data.dias);
						});
						_dad.attr('data-actual', _page);
						$('#frm-pagination-lab').html(_page  +"/" + _pages);
					}else{
						_lista_callback_not_ok(data);
					}//end if
				});
				break;
			case 'next':
				_page = parseInt(_actual)+1;
				if ($('#rows-body').hasClass('modo-horas-extras')) {
					_get_horas_extras($('#btn-horas-extra'),_page);
					return false;
				}//endif
				_get_lista_page(_ope, _page, _dep, _per,_dias, function(data){
					if (data.status =='ok'){
						$('#rows-body-error.visible').fadeOut(150);
						$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
						$('#rows-body-cargando.visible').fadeOut(150, function (){
							$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
							$('#dia-cont').show();
							$('#rows-body').html('');
							$('#rows-body').html(data.html).fadeIn(300);
							$('#rows-head').html(data.columnas).fadeIn(300);
							$('#rows-head').attr('data-dias-periodo',data.dias);
						});
						_dad.attr('data-actual', _page);
						$('#frm-pagination-lab').html(_page  +"/" + _pages);
					}else{
						_lista_callback_not_ok(data);
					}//end if
				});
				break;
			case 'last':
				_page = parseInt(_pages);
				if ($('#rows-body').hasClass('modo-horas-extras')) {
					_get_horas_extras($('#btn-horas-extra'),_page);
					return false;
				}//endif
				_get_lista_page(_ope, _page, _dep, _per, _dias, function(data){
					if (data.status =='ok'){
						$('#rows-body-error.visible').fadeOut(150);
						$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
						$('#rows-body-cargando.visible').fadeOut(150, function (){
							$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
							$('#dia-cont').show();
							$('#rows-body').html('');
							$('#rows-body').html(data.html).fadeIn(300);
							$('#rows-head').html(data.columnas).fadeIn(300);
							$('#rows-head').attr('data-dias-periodo',data.dias);
						});
						_dad.attr('data-actual', _page);

						$('#frm-pagination-lab').html(_page +"/" + _pages);
					}else{
						_lista_callback_not_ok(data);
					}//end if
				});
				break;
			default:
				return;
		}//end switch
	});

	$(document).on('click','.frm-employees .sel-box-title .close, .frm-employees #frm-buscar .close', function(){
		var _who = $(this).data('who');
		var _btn = $(this).data('btn');
		$(_who).fadeOut(300, function(){
			$(_btn).removeClass('active').addClass('unactive');
		});
	});
	$(document).on('click', '#frm-tabs #btn-buscar.tab.unactive', function(){
		//console.log(2);
		var _btn = $(this);
		var _buscar = $('.frm-employees #frm-buscar.frm-set');
		_buscar.find('input#inp-buscar').val('');
		$('.frm-employees .frm-set.visible').fadeOut(300);
		$('#frm-tabs .tab.panel.active').removeClass('active').addClass('unactive');
		_btn.removeClass('unactive').addClass('active');
		_buscar.fadeIn(300).removeClass('oculto').addClass('visible');
		_buscar.find('input#inp-buscar').focus();

	});
	$(document).on('click','.frm-lista #frm-buscar #btn-exec',function(){
		var _alter = $('.frm-lista #frm-buscar #inp-buscar').val();
		if(_alter.length >0){
			var _ope = $('.frm-lista').attr('data-op');
			var _per = $('#set-periodo').attr('data-periodo');
			var _dias = $('#rows-head').attr('data-dias-periodo');
			if($('.frm-lista #rows-body').hasClass('modo-checadas')){
				_get_lista_by_employee(_alter, _per,_ope,_dias);
				return false;
		  }//end if
			if($('.frm-lista #rows-body').hasClass('modo-horas-extras')){
				_get_horas_by_employee(_alter, _per,_ope,_dias);
				return false;
		  }//end if
		}else{
			return false;
		}//end if
	});
	$(document).on('keyup','.frm-lista #frm-buscar #inp-buscar',function(e){
		if(e.which != 13) return false;
		var _alter = $('.frm-lista #frm-buscar #inp-buscar').val();
		if(_alter.length >0){
			var _ope = $('.frm-lista').attr('data-op');
			var _per = $('#set-periodo').attr('data-periodo');
			var _dias = $('#rows-head').attr('data-dias-periodo');
			if($('.frm-lista #rows-body').hasClass('modo-checadas')){
				_get_lista_by_employee(_alter, _per,_ope,_dias);
				return false;
		  }//end if
			if($('.frm-lista #rows-body').hasClass('modo-horas-extras')){
				_get_horas_by_employee(_alter, _per,_ope,_dias);
				return false;
			}
		}else{
			return false;
		}//end if
	});

	$(document).on('click', '#frm-tabs .tab.panel.unactive', function(){
		//console.log(1);
		var _btn = $(this);
		var _who = _btn.attr('data-panel');
		var _ant = $('#frm-tabs .tab.panel.active').data('panel');
		if (_ant === undefined) _ant = '#frm-set-periodo';
		//en caso de que ventan abuscar este abierta
		$('.frm-employees #frm-buscar.frm-set.visible').fadeOut(300);
		$('.frm-employees #frm-tabs #btn-buscar.tab.active').removeClass('active').addClass('unactive');
		//----------
		$(_ant).fadeOut(300, function(){
			$('#frm-tabs .tab.panel.active').removeClass('active').addClass('unactive');
			_btn.removeClass('unactive').addClass('active');
			$(_who).fadeIn(300 , function(){
				$(_who).removeClass('oculto').addClass('visible');
				switch (_who) {
					//------------------------------------------------------------
					case '#frm-set-departamento':
						var _ope = _btn.data('ope');
						_get_departamentos_for_set(_ope, function(data){
							if(data.status == 'ok'){
								var _tree = list_to_tree(data.arreglo);
								//console.log(_tree);
								$('#frm-set-departamento .set-departamento-arbol .cargando').fadeOut(300,function(){
									$('#frm-set-departamento .set-departamento-arbol').jstree({ 'core' : { 'data' : _tree	}});
								});
							}else{
								_lista_callback_not_ok(data);
							}//end if
						});
						break;
						case '#btn-info':
							//console.log(1);
							break;
						//------------------------------------------------------------
						case '#frm-set-periodo':
							//console.log(1);
							break;

					default:
						return;
				}//end if;
			});
		});
	});




	$(document).on("click", '#frm-set-departamento .jstree-anchor',function () {
		//-------------------------------------------
		// transicion para cargas lentas
		//-------------------------------------------
		$('#rows-body').hide();
		$('#dia-cont').hide();
		$('#rows-body-cargando').show();
		$('#rows-body-cargando').removeClass('oculto').addClass('visible');
		//-------------------------------------------

	  var _dep = $(this).attr('id').replace(/_anchor/g,'');
		var _dad = $('#frm-set-departamento');
		var _ope = _dad.attr('data-ope');
		$('#set-departamento').attr('data-departamento', _dep);
		var _per = $('#set-periodo').attr('data-periodo');
		var _page = 1;
		var _dias = $('#rows-head').attr('data-dias-periodo');
		_get_lista_page(_ope, _page, _dep, _per, _dias, function(data){
			if (data.status =='ok' && data.html != undefined){
				//console.log(data.html);
				$('#rows-body-error.visible').fadeOut(150);
				$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
				$('#rows-body-cargando.visible').fadeOut(150, function (){
					$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
					$('#dia-cont').show();
					$('#rows-body').html('');
					$('#rows-body').html(data.html).fadeIn(300);
					$('#rows-head').html(data.columnas).fadeIn(300);
					$('#rows-head').attr('data-dias-periodo',data.dias);
					var _row = $('#rows-body').children('.empl.row').first();
					var _id = _row.data('employee');
					_load_img_col(_id, function(){
						var _pos = _row.children('div.last').html()|| _row.children('div.last13').html() || _row.children('div.last16').html();
						var _col = _row.children('div.id').html() + ' - ' + _row.children('div.name').html()  + ' - ' + _pos;
						$('#dia-wrapper-head #dia-wrapper-title').html(_col);
						_set_focus(_row);
					});
				});

				_dad.attr('data-actual', _page);
				if (data.pages != undefined ){
					if (data.pages > 1) {
						$('#frm-pagination').addClass('pages');
					}else{
						$('#frm-pagination').removeClass('pages');
					}//end if
					$('#frm-pagination').attr('data-pages', data.pages);
					$('#frm-pagination-controls').attr('data-pages', data.pages);
					$('#frm-pagination').attr('data-actual', 1);
					$('#frm-pagination-controls').attr('data-actual', 1);
					$('#frm-pagination-lab').html(_page +"/" + data.pages);
				}//end if
			}else{
				_lista_callback_not_ok(data);
			}//end if
		});
	});
	//-------------------------------------------------------
	// Control de Filtro de lista
	//-------------------------------------------------------

	$(document).on('keyup','div.frm #rows-head #list-filter-cont #filter-inp-cont input#filter-input',function(e){
		var _inp = $(this);
    var _str = _inp.val();
    var _sou = _inp.attr('data-source');

		if (_str.length == 0) {
      $(_sou + ' .empl.row.oculto').removeClass('oculto');
      _inp = null;
      return false;
    }//end if
		$(_sou + ' .empl.row').addClass('oculto');
    $(_sou + ' .empl.row.oculto').each(function(){
      var _item = $(this);
      var _code = _item.attr('data-code');
      var _name = _item.attr('data-name');
      var _alte = _item.attr('data-_alter_id');
      if (!$.isNumeric(_code)) _code = _code.toLowerCase();
      if (!$.isNumeric(_name)) _name = _name.toLowerCase();
      if (!$.isNumeric(_str)) _str = _str.toLowerCase();
      if(_find_str(_code,"*"+_str+"*") || _find_str(_name,"*"+_str+"*") || _find_str(_alte,"*"+_str+"*")){
        _item.removeClass('oculto').prependTo(_sou);
      }//end if
    });

	});
	$(document).on('mouseover', 'div.frm #rows-head #list-filter-cont.hide', function(){
		$(this).removeClass('hide').addClass('show');
	});
	$(document).on('mouseleave', 'div.frm #rows-head #list-filter-cont.show.closed', function(){
		$(this).removeClass('show').addClass('hide');
	});
	$(document).on('click', 'div.frm #rows-head #list-filter-cont.show #filter-ico-cont', function(){
    var _ico = $(this);
    var _item = $('div.frm #rows-head #list-filter-cont');
		if(_item.hasClass('closed')){
			_item.removeClass('closed').addClass('opened');
      _ico.children('i.fa').removeClass('fa-filter').addClass('fa-angle-left');
			$('div.frm #rows-head #list-filter-cont #filter-inp-cont input#filter-input').focus();
			_item = null;
			return false;
		}//end if
		if(_item.hasClass('opened')){
			_item.removeClass('opened').addClass('closed');
      _ico.children('i.fa').removeClass('fa-angle-left').addClass('fa-filter');
			$('div.frm #rows-head #list-filter-cont #filter-inp-cont input#filter-input').val('');
      $('#rows-body .empl.row.oculto').removeClass('oculto');

			_item = null;
			return false;
		}//end if
	});
	$(document).on('mouseleave','.frm-lista .data-item.ausentismos',function(){
		$('.frm-lista .data-item.ausentismos .g .le-causa.visible').removeClass('visible').addClass('oculto');
		$('.frm-lista .data-item.ausentismos .g .has-causa.oculto').removeClass('oculto').addClass('visible');
	});
	$(document).on('click','.frm-lista .data-item.ausentismos .g .has-causa.visible',function(){
		var _g = $(this).parent();
		var _v= $(this);

		$('.frm-lista .data-item.ausentismos .g .has-causa.oculto').removeClass('oculto').addClass('visible');
		$('.frm-lista .data-item.ausentismos .g .le-causa.visible').removeClass('visible').addClass('oculto');
		_v.removeClass('visible').addClass('oculto');
		_g.find('.le-causa').removeClass('oculto').addClass('visible');
	});
	$(document).on('click','.frm-lista .data-item.ausentismos .g .le-causa.visible',function(){
		var _g = $(this).parent();
		var _v= $(this);

		$('.frm-lista .data-item.ausentismos .g .le-causa.visible').removeClass('visible').addClass('oculto');
		_v.removeClass('visible').addClass('oculto');
		_g.find('.has-causa').removeClass('oculto').addClass('visible');

	});

	$(document).on('mouseover', '.frm-lista #rows-body .cell-tooltip', function(){
		var _tooltip = $(this);
		_move_tooltip(_tooltip);
	});
	$(document).on('scroll', '.frm-lista #rows-body', function(){
		//$('.frm-lista #rows-body .cell-tooltip')
		//$('.frm-lista #rows-body .cell-tooltip').remove();
	});


	//-------------------------------------------------------
	//fin de Ready DOCUMENT
	//-------------------------------------------------------
});
function _set_focus(_row , _first = 0, callback){
	//console.log(1);
	//console.log($('#rows-body .empl.row').first().children('.cell.dia');
	if(_row === undefined){
		$('#rows-body .empl.row').first().children('.cell.dia').first().focus();
	}else{
		$('#rows-body .empl.row.selected').removeClass('selected')
		_row.addClass('selected');
		var _emp = _row.data('employee');
		var _per = _row.data('periodo');
		var _act = $('#dia-cont').data('employee');
		// Carga datos de barra dia
		if (_act !== _emp || _first === 1){
			var _car = $('#dia-wrapper-content > .data > .data-item > .box > .content > .cargando.visible');
			_car.removeClass('visible').addClass('oculto');
			var _params = {action: 'data::employee', emp : _emp, per: _per};
			_jlista_post_proc( _params ,function(data){
				if (data.status ==='ok'){
					$('#dia-cont').data('employee', _emp);
					var _info = $('#dia-wrapper-content > .data > .data-item.info > .box > .content > .datos');
					_info.html(data.info).removeClass('oculto').addClass('visible');
					var _chec = $('#dia-wrapper-content > .data > .data-item.checadas > .box > .content > .datos-checadas');
					_chec.html(data.checadas).removeClass('oculto').addClass('visible');
					var _jorn = $('#dia-wrapper-content > .data > .data-item.checadas > .box > .content > .datos-jornadas');
					_jorn.html(data.jornadas).removeClass('oculto').removeClass('visible').addClass('oculto');
					var _ause = $('#dia-wrapper-content > .data > .data-item.ausentismos > .box > .content > .datos');
					_ause.html(data.ausentismos).removeClass('oculto').addClass('visible');
				}else{
					_lista_callback_not_ok(data);
				}//end if
			});
		}//end if
	}//end if
	if(callback) callback();
}// end function

function _effect_cargando(_cargando, callback){
	if (_cargando.hasClass('visible')){
		_cargando.fadeOut(300).removeClass('visible').addClass('oculto',function(){
			if(callback) callback();
		});
	}else{
		_cargando.fadeIn(300).fadeOut(300, function(){
			if(callback) callback();
		});
	}//End if
}
function list_to_tree(list) {
    var map = {}, node, roots = [], i;
    for (i = 0; i < list.length; i += 1) {
				//console.log(list[i].id);
        map[list[i].id] = i; // initialize the map
        list[i].children = []; // initialize the children
    }//end for

    for (i = 0; i < list.length; i += 1) {
        node = list[i];
        if (node.parentid !== "0") {
            // if you have dangling branches check that map[node.parentId] exists
						if(list[map[node.parentid]] == undefined){
							roots.push(node);
						}else{
							list[map[node.parentid]].children.push(node);
						}//end if
        } else {
            roots.push(node);
        }//end if
    }//end for
    return roots;
}
function _get_departamentos_for_set(_ope, callback){
	if(_ope == undefined) return;
	var _params = {action: 'get_departamentos_for_set'
								,ope: _ope };
	var req = $.ajax({
		url: 'frm.lista.proc.php',
		type: 'POST',
		dataType: 'json',
		data: _params
	});
	req.done(function(data) {
		//console.log(data);
		if (callback) callback(data); else console.log(data);
	});
	req.fail(function(data) {
		if (callback) callback(data); else console.log(data);
	});
}
//-------------------------------------------------------------------------------------------------
function _lista_callback_not_ok(data){
  if (data.status == 'login'){
    window.location = data.url;
    //console.log(data);
  }else{
    console.log(data);
		return false;
  }// end if
}//end function
function _jlista_post_proc( _params,callback){
	var req = $.ajax({
		url: 'frm.lista.proc.php',
		type: 'POST',
		dataType: 'json',
		data: _params
	});
	req.done(function(data) {
		//console.log(data);
		if (callback) callback(data); else console.log(data);
	});
	req.fail(function(data) {
		if (callback) callback(data); else console.log(data);
	});
}//end function
function _get_lista_page( _ope, _page, _dep, _per, _dias, callback){
	//console.log(_ope);
	//console.log(_per);
	var _params = {action: 'pagination'
								,ope: _ope
								,dep: _dep
								,per: _per
								,dias: parseInt(_dias)
								,page: parseInt(_page)};
	var req = $.ajax({
		url: 'frm.lista.proc.php',
		type: 'POST',
		dataType: 'json',
		data: _params
	});
	req.done(function(data) {
		//console.log(data);
		if (callback) callback(data); else console.log(data);
	});
	req.fail(function(data) {
		if (callback) callback(data); else console.log(data);
	});
}//end function
function set_cell_letra(_letra, _cell, _per, _emp){
	//console.log(_letra);
	//console.log(_cell);
	if (_cell.hasClass('work')){
		return;
	}else{
		var _params = {action:'letra', letra: _letra , emp: _emp, per:_per};
		var req = $.ajax({
			url: 'frm.lista.proc.php',
			type: 'POST',
			dataType: 'json',
			data: _params
		});
		req.done(function(data) {
			//console.log(data);
			if (data.status === 'ok') {
				_cell.children('span').html(data.letra);
			}else{
				_lista_callback_not_ok(data);
			}//end if
		});
		req.fail(function(data) {
			console.log(data);
		});
		req.always(function(data) {	return;	});
	}//end if
}// end function
//-----------------------------------------------------------------------------------------------
function btn_tools_click(act, val){
	//console.log(val);
	var dad = act.parent();
	if (val == 0){
		act.attr('data-val',1);
		dad.addClass('active');
		dad.animate({
			width: 328},
			300, function() {
				act.children('i.fa').addClass('fa-rotate-180');
				dad.children('.oculto').removeClass('oculto').addClass('ocultar');

		});
	}else{
		dad.children('.ocultar').removeClass('ocultar').addClass('oculto');
		dad.animate({
			width: 33},
			300, function() {
				dad.removeAttr('style');
				act.attr('data-val',0);
				act.children('i.fa').removeClass('fa-rotate-180');
				dad.removeClass('active');
				$('input#tb_buscar').val('');
		});

	}//end if
	//act = null;
	//dad = null;
}//end function
function btn_show_dia_click(val, act){
	if (val == 1 ) {val = 0;} else{ val = 1;}
	act.attr('data-status', val);
	act.children('i.fa').removeClass((val == 1 ? 'fa-square-o':'fa-check-square-o')).addClass((val == 1 ? 'fa-check-square-o':'fa-square-o'));
	hide_dia_wrapper();
}//end function
function move_cell_dia(r, i){

	//console.log("div.empl[data-id='"+r+"'] div.cell.dia[tabindex='"+i+"']");
	var c = parseInt($('#rows-body .empl.row').length);
	if(r < 1 || i == 17 || i < 1 || r > c){ return null;}

	$("div.empl[data-id='"+r+"'] div.cell.dia[tabindex='"+i+"']").focus();
	$("div.empl div.cell.dia[focused='true']").attr('focused' ,'false');
	$("div.empl[data-id='"+r+"'] div.cell.dia[tabindex='"+i+"']").attr('focused' ,'true');
}//end function
function hide_dia_wrapper(){

	var dia = $('#rows-body #dia_wrapper');
	//dia.fadeOut(300);
	if(!dia.hasClass('oculto')){
		dia.slideUp(300,function(){
			$('#rows-body span.border-interno').removeClass('active');
			dia.addClass('oculto');
			dia = null;
		});

	}//end if

}//End function
function show_dia_wrapper(r,i){
	var act = $("div.empl[data-id='"+r+"'] div.cell.dia[tabindex='"+i+"']");
	$('#rows-body span.border-interno').removeClass('active');
	act.children('span.border-interno').addClass('active');
	var dia = $('#rows-body #dia_wrapper');

	_dia_pos(act, function(){
		if (dia.hasClass('oculto')){
			dia.slideDown(300, function(){
				dia.removeClass('oculto');
				act = null;
			});
		}
	});
	//dia.fadeIn(300);

	//dia = null;
}//end function

function _dia_pos(act, callback, _side){

	//_side = _side || false;
	//console.log(_side);
	var dia = $('#rows-body #dia_wrapper');
	var _top = act.position().top;
	var _lef = act.position().left;
	var _wid = dia.width();

	if(_side == true || $('#sidebar').width() == 45 ){
		_lef = _lef - 205;
	}//end if
	//console.log(_lef);
	dia.children('span').removeClass('border-top-left border-top-right');
	if(act.hasClass('der')){
		dia.children('span').addClass('border-top-left');
		dia.animate({'top' : (_top + 32) + 'px' , 'left': (_lef + (_wid - 20)) + 'px'},50,function(){
			act = null;
			if (callback) callback();
		});
	}else{
		dia.children('span').addClass('border-top-right');
		dia.animate({'top' : (_top + 32) + 'px' , 'left': (_lef + 11 ) + 'px'},50,function(){
			act = null;
			if (callback) callback();
		});
	}//end if

}//end function

function _load_img_col(_id, callback){
	var _url = 'https://asistenciarcd.aicollection.local:444/app/proc.photo?id=';
	_url = _url + _id;
	//console.log(_url);
	var _img = new Image();
	_img.src = _url;
	_img.onload = function(){
		$('#dia-wrapper-content img#photo_item').attr('src', _url).load();
		$('img#bar-photo-item').attr('src', _url).load();
	}//end function
	_img.onerror = function(){
		_url = 'https://asistenciarcd.aicollection.local:444/imagenes/no_image_profile.jpg';
		$('#dia-wrapper-content img#photo_item').attr('src', _url).load();
		$('img#bar-photo-item').attr('src', _url).load();
	}//end function
	if (callback) callback();
}//end function

function _get_horas_extras(_btn,_page){

	//var _btn = $(this);

	_btn.removeClass('unactive').addClass('active');
	$('#rows-body').hide();
	$('#dia-cont').hide();
	_btn.find('i.fa').removeClass('fa-clock').addClass('fa-reply');
	$('#rows-body-cargando').show();
	$('#rows-body-cargando').removeClass('oculto').addClass('visible');

	var _dep = $('#set-departamento').attr('data-departamento');
	var _per = $('#set-periodo').attr('data-periodo');
	//var _page = 1;
	var _dias = $('#rows-head').attr('data-dias-periodo');
	var _ctl = $('#frm-pagination-controls');
	var _params = {
			action:'lista:horas:extras',
			page:_page, dep:_dep, per:_per, dias:_dias
	};
	_jlista_post_proc( _params, function(data){
		//console.log(data.html);
		if(data.status === "ok" && data.html === undefined){
			alert('Los colaboradores de este departamento no generan horas extras...');

			//console.log($('#rows-body-cargando.visible'));
			$('#rows-body-error.visible').fadeOut(150);
			$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
			$('#rows-body-cargando.visible').fadeOut(150, function (){
				$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
				_btn.removeClass('active').addClass('unactive');
				_btn.find('i.fa').removeClass('fa-reply').addClass('fa-clock');
				$('#dia-cont').show();
				$('#rows-body').fadeIn(150);

			});
			return false;
		}//end if
		if (data.status === "ok" && data.html != undefined){
			$('#rows-body-error.visible').fadeOut(150);
			$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
			$('#rows-body-cargando.visible').fadeOut(150, function (){
				$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
				$('#dia-cont').show();
				$('#rows-body').html('');
				if($('#rows-body').hasClass('modo-checadas')) $('#rows-body').removeClass('modo-checadas').addClass('modo-horas-extras');
				$('#rows-body').html(data.html).fadeIn(300);
				$('#dia-cont').show();
			});


			_ctl.attr('data-actual', _page);
			$('#frm-pagination-lab').html(_page  +"/" + data.pages );
			_btn = null;
			return false;
		}//end if
		_btn.removeClass('active').addClass('unactive');
		_lista_callback_not_ok(data);
		return false;
	});
}

function _get_lista_by_employee(_emp, _per, _ope, _dias){

	$('#rows-body').hide();
	$('#dia-cont').hide();
	$('#rows-body-cargando').show();
	$('#rows-body-cargando').removeClass('oculto').addClass('visible');
	var _page = 1;
	var _ctl = $('#frm-pagination-controls');
	var _params = {
			action:'get::lista::employee',
			emp:_emp, per:_per, ope:_ope, dias:_dias
	};
	_jlista_post_proc( _params, function(data){
		if (data.status === "ok" && data.html != undefined){
			$('#rows-body-error.visible').fadeOut(150);
			$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
			$('#rows-body-cargando.visible').fadeOut(150, function (){
				$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
				$('#dia-cont').show();
				$('#rows-body').html('');
				//if($('#rows-body').hasClass('modo-checadas')) $('#rows-body').removeClass('modo-checadas').addClass('modo-horas-extras');
				$('#rows-body').html(data.html).fadeIn(300);
				$('#dia-cont').show();
				_ctl.attr('data-actual', _page);
				$('#frm-pagination-lab').html(_page  +"/" + data.pages );
				return false;
			});

		}//end if
		if(data.status === "error"){
			$('#rows-body-cargando.visible').fadeOut(150);
			$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
			$('#rows-body-error.oculto').fadeIn(150);
			$('#rows-body-error.oculto').removeClass('oculto').addClass('visible');
			_lista_callback_not_ok(data);
				return false;


		}//end if

	});
}

function _get_horas_by_employee(_emp, _per, _ope, _dias){

	$('#rows-body').hide();
	$('#dia-cont').hide();
	$('#rows-body-cargando').show();
	$('#rows-body-cargando').removeClass('oculto').addClass('visible');
	var _page = 1;
	var _ctl = $('#frm-pagination-controls');
	var _params = {
			action:'get::horas::employee',
			emp:_emp, per:_per, ope:_ope, dias:_dias
	};
	_jlista_post_proc( _params, function(data){
		//console.log(data);
		if (data.status === "ok" && data.html != undefined){
			$('#rows-body-error.visible').fadeOut(150);
			$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
			$('#rows-body-cargando.visible').fadeOut(150, function (){
				$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
				$('#dia-cont').show();
				$('#rows-body').html('');
				//if($('#rows-body').hasClass('modo-checadas')) $('#rows-body').removeClass('modo-checadas').addClass('modo-horas-extras');
				$('#rows-body').html(data.html).fadeIn(300);
				$('#dia-cont').show();
				_ctl.attr('data-actual', _page);
				$('#frm-pagination-lab').html(_page  +"/" + data.pages );
				return false;
			});

		}//end if
		if(data.status === "error"){
			$('#rows-body-cargando.visible').fadeOut(150);
			$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
			$('#rows-body-error.oculto').fadeIn(150);
			$('#rows-body-error.oculto').removeClass('oculto').addClass('visible');
			_lista_callback_not_ok(data);
			return false;
		}//end if
		if(data.status == undefined){
			_lista_callback_not_ok(data);
	  }
		return false;
	});
}

function _get_checadas(_page){
	//-------------------------------------------
	// transicion para cargas lentas
	//-------------------------------------------
	$('#rows-body').hide();
	$('#dia-cont').hide();
	$('#rows-body-cargando').show();
	$('#rows-body-cargando').removeClass('oculto').addClass('visible');
	//-------------------------------------------
	var _dad = $('#frm-pagination-controls');
	var _ope = _dad.attr('data-ope');
	var _dep = $('#set-departamento').attr('data-departamento');
	var _per = $('#set-periodo').attr('data-periodo');
	var _dias = $('#rows-head').attr('data-dias-periodo');
	var _pages = parseInt(_dad.attr('data-pages'));

	_get_lista_page(_ope, _page, _dep, _per,_dias, function(data){
		if (data.status =='ok' && data.html != undefined){
			$('#rows-body-error.visible').fadeOut(150);
			$('#rows-body-error.visible').removeClass('visible').addClass('oculto');
			$('#rows-body-cargando.visible').fadeOut(150, function (){
				$('#rows-body-cargando.visible').removeClass('visible').addClass('oculto');
				$('#dia-cont').show();
				$('#rows-body').html('');
				$('#rows-body').html(data.html).fadeIn(300);
				$('#rows-head').html(data.columnas).fadeIn(300);
				$('#rows-head').attr('data-dias-periodo',data.dias);
			});
			_dad.attr('data-actual', _page);
			$('#frm-pagination-lab').html(_page  +"/" + _pages );
		}else{
			_lista_callback_not_ok(data);
		}//end if
	});
	return false;
}

function _ordenar_divs($divs, _orden_tip, _orden_dir,_orden_sou, _dest,_callback){
	//console.log(_divs.length);
	if($divs.length < 2) return false;
	//----------------------------------------
	//$divs = _divs;
	if (_orden_tip === 'alfabetico'  && _orden_dir === 'asc'){
		var _divs_ordenados = $divs.sort(function (a, b) {
				if ($(a).find("div.cell."+_orden_sou).text() > $(b).find("div.cell."+_orden_sou).text()) return 1;
				if ($(a).find("div.cell."+_orden_sou).text() < $(b).find("div.cell."+_orden_sou).text()) return -1;
				if ($(a).find("div.cell."+_orden_sou).text() === $(b).find("div.cell."+_orden_sou).text()) return 0;
    });
    $(_dest).html(_divs_ordenados);
		if (_callback) _callback();
		return false;
	}//end if
	if (_orden_tip === 'alfabetico' && _orden_dir === 'desc'){
		var _divs_ordenados = $divs.sort(function (a, b) {
				if ($(a).find("div.cell."+_orden_sou).text() < $(b).find("div.cell."+_orden_sou).text()) return 1;
				if ($(a).find("div.cell."+_orden_sou).text() > $(b).find("div.cell."+_orden_sou).text()) return -1;
				if ($(a).find("div.cell."+_orden_sou).text() === $(b).find("div.cell."+_orden_sou).text()) return 0;
		});
		$(_dest).html(_divs_ordenados);
		if (_callback) _callback();
		return false;
	}//end if

	if (_orden_tip === 'numerico'  && _orden_dir === 'asc'){
		var _divs_ordenados = $divs.sort(function (a, b) {
				if (parseInt($(a).find("div.cell."+_orden_sou).text()) > parseInt($(b).find("div.cell."+_orden_sou).text())) return 1;
				if (parseInt($(a).find("div.cell."+_orden_sou).text()) < parseInt($(b).find("div.cell."+_orden_sou).text())) return -1;
				if (parseInt($(a).find("div.cell."+_orden_sou).text()) === parseInt($(b).find("div.cell."+_orden_sou).text())) return 0;
    });
    $(_dest).html(_divs_ordenados);
		if (_callback) _callback();
		return false;
	}//end if
	if (_orden_tip === 'numerico' && _orden_dir === 'desc'){
		var _divs_ordenados = $divs.sort(function (a, b) {
				if (parseInt($(a).find("div.cell."+_orden_sou).text()) < parseInt($(b).find("div.cell."+_orden_sou).text())) return 1;
				if (parseInt($(a).find("div.cell."+_orden_sou).text()) > parseInt($(b).find("div.cell."+_orden_sou).text())) return -1;
				if (parseInt($(a).find("div.cell."+_orden_sou).text()) === parseInt($(b).find("div.cell."+_orden_sou).text())) return 0;
		});
		$(_dest).html(_divs_ordenados);
		if (_callback) _callback();
		return false;
	}//end if
}//end function


function _show_tooltip(_cell){
	$('.frm-lista #rows-body .cell-tooltip').remove();
	if (_cell.hasClass('work')) return false;
	var _per = _cell.parent().attr('data-periodo');
	var _emp = _cell.parent().attr('data-employee');
	var _cn = _cell.attr('data-cn');
	var _top = _cell.offset().top;
	var _left = _cell.offset().left;
	var _params = {
		action: 'get::lista::tooltip'
		,per: _per
		,emp: _emp
		,cn: _cn
	};
	_jlista_post_proc(_params, function(data){
		if(data.status === 'ok'){
			var _tooltip = $(data.html);
			//console.log(_tooltip);
			$('.frm-lista #rows-body .cell-tooltip').remove();
			$('.frm-lista #rows-body').append(_tooltip);
			//console.log(_tooltip.width());
			_tooltip.offset({top: (_top + 35), left: (_left - _tooltip.width() + 37)});
			//_tooltip.show();
		}else{
			console.log(data);
		}
	})//end call function

}//end function

function _move_tooltip(_tooltip){
	if(_tooltip.hasClass('abajo')){
		_tooltip.removeClass('abajo').addClass('arriba');
		_tooltip.find('.flecha-cont-up.visible').removeClass('visible').addClass('oculto');
		_tooltip.find('.flecha-cont-down.oculto').removeClass('oculto').addClass('visible');
		var _top = $('.frm-employees #rows-body .empl .cell.dia:focus').offset().top;
		_tooltip.offset({top: (_top - 35)});
		return false;
	}//end if
	if(_tooltip.hasClass('arriba')){
		_tooltip.removeClass('arriba').addClass('abajo');
		_tooltip.find('.flecha-cont-down.visible').removeClass('visible').addClass('oculto');
		_tooltip.find('.flecha-cont-up.oculto').removeClass('oculto').addClass('visible');
		var _top = $('.frm-employees #rows-body .empl .cell.dia:focus').offset().top;
		_tooltip.offset({top: (_top + 35)});
		return false;
	}//end if
}//end function

function _validar_letra(_cell, _which, _callback){
	var _ope = $('.frm-lista').attr('data-op');
	var _per = $('#set-periodo').attr('data-periodo');
	var _cn = _cell.attr('data-cn');
	var _emp = _cell.parent().attr('data-employee');
	//console.log(_cell);
	var _anterior = _cell.find('span.border-interno').html().substring(0,1);
	var _letra = String.fromCharCode(_which);
	_params = {
		action: 'validar::letra::lista'
		,ope: _ope
		,per: _per
		,cn: _cn
		,anterior: _anterior
		,letra: _letra
		,emp: _emp
	};
	_jlista_post_proc(_params, function(data){
		//console.log(data);
		if(_callback) _callback(data);
	});
}// end function
function _validar_atras(_cell, _callback){
	var _ope = $('.frm-lista').attr('data-op');
	var _per = $('#set-periodo').attr('data-periodo');
	var _cn = _cell.attr('data-cn');
	var _emp = _cell.parent().attr('data-employee');
	//console.log(_cell);
	var _anterior = _cell.find('span.border-interno').html().substring(0,1);
	//var _letra = String.fromCharCode(_which);
	_params = {
		action: 'validar::atras::lista'
		,ope: _ope
		,per: _per
		,cn: _cn
		,anterior: _anterior
		,emp: _emp
	};
	_jlista_post_proc(_params, function(data){
		//console.log(data);
		if(_callback) _callback(data);
	});
}// end function

function _show_dialog_error(_msg){
	var _dialog;
	var _option = {
			autoOpen: false,
			resizable: false,
			height: 'auto',
			maxHeight: 175,
			width: 450,
			modal: true,
			show: { effect: 'drop', direction: 'up' },
			hide: { effect: 'drop', direction: 'up' },
			buttons: {
				'Aceptar': function() {
					_dialog.dialog( 'close');
		}},close: function(){
			_dialog.dialog('destroy').remove();
			_dialog = 0;
			_dialog = null;
		}};
		var _div = "<div title='Mov. no permitido' class='fn'>";
					_div+= "<i style='width: 60px; text-align:center; height: 40px; line-height:40px; vertical-align:middle;' class='enlinea rojo floL fa fa-2x fa-exclamation-triangle'></i>";
					_div+= "<div style='height: 40px; line-height:40px; vertical-align:middle;' class='enlinea floL fn'>"+_msg+"</div>";
				_div+= "</div>";
		var $div = $(_div);
		_dialog = $div.dialog(_option);
		_dialog.dialog('open');
}
