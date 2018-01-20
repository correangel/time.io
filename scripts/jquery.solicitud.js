$(document).ready(function(){

  $(document).on('keydown', '#tabs-sol-data #_alter_id', function(e){
    var inp = $(this);
    var _code = $(this).val();
    //var _ope = $(this).attr('data-ope');
    //console.log(e.keyCode);
    if((e.keyCode === 13 || e.keyCode === 9) && (_code !== undefined && _code.length !== 0 )){

      var _stu = $('#tabs-sol-data .gpo-captura.code .msg i.fa');
      _stu.removeClass('fa-angle-left fa-times-circle fa-check-circle rojo verde').addClass('fa-cog fa-spin');

      var _params = {
         code: _code
        ,action: 'get::data::employee'
      };
      //console.log(_params);
      _solicitud_proc(_params, function(data){
        if (data.status === 'ok'){
          var _tar =$('#tabs-sol-data .tarjeta .data');
          //console.log(data.result.msg);
          if(data.result.msg === 'OK'){
            _load_img(data.result.emp, $('#data-employee #employee-photo'));
            $('#tabs-sol-data .gpo-captura.code .le-msg').html('');
            _tar.children('#_nombre').val(data.result.nombre).attr('data-emp',data.result.emp);
            _tar.children('#_locacion').val(data.result.locacion).attr('data-loc',data.result.loc);
            _tar.children('#_departamento').val(data.result.departamento).attr('data-dep',data.result.dep);
            _tar.children('#_posicion').val(data.result.posicion).attr('data-loc',data.result.pos);
            _stu.removeClass('fa-cog fa-spin fa-times-circle fa-angle-left rojo verde').addClass('fa-check-circle verde');
            $('#tabs-sol-data #id_role').focus();
          }else{
            $('#data-employee #employee-photo').attr('src', '/imagenes/no_image_profile.jpg').load();
            _tar.children('#_nombre').val('').attr('data-emp','');;
            _tar.children('#_locacion').val('').attr('data-loc','');
            _tar.children('#_departamento').val('').attr('data-dep','');
            _tar.children('#_posicion').val('').attr('data-loc','');
            $('#tabs-sol-data .gpo-captura.code .le-msg').html(data.result.msg);
            _stu.removeClass('fa-cog fa-spin fa-check-circle fa-angle-left rojo verde').addClass('fa-times-circle rojo');
            inp.focus();
          }//end if
        }else{
          _solicita_callback_not_ok(data);
        }//end if
      });
    }else{
      return;
    }//end if
  });
  $(document).on('click', '#frm-departamento .departamento-item', function(){
    $('.filtra').val('');
    $('#frm-departamento .for-filtra').removeClass('oculto');
  });

  $(document).on('click', '#frm-locacion .locacion', function(){
    $('.filtra').val('');
    $('#frm-locacion .for-filtra').removeClass('oculto');
    var _act = $(this);
    if(_act.hasClass('active')) return;
    var _loc = _act.attr('id');
    $('#frm-locacion .locacion .check').addClass('oculto');
    $('#frm-locacion .locacion.active').removeClass('active');
    _act.addClass('active');
    $('#frm-departamento #departamento-rows').fadeOut(300, function(){
      $('#frm-departamento #departamento-rows').addClass('oculto');
      $('#frm-departamento .cargando.oculto').fadeIn(300, function(){
        $('#frm-departamento .cargando.oculto').removeClass('oculto');
        var _nav = $('.frm').data('nav');
        var _params = {action: 'get::departamentos'
                      ,nav: _nav
                      ,loc: _loc};
        _solicitud_proc(_params, function(data){
          if(data.status === 'ok'){
            //console.log(data);
            $('#frm-departamento #departamento-rows.oculto').html('');
            $('#frm-departamento #departamento-rows.oculto').html(data.html);
            $('#frm-departamento .cargando').fadeOut(300, function(){
              $('#frm-departamento .cargando').addClass('oculto');
              //----------------------------
              // Remarcar departamentos.
              //----------------------------
              var c= 0;
              var e = parseInt($('#departamento-rows.oculto > .departamento-item').length);
              $('#cont-deptos.contenedor.has-options #options.delete .option').each(function(){
                var _id = "#departamento-rows > #"+$(this).attr('id') + " > div.check > i.fa-square-o";
                $(_id).removeClass('fa-square-o').addClass('fa-check-square-o');
                c++;
                if(e===c && $('.locacion.for-filtra.active .check i.fa').hasClass('fa-square-o')){
                  $('.locacion.for-filtra.active .check i.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
                }else{
                    $('.locacion.for-filtra.active .check i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
                }//end if
              });
              //----------------------------
              $('#frm-departamento #departamento-rows.oculto').fadeIn(300,function(){
                $('#frm-departamento #departamento-rows.oculto').removeClass('oculto');
                $('#frm-locacion .locacion.active .check.oculto').removeClass('oculto');
              });
            });
          }else{
            _solicita_callback_not_ok(data);
            $('#frm-departamento #departamento-rows.oculto').html('');
            $('#frm-departamento #departamento-rows.oculto').html(data.error_html);
            $('#frm-departamento .cargando').fadeOut(300, function(){
              $('#frm-departamento .cargando').addClass('oculto');
              $('#frm-departamento #departamento-rows.oculto').fadeIn(300,function(){
                $('#frm-departamento #departamento-rows.oculto').removeClass('oculto');
              });
            });
          }//end if
        });
      });
    });
  });
  $(document).on('keyup', '#frm-tabs-head .filtra', function(e){
    //console.log(e.which);
    var _inp = $(this);
    var _str = _inp.val();
    var _sou = _inp.attr('data-source');
    //console.log(_str.length);
    if (_str.length == 0) {
      $(_sou + ' .for-filtra.oculto').removeClass('oculto');
      return false;
    }//end if
    $(_sou + ' .for-filtra').addClass('oculto');
    $(_sou + ' .for-filtra.oculto').each(function(){
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

  $(document).on('click', '.locacion.active .check', function(){
    var _che = $(this);
    //--------------------------------------------------------------
    if(_che.children('i.fa').hasClass('fa-square-o')){
      _che.children('i.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
      $('#departamento-rows .check i.fa.fa-square-o').removeClass('fa-square-o').addClass('fa-check-square-o');
      var _depto;
      $('#departamento-rows .check').each(function(){
         _che = $(this);
         $("#cont-deptos.has-options #options #" + _che.parent().attr('id') +".depto-selected").remove();
         _depto = $("<div id='"+_che.parent().attr('id')+"' class='fs option depto-selected bloque'><div class='txt fs floL enlinea'>"+_che.parent().attr('data-code') +' - ' +_che.parent().attr('data-name')+"</div><i class='fa fa-1x fa-trash-o'></i></div>")
        $('#cont-deptos.has-options #options').append(_depto);
      });
    }else{
      _che.children('i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
      $('#departamento-rows .check i.fa.fa-check-square-o').removeClass('fa-check-square-o').addClass('fa-square-o');
      $('#departamento-rows .check').each(function(){
        _che = $(this);
        $("#cont-deptos.has-options #options #" + _che.parent().attr('id') +".depto-selected").remove();
      });
    }//end if
    $('#cont-deptos-title #txt').html($('#cont-deptos.has-options #options .depto-selected').length + ' Departamentos Seleccionados');
    //console.log(_che.parent().attr('id'));
  });
  $(document).on('click', '.departamento-item .check', function(){
    var _che = $(this);
    if(_che.children('i.fa').hasClass('fa-square-o')){
      _che.children('i.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
      var _depto = $("<div id='"+_che.parent().attr('id')+"' class='fs option depto-selected bloque'><div class='txt fs floL enlinea'>"+_che.parent().attr('data-code') +' - ' +_che.parent().attr('data-name')+"</div><i class='fa fa-1x fa-trash-o'></i></div>")
      $('#cont-deptos.has-options #options').append(_depto);
      //$('#departamento-rows .check i.fa.fa-square-o').removeClass('fa-square-o').addClass('fa-check-square-o');
      var e = parseInt($('#departamento-rows > .departamento-item').length);
      var c = parseInt($('#departamento-rows > .departamento-item > .check > i.fa-check-square-o').length);
      if(e===c && $('.locacion.for-filtra.active .check i.fa').hasClass('fa-square-o')){
        $('.locacion.for-filtra.active .check i.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
      }//end if
    }else{
      if($('.locacion.for-filtra.active .check i.fa').hasClass('fa-check-square-o')){
        $('.locacion.for-filtra.active .check i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
      }//end if
      _che.children('i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
      $("#cont-deptos.has-options #options #" + _che.parent().attr('id') +".depto-selected").remove();
      //$('#departamento-rows .check i.fa.fa-check-square-o').removeClass('fa-check-square-o').addClass('fa-square-o');
    }//end if
    $('#cont-deptos-title #txt').html($('#cont-deptos.has-options #options .depto-selected').length + ' Departamentos Seleccionados');
  });

  $(document).on('click', '.depto-selected i.fa-trash-o', function(){
    var _item = $(this).parent();
    var _id =  _item.attr('id');
    //console.log(_id);
    _item.slideUp(150, function(){
      _item.remove();
      $("#departamento-rows #"+_id+".departamento-item .check i.fa-check-square-o").removeClass('fa-check-square-o').addClass('fa-square-o');
      $('#cont-deptos-title #txt').html($('#cont-deptos.has-options #options .depto-selected').length + ' Departamentos Seleccionados');

      var e = parseInt($('#departamento-rows > .departamento-item').length);
      var c = parseInt($('#departamento-rows > .departamento-item > .check > i.fa-check-square-o').length);
      if(e===c && $('.locacion.for-filtra.active .check i.fa').hasClass('fa-square-o')){
        $('.locacion.for-filtra.active .check i.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
      }else{
          $('.locacion.for-filtra.active .check i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
      }//end if
    });
  });

  $(document).on('click', '#tabs-sol-data #btns #btn-enviar.noerror', function(){
    var _btn = $(this);
    var _emp = $('#tabs-sol-data #_nombre').attr('data-emp');
    if (_emp === undefined || _emp ==='') {
      _solicitud_error_enviar(_btn,'Selecciona el empleado...');
      $('#tabs-sol-data #_alter_id').focus();
      return false;
    }//enf if

    var _rol = $('#tabs-sol-data #id_role').attr('data-id');
    if (_rol === undefined || _rol ==='') {
      _solicitud_error_enviar(_btn,'Selecciona el Rol...');
      $('#tabs-sol-data #id_role').focus();
      return false;
    }//enf if

    var _correo = $('#tabs-sol-data #_correo').val();
    if (_correo === undefined || _correo ==='') {
      _solicitud_error_enviar(_btn,'Captura el correo...');
      $('#tabs-sol-data #_correo').focus();
      return false;
    }//enf if
    var _dom = $('#tabs-sol-data #id_email').val();
    if (_dom === undefined || _dom ==='') {
      _solicitud_error_enviar(_btn,'Selecciona el Dominio de Correo...');
      $('#tabs-sol-data #id_mail').focus();
      return false;
    }//enf if
    var _deptos = parseInt($('#cont-deptos #options .option.depto-selected').length);
    if (_deptos === undefined || _deptos ===0) {
      _solicitud_error_enviar(_btn,'Selecciona los departamentos...');
      $('#tabs-sol-data #cont-departamentos-title').focus();
      return false;
    }//enf if

    var _departamentos = "";
    var _first = true;
    $('#cont-deptos #options .option.depto-selected').each(function(){
      //console.log(_first);
      if(_first===true){
        _departamentos+=$(this).attr('id');
        _first = false;
      }else{
        _departamentos+=","+$(this).attr('id');
      }//end if
    });
    var _nav = $('.frm').data('nav');
    var _ope = $('.frm').data('ope');
    var _loc = $('#tabs-sol-data #_locacion').attr('data-loc');
    //var _dom = $('#tabs-sol-data #id_mail').val();
    var _mai = $('#tabs-sol-data #id_email').attr('data-id');
    var _params = {
       action: 'insert::solicitud'
      ,nav: _nav
      ,ope: _ope
      ,emp: _emp
      ,loc: _loc
      ,rol: _rol
      ,correo: _correo
      ,dominio: _dom
      ,id_mail: _mai
      ,departamentos: _departamentos
    };
    _solicitud_proc(_params, function(data){
      if(data.status === 'ok'){
        if(data.insertado === true){
          $('#frm-wrapper.solicitud').fadeOut(function(){
            $(this).addClass('oculto');
            $('#frm-result.solicitud #data #msg').html('ID solicitud: '+ data.id);
            $('#frm-result.solicitud').fadeIn(function(){
              $(this).removeClass('oculto');
            });
          });
        }else{
          _solicitud_error_enviar(_btn,data.msg);
          console.log(data);
          $("#tabs-sol-data "+ data.focus).focus();
        }//end if
      }else{
        _solicita_callback_not_ok(data);
      }//end if
    });
  });

  $(document).on('click', '#frm-result.solicitud #btns #btn-salir', function(){
    var nav = $('.frm');
    var id = nav.attr('data-nav');
    nav.fadeOut(300, function(){
      nav.remove();
      $('#'+id).attr('data-open',0).removeClass('active');
      delete nav;
    });
  });
  $(document).on('click', '#frm-result.solicitud #btns #btn-nuevo', function(){
    $('#frm-result.solicitud').fadeOut(function(){
      $(this).addClass('oculto');
      $('#tabs-sol-data .gpo-captura.code i.fa').removeClass('verde fa-check-circle').addClass('fa-angle-left');
      $('#data-employee #employee-photo').attr('src', '/imagenes/no_image_profile.jpg').load();
      $('#tabs-sol-data #_alter_id').val('');
      $('#tabs-sol-data #id_role').val('');
      $('#tabs-sol-data #id_role').attr('data-id','');
      $('#tabs-sol-data #_correo').val('');
      $('#tabs-sol-data #id_email').val('');
      $('#tabs-sol-data #id_email').attr('data-id','');
      $('#tabs-sol-data #cont-deptos-title #txt').html('0 Departamentos Seleccionados');
      var _tar =$('#tabs-sol-data .tarjeta .data');
      _tar.children('#_nombre').val('').attr('data-emp','');
      _tar.children('#_locacion').val('').attr('data-loc','');
      _tar.children('#_departamento').val('').attr('data-dep','');
      _tar.children('#_posicion').val('').attr('data-loc','');
      $('#frm-tabs-head #inp-loca').val('');
      $('#frm-tabs-head #inp-depa').val('');
      $('#frm-locacion .locacion.for-filtra.active .check').addClass('oculto');
      $('#frm-locacion .locacion.for-filtra.oculto').removeClass('oculto');
      $('#frm-locacion .locacion.for-filtra.active').removeClass('active');
      $('#frm-departamento #departamento-rows').html('');
      $('#cont-deptos #options').html('');
      $('#frm-locacion i.fa.fa-check-square-o').removeClass('fa-check-square-o').addClass('fa-square-o')
      $('#frm-wrapper.solicitud').fadeIn(function(){
        $(this).removeClass('oculto');
      });
    });
  });


  $(document).click(function(e){
    if($('#frm-wrapper.solicitud').length > 0 ){
      //console.log(e.target);
      //console.log($(e.target).attr('data-parent'));
      //console.log($(e.target));
      var _target = $(e.target);

      if(_target.is('.capturas-completa .has-options.closed *')){
        //console.log(1);
        var _opened = $('.capturas-completa .has-options.opened');
        var _contenedor = $(_target.attr('data-parent'));
        if(_opened.length===0){
          _contenedor.removeClass('closed').addClass('opened');
          _contenedor.children('.title').children('#txt').children('input.integrado').focus();
          //_contenedor.children('#options').removeClass('oculto');
          _contenedor.children('#options').slideDown(150, function(){
            $(this).removeClass('oculto');
            _check_option(_contenedor);
          });
        }else{
          _opened.removeClass('opened').addClass('closed');
          _opened.children('#options').slideUp(150, function(){
            $(this).addClass('oculto');
            _check_option(_opened, function(){
              _contenedor.removeClass('closed').addClass('opened');
              _contenedor.children('.title').children('#txt').children('input.integrado').focus();
              //_contenedor.children('#options').removeClass('oculto');
              _contenedor.children('#options').slideDown(150, function(){
                $(this).removeClass('oculto');
                _check_option(_contenedor);
              });
            });
          });
        }//end if

        _target = null;
        return false;
      }//end if

      if(!_target.is('.capturas-completa .has-options.opened *')){
        //console.log($(e.target));
        if (_target.is('.option i.fa-trash-o')) return;
        //console.log(2);
        var _contenedor = $('.capturas-completa .has-options.opened');
        _contenedor.removeClass('opened').addClass('closed');
        //_contenedor.children('#options').addClass('oculto');
        _contenedor.children('#options').slideUp(150, function(){
          $(this).addClass('oculto');
          _check_option(_contenedor);
        });
        _target=null;
        return false;
      }//end if
      //console.log(e.target);
      if(_target.is('.capturas-completa .has-options.opened #ico i.fa')){
        //console.log(3);
        var _contenedor = $($(e.target).attr('data-parent'));

        _contenedor.removeClass('opened').addClass('closed');
        //_contenedor.children('#options').addClass('oculto');
        _contenedor.children('#options').slideUp(150, function(){
          $(this).addClass('oculto');
          _check_option(_contenedor);
        });
        _target = null;
        return false;
      }//end if
      if(_target.is('.capturas-completa .has-options.opened #options.select .option')){
        //console.log(3);
        var _contenedor = $($(e.target).attr('data-parent'));
        _contenedor.children('.title').children('#txt').children('input.integrado').val($(e.target).html());
        _contenedor.children('.title').children('#txt').children('input.integrado').attr('data-id',$(e.target).attr('data-id'));
        _contenedor.removeClass('opened').addClass('closed');
        //_contenedor.children('#options').addClass('oculto');
        _contenedor.children('#options').slideUp(150, function(){
          $(this).addClass('oculto');
          _check_option(_contenedor);
        });
        _target = null;
        return false;
      }//end if
    }else{
      return;
    }//end if
  });

  $(document).on('keydown','.has-options input.integrado', function(e){
    var _target = $(this);
    var _parent = _target.attr('data-parent');
    //console.log(e.keyCode);
    if(e.keyCode === 13 || e.keyCode=== 9){
    //  console.log($(_parent).find("#options.select .option:visible").eq(0).html());
      _target.val($(_parent).find("#options.select .option:visible").eq(0).html());
      _check_option($(_parent), function(){
        var _contenedor = $(_parent);
        _contenedor.removeClass('opened').addClass('closed');
        //_contenedor.children('#options').addClass('oculto');
        _contenedor.children('#options').slideUp(150, function(){
          $(this).addClass('oculto');
        });
      });

    }else{
      var _value =  _target.val();
      if (_target.hasClass('toupper')) _value= _value.toUpperCase(); else _value = _value.toLowerCase();

      _target.attr('data-id', '');

      if(_value.length > 0){
        $(_parent).find("#options.select .option:contains(\""+_value+"\")").show();
        $(_parent).find("#options.select .option:not(:contains(\""+_value+"\"))").hide();
      }else{
        $(_parent).find("#options.select .option").show();
      }//end if
    }//end if
  });
  $(document).on('click', '.solicitud.for-filtra', function(){
    var _soli = $(this);
    var _id = _soli.attr('id');
    var _code = _soli.attr('data-_alter_id');
    $('.solicitud.for-filtra.active').removeClass('active');
    _soli.addClass('active');
    $('.capturas-candado > i.fa.fa-lock').addClass('fa-spinner fa-spin').removeClass('fa-lock');

    var _params = {
       id: _id
      ,action: 'get::data::request'
    };
    //console.log(_params);
    _solicitud_proc(_params, function(data){
      if (data.status === 'ok'){
        $('#id_request').val(_id);
        var _tar = $('#tabs-sol-data .tarjeta .data');
        var _edi = $('#tabs-sol-data .capturas-completa');
        //console.log(data.result.msg);
        if(data.result.msg === 'OK'){
          //console.log(data);
          _load_img(data.result.emp, $('#data-employee #employee-photo'));
          $('#tabs-sol-data .gpo-captura.code .le-msg').html('');
          _tar.children('#_nombre').val(data.result.code + ' - ' + data.result.nombre).attr('data-emp',data.result.emp).attr('title','Colaborador: '+ data.post.code + ' - ' + data.result.nombre);
          _tar.children('#_locacion').val(data.result.locacion).attr('data-loc',data.result.loc).attr('title','Locación: '+ data.result.locacion);
          _tar.children('#_departamento').val(data.result.departamento).attr('data-dep',data.result.dep).attr('title','Departamento: '+ data.result.departamento);
          _tar.children('#_posicion').val(data.result.posicion).attr('data-loc',data.result.pos).attr('title','Posición: '+ data.result.posicion);
          //_stu.removeClass('fa-cog fa-spin fa-times-circle fa-angle-left rojo verde').addClass('fa-check-circle verde');
          //$('#tabs-sol-data #id_role').focus();
          //roles
          _edi.find('#cont-roles #txt input.integrado').val(_edi.find('#cont-roles #options #'+data.result.id_role).html())
          _edi.find('#cont-roles #txt input.integrado').attr('data-id', data.result.id_role);

          //correo y dominio
          _edi.find('input#_correo').val( data.result.correo);
          _edi.find('#cont-dominios #txt input.integrado').val(_edi.find('#cont-dominios #options #'+data.result.id_email).html())
          _edi.find('#cont-dominios #txt input.integrado').attr('data-id', data.result.id_email);
          _edi.find('#cont-usuarios #txt input.integrado').attr('data-id', '');
          _edi.find('#cont-usuarios #txt input.integrado').val('');

          $('.capturas-candado.visible').fadeOut(300).removeClass('visible').addClass('oculto', function(){
            $('.capturas-completa.oculto').fadeIn(300).removeClass('oculto');
            $('.capturas-candado > i.fa.fa-spinner').removeClass('fa-spin');
          });



        }else{
          $('#data-employee #employee-photo').attr('src', '/imagenes/no_image_profile.jpg').load();
          _tar.children('#_nombre').val('').attr('data-emp','');;
          _tar.children('#_locacion').val('').attr('data-loc','');
          _tar.children('#_departamento').val('').attr('data-dep','');
          _tar.children('#_posicion').val('').attr('data-loc','');
          $('#tabs-sol-data .gpo-captura.code .le-msg').html(data.result.msg);
          //_stu.removeClass('fa-cog fa-spin fa-check-circle fa-angle-left rojo verde').addClass('fa-times-circle rojo');
          //inp.focus();
        }//end if

      }else{
        _solicita_callback_not_ok(data);
      }//end if
    });
    //alert(_id);
  });

});//End ready document

function _check_option(_target, callback){
  if(_target.children('#options').hasClass('select')){
    var _id = _target.find('input.integrado').attr('data-id');
    var _value = _target.find('input.integrado').val();
    if (_value.length > 0){
      var _real_id = '';
      var _opts = _target.find("#options.select");
      $('.option', _opts).each(function(){
        //console.log($(this).attr('data-id'));
        //console.log($(this).html().toLowerCase());
        //console.log(_value);
        _target.find('input.integrado').attr('data-id','');
        _target.find('input.integrado').val('');
        if($(this).html().toLowerCase() === _value.toLowerCase()){
          _real_id = $(this).attr('data-id');
          _target.find('input.integrado').attr('data-id',_real_id);
          if(_target.find('input.integrado').hasClass('toupper')) _value = _value.toUpperCase();
          _target.find('input.integrado').val(_value);
          return false;
        }//end if

      });
      //_target.find('input.integrado').attr('data-id','');
      //_target.find('input.integrado').val('');
    }else{
      _target.find('input.integrado').attr('data-id','');
      _target.find('input.integrado').val('');
    }//end if

    _target.find("#options.select .option").show();

    if(callback) callback();

  }else return;//end if
}//end function

function _solicitud_error_enviar(_btn,_msg){
  _solicitud_set_error('bg-verde-2','bg-rojo', 'fa-send-o','fa-exclamation-triangle',_msg,_btn, function(){
    _btn.delay(3000).queue( function(next){
      _solicitud_set_error('bg-rojo','bg-verde-2', 'fa-exclamation-triangle','fa-send-o','Enviar',_btn, function(){
        _btn.addClass('noerror');
        delete _btn;
        return false;
      });
      next();
    });
  });
};
function _solicitud_set_error(  rem_class
                              , add_class
                              , rem_icon
                              , add_icon
                              , msg
                              , who
                              , callback){
  who.removeClass('noerror');
  who.removeClass(rem_class).addClass(add_class);
  who.children('i.fa').removeClass(rem_icon).addClass(add_icon);
  who.children('div.msg').html(msg);
  if(callback) {
      delete who;
      callback();
  }else{
    delete who;
    return;
  }//end if
};
function _solicitud_proc(_params, callback){
  var req = $.ajax({
		url: 'administrar/frm.solicitud.proc.php',
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
};
function _solicita_callback_not_ok(data){
  if (data.status == 'login'){
    window.location = data.url;
    //console.log(data);
  }else{
    console.log(data);
		return;
  }// end if
}//end function

function _load_img(_id, where,callback){
	var _url = 'https://asistenciarcd.aicollection.local:444/app/proc.photo?id=';
	_url = _url + _id;
	//console.log(_url);
	var _img = new Image();
	_img.src = _url;
	_img.onload = function(){
		where.attr('src', _url).load();
	}//end function
	_img.onerror = function(){
		_url = 'https://asistenciarcd.aicollection.local:444/imagenes/no_image_profile.jpg';
		where.attr('src', _url).load();
	}//end function
	if (callback) callback();
}//end function
