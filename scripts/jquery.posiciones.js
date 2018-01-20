$(document).ready(function(){

  $(document).on('click','.vtab-pos .val', function(){
    //console.log(1);
    //--------------------------------------------------------------
    var _act = $(this);
    var _col = _act.data('col');
    if (_col !== 'EM'){
      var _val = 0;
      var _che = _act.children('i.fa').first();
      var _nav = $('.frm').data('nav');
      var _pos = _act.parent().attr('id');

      if (_che.hasClass('fa-square-o')) _val = 1; else _val = 0;
      //--------------------------------------------------------------
      var _params = {action: 'checkbox'
                    ,nav: _nav
                    ,pos: _pos
                    ,col: _col
                    ,val: _val};
      _jpos_send_post_to_proc(_params, function(data){
        if(data.status==='ok'){
          if (_val===1)
            _che.removeClass('fa-square-o').addClass('fa-check-square-o');
          else
            _che.removeClass('fa-check-square-o').addClass('fa-square-o');
        }else{
          _jpos_callback_error(data);
        }//end if
        _che = undefined;
        _act = undefined;
      });
    }else{
      return;
    }//end if
  });

  $(document).on('keyup', '#frm-tabs-head .filtra', function(e){
    //console.log(e.which);
    var _inp = $(this);
    var _str = _inp.val();
    //console.log(_str.length);
    var _sou = _inp.data('source');
    if (_str.length == 0) {
      $(_sou + ' .for-filtra.oculto').removeClass('oculto');
      return;
    }//end if


    $(_sou + ' .for-filtra').addClass('oculto');
    $(_sou + ' .for-filtra.oculto').each(function(){
      var _item = $(this);
      var _code = _item.data('code');
      var _name = _item.data('name');
      if (!$.isNumeric(_code)) _code = _code.toLowerCase();
      if (!$.isNumeric(_name)) _name = _name.toLowerCase();
      if (!$.isNumeric(_str)) _str = _str.toLowerCase();
      if(_find_str(_code,"*"+_str+"*") || _find_str(_name,"*"+_str+"*")){
        _item.removeClass('oculto').prependTo(_sou);
      }//end if
    });
  });


  $(document).on('click', '#tabs-dep-rows .vtab-dep', function(){
    $('.filtra').val('');
    $('#frm-vert-tabs-dep .for-filtra').removeClass('oculto');
    var _act = $(this);
    var _dep = _act.attr('id');
    $('#tabs-dep-rows .vtab-dep.active').removeClass('active');
    _act.addClass('active');
    $('#frm-vert-tabs-pos #tabs-pos-rows').fadeOut(300, function(){
      $('#frm-vert-tabs-pos #tabs-pos-rows').addClass('oculto');
      $('#frm-vert-tabs-pos .cargando.oculto').fadeIn(300, function(){
        $('#frm-vert-tabs-pos .cargando.oculto').removeClass('oculto');
        var _nav = $('.frm').data('nav');
        var _params = {action: 'departamento->posiciones'
                      ,nav: _nav
                      ,dep: _dep};
        _jpos_send_post_to_proc(_params, function(data){
          if(data.status === 'ok'){
            //console.log(data);
            $('#frm-vert-tabs-pos #tabs-pos-rows.oculto').html('');
            $('#frm-vert-tabs-pos #tabs-pos-rows.oculto').html(data.html);
            $('#frm-vert-tabs-pos .cargando').fadeOut(300, function(){
              $('#frm-vert-tabs-pos .cargando').addClass('oculto');
              $('#frm-vert-tabs-pos #tabs-pos-rows.oculto').fadeIn(300,function(){
                $('#frm-vert-tabs-pos #tabs-pos-rows.oculto').removeClass('oculto');
              });
            });
          }else{
            _jpos_callback_error(data);
            $('#frm-vert-tabs-pos #tabs-pos-rows.oculto').html('');
            $('#frm-vert-tabs-pos #tabs-pos-rows.oculto').html(data.error_html);
            $('#frm-vert-tabs-pos .cargando').fadeOut(300, function(){
              $('#frm-vert-tabs-pos .cargando').addClass('oculto');
              $('#frm-vert-tabs-pos #tabs-pos-rows.oculto').fadeIn(300,function(){
                $('#frm-vert-tabs-pos #tabs-pos-rows.oculto').removeClass('oculto');
              });
            });
          }//end if
        });
      });
    });
  });

  $(document).on('click', '#frm-vert-tabs .vtab', function(){
    $('.filtra').val('');
    $('#frm-vert-tabs-pos #tabs-pos-rows').html('');
    $('#frm-vert-tabs .for-filtra').removeClass('oculto');
    var _act = $(this);
    var _loc = _act.attr('id');
    $('#frm-vert-tabs .vtab.active').removeClass('active');
    _act.addClass('active');
    $('#frm-vert-tabs-dep #tabs-dep-rows').fadeOut(300, function(){
      $('#frm-vert-tabs-dep #tabs-dep-rows').addClass('oculto');
      $('#frm-vert-tabs-dep .cargando.oculto').fadeIn(300, function(){
        $('#frm-vert-tabs-dep .cargando.oculto').removeClass('oculto');
        var _nav = $('.frm').data('nav');
        var _params = {action: 'locacion->departamentos'
                      ,nav: _nav
                      ,loc: _loc};
        _jpos_send_post_to_proc(_params, function(data){
          if(data.status === 'ok'){
            //console.log(data);
            $('#frm-vert-tabs-dep #tabs-dep-rows.oculto').html('');
            $('#frm-vert-tabs-dep #tabs-dep-rows.oculto').html(data.html);
            $('#frm-vert-tabs-dep .cargando').fadeOut(300, function(){
              $('#frm-vert-tabs-dep .cargando').addClass('oculto');
              $('#frm-vert-tabs-dep #tabs-dep-rows.oculto').fadeIn(300,function(){
                $('#frm-vert-tabs-dep #tabs-dep-rows.oculto').removeClass('oculto');
              });
            });
          }else{
            _jpos_callback_error(data);
            $('#frm-vert-tabs-dep #tabs-dep-rows.oculto').html('');
            $('#frm-vert-tabs-dep #tabs-dep-rows.oculto').html(data.error_html);
            $('#frm-vert-tabs-dep .cargando').fadeOut(300, function(){
              $('#frm-vert-tabs-dep .cargando').addClass('oculto');
              $('#frm-vert-tabs-dep #tabs-dep-rows.oculto').fadeIn(300,function(){
                $('#frm-vert-tabs-dep #tabs-dep-rows.oculto').removeClass('oculto');
              });
            });
          }//end if
        });
      });
    });
  });

});
//--------------------------------------------------------
function _jpos_send_post_to_proc(_params, callback){
  var _request = $.ajax({
    url: 'catalogos/frm.posiciones.proc.php',
    type: 'POST',
    dataType: 'json',
    data: _params
  });
  _request.done(function(data) {
    //console.log(data);
    if (callback) callback(data); else console.log(data);
  });
  _request.fail(function(data) {
    if (callback) callback(data); else console.log(data);
  });
}//end function
function _find_str(str, rule) {
  return new RegExp("^" + rule.split("*").join(".*") + "$").test(str);
}//end func
function _jpos_callback_error(data){
  if(data){
    switch(data.status){
      case 'login':
        window.location = data.url;
        break;
      case'permisos':
        alert(data.msg);
        break;
      case 'error':
        console.log(data);
        break;
      default:
        console.log(data);
        break;
    }//end SW
  }else{
    return;
  }

}//end function
