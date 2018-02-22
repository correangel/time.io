$(document).ready(function(){


  $(document).on('click', '#cont-year > #options > .option', function(e){
    var _year = $(this).attr('data-id');
    //alert(_opt.attr('data-id'));
    var _params = {
      action: 'rep-generales::get::periodos'
      ,year: _year
    };
    _rep_procedure(_params, function(data){

      if(data.status == "ok"){
        //console.log(data.status);
        $('#cont-periodo input.integrado').val('');
        $('#cont-periodo input.integrado').attr('data-id','');
        $('#combo-fechas #inp-ini').val('');
        $('#combo-fechas #inp-fin').val('');
        $('#cont-periodo > #options').html(data.html);
      }else{
        _rep_procedure_error(data);
      }//
    });
  });

  $(document).on('click', '#cont-periodo > #options > .option', function(){
    var _per = $(this);
    var _ini = _per.attr('data-ini');
    var _fin = _per.attr('data-fin');

    var _ico = $('#combo-fechas .ico');
    if (_ico.hasClass('active')){
      _ico.parent().find('input').attr('disabled','disabled');
      _ico.parent().find('input').val('');
      _ico.removeClass('active').addClass('unactive');
      _ico.find('i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
    }//end if
    $('#combo-fechas #inp-ini').val(_ini);
    $('#combo-fechas #inp-fin').val(_fin);
  });


  $(document).on('click', '#combo-fechas .ico',function(){
    var _ico = $(this);
    if (_ico.hasClass('unactive')){
      $('#cont-periodo input.integrado').val('');
      $('#cont-periodo input.integrado').attr('data-id','');
      _ico.parent().find('input').removeAttr('disabled');
      _ico.removeClass('unactive').addClass('active');
      _ico.find('i.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
      return false;
    }//end if
    if (_ico.hasClass('active')){
      _ico.parent().find('input').attr('disabled','disabled');
      _ico.parent().find('input').val('');
      _ico.removeClass('active').addClass('unactive');
      _ico.find('i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
      return false;
    }//end if

  });


  $(document).on('click', '#btn-cont > #btn-generar', function(){
    var _btn = $(this);
    if (_btn.hasClass('procesando')) return false;
    _btn.find('i.fa').removeClass('fa-bolt').addClass('fa-spinner fa-pulse');
    _btn.addClass('procesando');
    $('.frm-rep-generales #results-cont').html('');
    //console.log($('input#_alter_id').val());
    if($('input#_alter_id').val() === '' || $('input#_alter_id').val() === undefined) {

      $('input#_alter_id').attr('data-id','*');
      $('input#_alter_id').attr('data-emp','*');
      var _ope = $('.frm-rep-generales').attr('data-ope');
      var _tip = $('#cont-tipo input#id-tipo').attr('data-id');
      var _aus = $('#cont-ausentismo input#id-ausentismo').attr('data-id');
      var _dep = $('#cont-deptos input#id-depto').attr('data-id');
      var _ini = $('#combo-fechas #inp-ini').val();
      var _fin = $('#combo-fechas #inp-fin').val();
      var _emp = $('input#_alter_id').attr('data-id');


      if (_ini === '' || _ini === undefined) _ini = null;
      if (_fin === '' || _fin === undefined) _fin = null;
      if (_aus === '' || _aus === undefined) _aus = '*';
      if (_dep === '' || _dep === undefined) _dep = '*';
      if (_emp === '' || _emp === undefined) _emp = '*';

      var _params = {
        action : 'rep-generales::exec::report'
        ,tip: _tip
        ,ope: _ope
        ,aus: _aus
        ,dep: _dep
        ,emp: _emp
        ,ini: _ini
        ,fin: _fin
      };

      _rep_procedure(_params, function(data){
        if(data.status === 'ok'){

          $('.frm-rep-generales #results-cont').html(data.table);
          $('.frm-rep-generales #status-bar span.count').html(data.count);
          _btn.find('i.fa').removeClass('fa-spinner fa-pulse').addClass('fa-bolt');
          _btn.removeClass('procesando');
        }else{
          $('.frm-rep-generales #results-cont').html('');
          $('.frm-rep-generales #status-bar span.count').html('0');
          _btn.find('i.fa').removeClass('fa-spinner fa-pulse').addClass('fa-bolt');
          _btn.removeClass('procesando');
          _rep_procedure_error(data);
        }//end if

      });
    } else {
      //console.log($('input#_alter_id').val());
      _rep_set_employee_by_enter($('input#_alter_id').val(), function(){
        var _ope = $('.frm-rep-generales').attr('data-ope');
        var _tip = $('#cont-tipo input#id-tipo').attr('data-id');
        var _aus = $('#cont-ausentismo input#id-ausentismo').attr('data-id');
        var _dep = $('#cont-deptos input#id-depto').attr('data-id');
        var _ini = $('#combo-fechas #inp-ini').val();
        var _fin = $('#combo-fechas #inp-fin').val();
        var _emp = $('input#_alter_id').attr('data-id');


        if (_ini === '' || _ini === undefined) _ini = null;
        if (_fin === '' || _fin === undefined) _fin = null;
        if (_aus === '' || _aus === undefined) _aus = '*';
        if (_dep === '' || _dep === undefined) _dep = '*';
        if (_emp === '' || _emp === undefined) _emp = '*';

        var _params = {
          action : 'rep-generales::exec::report'
          ,tip: _tip
          ,ope: _ope
          ,aus: _aus
          ,dep: _dep
          ,emp: _emp
          ,ini: _ini
          ,fin: _fin
        };

        _rep_procedure(_params, function(data){
          //console.log(data);
          if(data.status === 'ok'){

            $('.frm-rep-generales #results-cont').html(data.table);
            $('.frm-rep-generales #status-bar span.count').html(data.count);
            _btn.find('i.fa').removeClass('fa-spinner fa-pulse').addClass('fa-bolt');
            _btn.removeClass('procesando');
          }else{
            $('.frm-rep-generales #results-cont').html('');
            $('.frm-rep-generales #status-bar span.count').html('0');
            _btn.find('i.fa').removeClass('fa-spinner fa-pulse').addClass('fa-bolt');
            _btn.removeClass('procesando');
            _rep_procedure_error(data);
          }//end if

        });
      });
    }//end if

  });
  $(document).on('click', '.frm-rep-generales #btn-excel', function(){
    //----------------------------------------------
    //if(_is_not_tarjeta())return false;
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

  $(document).on('click', '.frm-rep-generales #btn-pdf', function(){
    //----------------------------------------------
    //if(_is_not_tarjeta())return false;
    //----------------------------------------------
    var _btn = $(this);
    var _clon = $('#rep-grid').clone();

    _clon.find('thead tr').css('background-color', 'blue');
    _clon.find('thead tr').css('color', 'white');
    _clon.find('tbody tr').css('background-color', 'white');
    _clon.find('tbody tr').css('color', 'black');
    _clon.printThis({
      importCSS: true,
      importStyle: true,
      loadCSS:[ "css/style.rep-generales.css" , "css/main.css", "css/style.has-options.css"]
      ,printDelay:1000
    });
  });
  $(document).on('click', '.frm-rep-generales button#buscar-emp',function(e){
    //----------------------------------------------
    //if(_is_not_tarjeta())return false;
    //----------------------------------------------
    e.preventDefault();
    if($('div#win-buscar-emp').hasClass('oculto')){
      $('div#win-buscar-emp').removeClass('oculto').addClass('visible');
      $('div#win-buscar-emp input#inp-filter').val('');
      $('div#win-buscar-emp input#inp-filter').focus();
      if($('div#win-buscar-emp #rows-body .row').lenght > 0){
        return false;
      };
      var _dep = $('#cont-deptos input#id-depto').attr('data-id');
      var _ope = $('.frm-rep-generales').attr('data-ope');

      if (_dep === '' || _dep === undefined) _dep = '*';
      var _params = {action:'get::employees'
                    ,ope: _ope
                    ,dep: _dep};

      _rep_procedure(_params, function(data){
        if (data.status==='ok') {
          $('div#win-buscar-emp input#inp-filter').removeAttr('disabled');
          $('div#win-buscar-emp input#inp-filter').focus();
          $('div#win-buscar-emp #win-rows-body').html(data.html);
          $('div#win-buscar-emp .tar-cargando.visible').removeClass('visible').addClass('oculto');
          $('div#win-buscar-emp #win-rows-body').removeClass('oculto').addClass('visible');
        } else _rep_procedure_error(data);
      });
      return false;
    }//end if
    if($('div#win-buscar-emp').hasClass('visible')){
      $('div#win-buscar-emp').removeClass('visible').addClass('oculto');
      return false;
    }//end if
  });

  $(document).on('click', '.frm-rep-generales #btn-close', function(e){
    //----------------------------------------------
    //if(_is_not_tarjeta())return false;
    //----------------------------------------------
    e.preventDefault();
    var _btn = $(this);
    $(_btn.attr('data-target')).removeClass('visible').addClass('oculto');
    $('div#win-buscar-emp #win-rows-body .for-filtra.oculto').removeClass('oculto');
    $('div#win-buscar-emp input#inp-filter').val('');
  });

  $(document).on('keyup', '#win-buscar-emp #inp-filter', function(e){
    //----------------------------------------------
    //if(_is_not_tarjeta())return false;
    //----------------------------------------------

    var _inp = $(this);
    switch (e.which) {
      case 13:
        return false;
        break;
      default:
        _rep_filtra_employee(_inp);
        break;
    }//end switch
  });

  $(document).on('click', '#win-rows-body .row', function(e){
    //----------------------------------------------
    //if(_is_not_tarjeta())return false;
    //----------------------------------------------
    e.preventDefault();
    var _row = $(this);
    _rep_set_employee(_row);
    //$('.frm-rep-generales #tarjeta-data').fadeOut(300).removeClass('visible').addClass('oculto');
  });
  $(document).on('click', '.frm-rep-generales #cont-tipo #options .option', function(){
    var _id = $(this).attr('data-id');
    if(_id === 'rep-ausentismos'){
      $('.gpo.ausentismos.oculto').removeClass('oculto').addClass('visible');
    }else{
      $('.gpo.ausentismos.visible').removeClass('visible').addClass('oculto');
    }//end if
  });

//end ready document
});
function _rep_set_employee(_row){

    var data_alter = _row.attr('data-alter');
    var data_name = _row.attr('data-name');
    var _id = _row.attr('id');
    $('.frm-rep-generales input#_alter_id').val(data_alter);
    $('.frm-rep-generales input#_alter_id').attr('data-id', _id);
    $('.frm-rep-generales input#_alter_id').attr('data-emp', _id);
    $('div#win-buscar-emp #win-rows-body .for-filtra.oculto').removeClass('oculto');
    $('div#win-buscar-emp input#inp-filter').val('');
    $('div#win-buscar-emp').removeClass('visible').addClass('oculto');
};
function _rep_filtra_employee(_inp){
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

function _rep_procedure( _params, _callback) {
  var _req = $.ajax({
    url: '/app/reportes/frm.generales.proc.php',
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
function _rep_procedure_error(data){
  if (data.status == 'login'){
    window.location = data.url;
    //console.log(data);
  }else{
    console.log(data);
		return;
  }// end if
}//end function

function _rep_set_employee_by_enter(_alter, _callback){
  var _ope = $('.frm-rep-generales').attr('data-ope');
  var _params = {action:'get::employee::by::enter'
                ,ope:_ope
                ,alter:_alter};
  _rep_procedure(_params, function(data){
    //console.log(data);
    if (data.status==='ok' && data.result===1 ) {
      $('.frm-rep-generales input#_alter_id').val(data.row._alter_id);
      $('.frm-rep-generales input#_alter_id').attr('data-id', data.row.employee_id);
      $('.frm-rep-generales input#_alter_id').attr('data-emp', data.row.employee_id);
      $('div#win-buscar-emp #win-rows-body .for-filtra.oculto').removeClass('oculto');
      $('div#win-buscar-emp input#inp-filter').val('');
      $('div#win-buscar-emp').removeClass('visible').addClass('oculto');
      if (_callback) _callback();
    }else{if(data.status==='error' && data.result===0 ){

      $('.frm-rep-generales input#_alter_id').removeClass('correct').addClass('error');
      $('.frm-rep-generales input#_alter_id').val(data.msg);
      if (_callback) _callback();
    }else _procedure_error(data);}
  });


}//end if
