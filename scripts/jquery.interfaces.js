$(document).ready( function() {
  _run_interfaces_by_hits();
});

var _run_interfaces_by_hits = function(){
  //console.log(1);
  var _req = $.ajax({
    url: '/app/interfaces/ifc.by_hits.php',
		type: 'POST',
		dataType: 'json',
		data: {ifc:'by_hits'}
  });
  _req.done(function(data){
    if(data.count > 0 ){
      $('#ifc-container > .data').html('');
      for (var key in data.interfaces){
        //console.log(data.interfaces[key].div);
        $('#ifc-container > .data').append($(data.interfaces[key].div));
      }//end for
    }else{
      $('#ifc-container > .data').html('');
      $('#ifc-container > .data').append($(data.div));
    }//end if
  });
  _req.fail(function(data){
    console.log(data);
  });
};
