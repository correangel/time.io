$(document).ready(function(){
  $(document).on('click', '.frm-tarjeta button#btn-run.unactive',function(e){
    //----------------------------------------------
    if(_is_not_tarjeta())return false;
    //----------------------------------------------
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    var _inp = $('.frm-tarjeta #select-opt #_alter_id')
    var _alter = _inp.val();
    //console.log(_alter);
    if (_alter.length == 0 ) return false;
    _set_employee_by_enter(_alter, function(){

      var _btn = $(this);
      if(_btn.hasClass('active'))return false;
      if ($('.frm-tarjeta #tarjeta-data').hasClass('visible')){
        $('.frm-tarjeta #tarjeta-data').fadeOut(300).removeClass('visible').addClass('oculto');
      }//end if
      //----------------------------------------------
      if($('.frm-tarjeta #select-opt #id-per').val() === '') {
        $('.frm-tarjeta #select-opt #id-per').focus();
        $('.frm-tarjeta #select-opt #id-per').trigger('click');
        _btn = null;
        return false;
      }//end if
      if(!$('.frm-tarjeta #select-opt #_alter_id').hasClass('correct')) {
        $('.frm-tarjeta #select-opt #_alter_id').focus();
        _btn = null;
        return false;
      }//end if

      _btn.removeClass('unactive').addClass('active');
      _btn.children('i.fa').removeClass('fa-bolt').addClass(' fa-cog fa-spin');

      var _emp = $('.frm-tarjeta #select-opt #_alter_id').attr('data-id');
      var _per = $('.frm-tarjeta #select-opt #id-per').attr('data-id');
      if ($('.frm-tarjeta #tarjeta-data').hasClass('oculto')){
        var _params = {action:'get::data::asistencia'
                      ,emp: _emp
                      ,per: _per};
        _procedure(_params, function(data){
          if(data.status === 'ok'){
            $('.frm-tarjeta #tarjeta-data').fadeIn(300).removeClass('oculto').addClass('visible');
            _btn.removeClass('active').addClass('unactive');
            _btn.children('i.fa').removeClass('fa-cog fa-spin').addClass('fa-bolt');

            var _target_head = $('.frm-tarjeta #tarjeta-data-rows #data-rows-head');
            var _target_body = $('.frm-tarjeta #tarjeta-data-rows #data-rows-body');
            _target_head.children('#tab-asis-head').html(data.asis_head);
            _target_head.children('#tab-chec-head').html(data.chec_head);
            _target_head.children('#tab-jorn-head').html(data.jorn_head);
            _target_head.children('#tab-hora-head').html(data.hora_head);

            _target_body.children('#tab-asis-rows').html(data.asis_rows);
            _target_body.children('#tab-chec-rows').html(data.chec_rows);
            _target_body.children('#tab-jorn-rows').html(data.jorn_rows);
            _target_body.children('#tab-hora-rows').html(data.hora_rows);
            //console.log(data);
            $('#tarjeta-data-tabs #tab-asistencia #btn-exc').attr('data-filename', data.emp_data._periodo +'-'+ data.emp_data._alter_id +'-Asistencia');
            $('#tarjeta-data-tabs #tab-checadas #btn-exc').attr('data-filename', data.emp_data._periodo +'-'+ data.emp_data._alter_id +'-Checadas');
            $('#tarjeta-data-tabs #tab-jornadas #btn-exc').attr('data-filename', data.emp_data._periodo +'-'+ data.emp_data._alter_id +'-Jornadas');
            $('#tarjeta-data-tabs #tab-horas #btn-exc').attr('data-filename', data.emp_data._periodo +'-'+ data.emp_data._alter_id +'-Horas');
            $('#tarjeta-data-tabs #tab-asistencia #btn-pdf').attr('data-filename', data.emp_data._periodo +'-'+ data.emp_data._alter_id +'-Asistencia');
            $('#tarjeta-data-tabs #tab-checadas #btn-pdf').attr('data-filename', data.emp_data._periodo +'-'+ data.emp_data._alter_id +'-Checadas');
            $('#tarjeta-data-tabs #tab-jornadas #btn-pdf').attr('data-filename', data.emp_data._periodo +'-'+ data.emp_data._alter_id +'-Jornadas');
            $('#tarjeta-data-tabs #tab-horas #btn-pdf').attr('data-filename', data.emp_data._periodo +'-'+ data.emp_data._alter_id +'-Horas');

            $('.frm-tarjeta #tablas-ocultas-for-docs').html(data.tablas);
            _btn = null;
            return false;
          }else {
            _procedure_error(data);
            alert(data.msg);
            _btn.removeClass('active').addClass('unactive');
            _btn.children('i.fa').removeClass('fa-cog fa-spin').addClass('fa-bolt');
          }//end if
        });
      }//end if
    });
  });
  $(document).on('click', '.frm-tarjeta button#buscar-emp',function(e){
    //----------------------------------------------
    if(_is_not_tarjeta())return false;
    //----------------------------------------------
    e.preventDefault();
    if($('div#win-buscar-emp').hasClass('oculto')){
      $('div#win-buscar-emp').removeClass('oculto').addClass('visible');
      $('div#win-buscar-emp input#inp-filter').val('');
      $('div#win-buscar-emp input#inp-filter').focus();
      if($('div#win-buscar-emp #rows-body .row').lenght > 0){
        return false;
      };
      var _ope = $('.frm.frm-tarjeta').attr('data-ope');
      var _params = {action:'get::employees'
                    ,ope:_ope};

      _procedure(_params, function(data){
        if (data.status==='ok') {
          $('div#win-buscar-emp input#inp-filter').removeAttr('disabled');
          $('div#win-buscar-emp input#inp-filter').focus();
          $('div#win-buscar-emp #win-rows-body').html(data.html);
          $('div#win-buscar-emp .tar-cargando.visible').removeClass('visible').addClass('oculto');
          $('div#win-buscar-emp #win-rows-body').removeClass('oculto').addClass('visible');
        } else _procedure_error(data);
      });
      return false;
    }//end if
    if($('div#win-buscar-emp').hasClass('visible')){
      $('div#win-buscar-emp').removeClass('visible').addClass('oculto');
      return false;
    }//end if
  });
  //alert(1;

  $(document).on('click', '.frm-tarjeta #btn-close', function(e){
    //----------------------------------------------
    if(_is_not_tarjeta())return false;
    //----------------------------------------------
    e.preventDefault();
    var _btn = $(this);
    $(_btn.attr('data-target')).removeClass('visible').addClass('oculto');
    $('div#win-buscar-emp #win-rows-body .for-filtra.oculto').removeClass('oculto');
    $('div#win-buscar-emp input#inp-filter').val('');
  });

  $(document).on('keyup', '#win-buscar-emp #inp-filter', function(e){
    //----------------------------------------------
    if(_is_not_tarjeta())return false;
    //----------------------------------------------
    //console.log(e.which);
    var _inp = $(this);
    switch (e.which) {
      case 13:

        break;
      default:
        _filtra_employee(_inp);
        break;
    }//end switch
  });

  $(document).on('click', '#win-rows-body .row', function(e){
    //----------------------------------------------
    if(_is_not_tarjeta())return false;
    //----------------------------------------------
    e.preventDefault();
    var _row = $(this);
    _set_employee(_row);
  });

  $(document).on('keyup', '.frm-tarjeta #select-opt #_alter_id', function(e){
    //----------------------------------------------
    if(_is_not_tarjeta())return false;
    //----------------------------------------------
    var _inp = $(this);
    var _alter = _inp.val();
    //console.log(_alter);
    if (_alter.length == 0 ) return false;

    switch (e.which) {
      case 13:
        _set_employee_by_enter(_alter);
        break;
      default:
        if(_inp.hasClass('error')){
          _inp.removeClass('error').val('');
          return false;
        }//end if
        if(_inp.hasClass('correct')){
          _inp.removeClass('correct');
          return false;
        }//end if
      }//end switch
    });

    $(document).on('focusout', '.frm-tarjeta #select-opt #_alter_id', function(e){
      //----------------------------------------------
      if(_is_not_tarjeta())return false;
      //----------------------------------------------
      var _inp = $(this);
      var _alter = _inp.val();
      //console.log(_alter);
      if (_alter.length == 0 ) return false;
      _set_employee_by_enter(_alter);

      });

    $(document).on('click', '.frm-tarjeta #tarjeta-data-tabs .eti-tab.unactive', function(){
      //----------------------------------------------
      if(_is_not_tarjeta())return false;
      //----------------------------------------------
      var _tab = $(this);
      $('.frm-tarjeta #tarjeta-data-tabs .eti-tab.active').removeClass('active').addClass('unactive');
      _tab.removeClass('unactive ').addClass('active');

      $('.frm-tarjeta #data-rows-head .eti-tab-head.visible').removeClass('visible').addClass('oculto');
      $('.frm-tarjeta #data-rows-body .eti-tab-rows.visible').removeClass('visible').addClass('oculto');
      $(_tab.attr('data-target-rows')).addClass('visible').removeClass('oculto');
      $(_tab.attr('data-target-head')).addClass('visible').removeClass('oculto');

    });

    $(document).on('click', '.frm-tarjeta #tarjeta-data-tabs #btn-exc', function(){
      //----------------------------------------------
      if(_is_not_tarjeta())return false;
      //----------------------------------------------
      var _btn = $(this);
      var _target = _btn.attr('data-target');

      if($("#"+_target).length===0) return false;

      var _filename = _btn.attr('data-filename');
      excel = new ExcelGen({
          "src_id": _target,
          "show_header": true
      });
      excel.generate(_filename);
      excel = null;
      return false;
    });
    $(document).on('click', '.frm-tarjeta #tarjeta-data-tabs #btn-pdf', function(){
      //----------------------------------------------
      if(_is_not_tarjeta())return false;
      //----------------------------------------------
      var _btn = $(this);
      var _target = _btn.attr('data-target');

      if($("#"+_target).length===0) return false;

      var _filename = _btn.attr('data-filename');
      _generate_pdf($("#"+_target),_filename)
    });
});
function _set_employee_by_enter(_alter, _callback){
  var _ope = $('.frm.frm-tarjeta').attr('data-ope');
  var _params = {action:'get::employee::by::enter'
                ,ope:_ope
                ,alter:_alter};
  _procedure(_params, function(data){
    if (data.status==='ok' && data.result===1 ) {

      $('.frm-tarjeta #select-opt #_alter_id').val(data.row._alter_id);
      $('.frm-tarjeta #select-opt #_alter_id').attr('data-id', data.row.employee_id);
      $('.frm-tarjeta #select-datos #_name').val("Nombre: " + data.row._nombre);
      $('.frm-tarjeta #select-datos #_ingreso').val("Ingreso: "+data.row._hire_date);
      $('.frm-tarjeta #select-datos #_locacion').val("Locación: "+data.row._locacion_code);
      $('.frm-tarjeta #select-datos #_departamento').val("Dep: "+data.row._departamento_code + " - " + data.row._departamento_name);
      $('.frm-tarjeta #select-datos #_posicion').val("Pos: "+data.row._posicion_code +  " - " + data.row._posicion_name);
      $('.frm-tarjeta #select-datos #_clase').val("Clase: "+data.row._clase);
      $('.frm-tarjeta #select-opt #_alter_id').removeClass('error').addClass('correct');
      if (_callback) _callback();
    }else{if(data.status==='error' && data.result===0 ){
      $('.frm-tarjeta #select-opt #_alter_id').removeClass('correct').addClass('error');
      $('.frm-tarjeta #select-opt #_alter_id').val(data.msg);
      if (_callback) _callback();
    }else _procedure_error(data);}
  });


}//end if
function _set_employee(_row){
  var data_alter = _row.attr('data-alter');
  var data_name = _row.attr('data-name');
  var data_departamento = _row.attr('data-departamento');
  var data_departamento_code = _row.attr('data-departamento-code');
  var data_posicion = _row.attr('data-posicion');
  var data_posicion_code = _row.attr('data-posicion-code');
  var data_locacion_code = _row.attr('data-locacion-code');
  var data_clase = _row.attr('data-clase');
  var data_hire = _row.attr('data-hire');
  var _id = _row.attr('id');
  $('.frm-tarjeta #select-opt #_alter_id').val(data_alter);
  $('.frm-tarjeta #select-opt #_alter_id').attr('data-id', _id);
  $('.frm-tarjeta #select-datos #_name').val("Nombre: " + data_name);
  $('.frm-tarjeta #select-datos #_ingreso').val("Ingreso: "+data_hire);
  $('.frm-tarjeta #select-datos #_locacion').val("Locación: "+data_locacion_code);
  $('.frm-tarjeta #select-datos #_departamento').val("Dep: "+data_departamento_code + " - " + data_departamento);
  $('.frm-tarjeta #select-datos #_posicion').val("Pos: "+data_posicion_code +  " - " + data_posicion);
  $('.frm-tarjeta #select-datos #_clase').val("Clase: "+data_clase);
  $('div#win-buscar-emp #win-rows-body .for-filtra.oculto').removeClass('oculto');
  $('div#win-buscar-emp input#inp-filter').val('');
  $('div#win-buscar-emp').removeClass('visible').addClass('oculto');
  $('.frm-tarjeta #select-opt #_alter_id').removeClass('error').addClass('correct');
};

function _procedure( _params, _callback) {
  var _req = $.ajax({
    url: '/app/frm.tarjeta.proc.php',
    type: 'POST',
    dataType: 'json',
    data: _params
  });
  _req.done(function(data) {
    if (_callback) {
      _callback(data)
    }else return false;
  });
  _req.fail(function(data) {
    if (_callback) {
      _callback(data)
    }else console.log(data);
  });
}//end function
function _procedure_error(data){
  if (data.status == 'login'){
    window.location = data.url;
    //console.log(data);
  }else{
    console.log(data);
		return;
  }// end if
}//end function

function _filtra_employee(_inp){
  //console.log(1);
  //var _inp = $(this);
  var _str = _inp.val();
  var _sou = _inp.attr('data-source');
  //console.log(_str.length);
  if (_str.length == 0) {
    $(_sou + ' .for-filtra.oculto').removeClass('oculto');
    _inp = null;
    return false;
  }//end if
  $(_sou + ' .for-filtra').addClass('oculto');
  $(_sou + ' .for-filtra.oculto').each(function(){
    var _item = $(this);
    var _departamento = _item.attr('data-departamento');
    var _departamento_code = _item.attr('data-departamento-code');
    var _posicion = _item.attr('data-posicion');
    var _posicion_code = _item.attr('data-posicion-code');
    var _name = _item.attr('data-name');
    var _alte = _item.attr('data-alter');
    //if (!$.isNumeric(_code)) _code = _code.toLowerCase();
    if (!$.isNumeric(_name)) _name = _name.toLowerCase();
    if (!$.isNumeric(_str)) _str = _str.toLowerCase();
    if (!$.isNumeric(_departamento)) _departamento = _departamento.toLowerCase();
    if (!$.isNumeric(_departamento_code)) _departamento_code = _departamento_code.toLowerCase();
    if (!$.isNumeric(_posicion)) _posicion = _posicion.toLowerCase();
    if (!$.isNumeric(_posicion_code)) _posicion_code = _posicion_code.toLowerCase();
    if(//_find_str(_code,"*"+_str+"*")
        _find_str(_name,"*"+_str+"*")
        || _find_str(_alte,"*"+_str+"*")
        || _find_str(_departamento,"*"+_str+"*")
        || _find_str(_departamento_code,"*"+_str+"*")
        || _find_str(_posicion,"*"+_str+"*")
        || _find_str(_posicion_code,"*"+_str+"*")){
      _item.removeClass('oculto').prependTo(_sou);
    }//end if
  });
}//end if


function _is_not_tarjeta(){
  if ($('.frm.frm-tarjeta').length === 0) return true; else return false;
};


function _generate_pdf(_source,_filename) {
    var pdf = new jsPDF('p', 'pt', 'letter');
    _source = _source[0];
    specialElementHandlers = {
        // element with id of "bypass" - jQuery style selector
        '#bypassme': function (element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    };
    margins = {
        top: 80,
        bottom: 60,
        left: 10,
        width: 700
    };
    // all coords and widths are in jsPDF instance's declared units
    // 'inches' in this case
    pdf.fromHTML(
    _source, // HTML string or DOM elem ref.
    margins.left, // x coord
    margins.top, { // y coord
        'width': margins.width, // max width of content on PDF
        'elementHandlers': specialElementHandlers
    },

    function (dispose) {
        // dispose: object with X, Y of the last line add to the PDF
        //          this allow the insertion of new lines after html
        pdf.save(_filename+'.pdf');
    }, margins);
}
