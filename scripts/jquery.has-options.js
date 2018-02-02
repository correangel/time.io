$(document).click(function(e){
  if($('.frm.add-has-options').length > 0 ){
    //console.log(e.target);
    //console.log($(e.target).attr('data-parent'));
    //console.log($(e.target));
    var _target = $(e.target);

    if(_target.is('.has-options.closed *')){
      //console.log(1);
      var _opened = $('.has-options.opened');
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

    if(!_target.is('.has-options.opened *')){
      //console.log($(e.target));
      if (_target.is('.option i.fa-trash-o')) return;
      //console.log(2);
      var _contenedor = $('.has-options.opened');
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
    if(_target.is('.has-options.opened #ico i.fa')){
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
    if(_target.is('.has-options.opened #options.select .option')){
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
