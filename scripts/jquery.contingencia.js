$(document).ready(function(){
  $(document).on('click', '.frm-contingencia #btn-excel', function(){
    //----------------------------------------------
    //if(_is_not_tarjeta())return false;
    //----------------------------------------------
    var _btn = $(this);
    var _target = _btn.attr('data-target');

    if($("#"+_target).length===0) return false;

    var _filename = _btn.attr('data-filename')+".xlsx";
    excel = new ExcelGen({
        "src_id": _target,
        "show_header": true,
        "format":"xlsx",
        "file_name": _filename
    });
    excel.generate();
    excel = null;
    return false;
  });

  $(document).on('click', '.frm-contingencia #btn-pdf', function(){
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
      loadCSS:[ "css/style.contingencia.css" , "css/main.css", "css/style.has-options.css"]
      ,printDelay:1000
    });
  });
});
