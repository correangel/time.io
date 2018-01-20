<?php
  header('Content-type: application/json');

  include_once $_SERVER['DOCUMENT_ROOT']."/login/class.login.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.creates.php";
	$com = new com_mssql();
	$cnn = $com->_conectar_win(HOST,DATA);

	$lifetime = $com->_get_param($cnn,  'session_lifetime'); // in minutes

	//Login
	$clogin = new _login();
	$clogin->iniciar_sesion('TimeIO', $lifetime);

	//echo $_POST['id_nav'];
	if($clogin->_logeado()){
    $ope = $_SESSION['id_operator'];
    #-----------------------------------------------------------------------------------------------
    if(isset($_POST['nav'],$_POST['action'], $_POST['loc'])&&$_POST['action']=='locacion->departamentos'){
      $loc = $_POST['loc'];
      $html='';
      $query="exec cat.proc_get_list_elements
              	@table = 'cat.vw_estructura with(nolock) ',
              	@columns = '  locacion_id
                            , _locacion_code
                            , _locacion_name
                            , departamento_id
                            , _departamento_code
                            , _departamento_name',
              	@where = ?,
              	@active= null,
              	@distinct= 1,
              	@verbose = 0,
              	@orderby = '_departamento_name asc'";
      $where_locacion_id = "locacion_id = '$loc'";
      $params = array(array(&$where_locacion_id,SQLSRV_PARAM_IN));
      $stmt = $com->_create_stmt($cnn,$query,$params);
      if($stmt){
        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
          $html.="<div class='fs vtab-dep for-filtra waves-effect elli' data-name='".$row['_departamento_name']."' data-code='".$row['_departamento_code']."' id='".$row['departamento_id']."'>".$row['_departamento_code']." - ".$row['_departamento_name']."</div>";

        }//end while
        $resp['status'] = 'ok';
        $resp['html'] = $html;
        $resp['msg'] = 'Usar html...';
      }else{
        $resp['stmt'] = $stmt;
        $resp['status'] = 'error';
        $resp['msg'] = 'Error en stmt';
        $resp['error_html'] = "<div class='fn bug rojo'><i class='fa fa-2x fa-bug'></i></div>";
        $resp['error'] = sqlsrv_errors();
        $resp['post'] = $_POST;
      }//end if
  #-----------------------------------------------------------------------------------------------
}elseif(isset($_POST['nav'],$_POST['action'], $_POST['dep'])&&$_POST['action']=='departamento->posiciones'){
      $dep = $_POST['dep'];
      $html='';
      $query="exec cat.proc_get_list_elements
              	@table = 'cat.vw_estructura with(nolock) ',
              	@columns = '   departamento_id
                            , _departamento_code
                            , _departamento_name
                            ,  posicion_id
                            , _posicion_code
                            , _posicion_name
                            , _festivos
                            , _prima_dominical
                            , _horas_extras
                            , _horas_nocturnas',
              	@where = ?,
              	@active= null,
              	@distinct= 1,
              	@verbose = 0,
              	@orderby = '_posicion_name asc'";
      $where_locacion_id = "departamento_id = '$dep'";
      $params = array(array(&$where_locacion_id,SQLSRV_PARAM_IN));
      $stmt = $com->_create_stmt($cnn,$query,$params);
      if($stmt){
        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
          $html.="<div class='vtab-pos for-filtra waves-effect' data-name='".$row['_posicion_name']."' data-code='".$row['_posicion_code']."' id='".$row['posicion_id']."'>
                    <div class='fs enlinea txt elli floL'>".$row['_posicion_code']." - ".$row['_posicion_name']."</div>
                    <div id='val-fe' data-col='FE' class='val fs enlinea cur-poi waves-effect'><i class='fa fa-1x ".($row['_festivos']==1?'fa-check-square-o':'fa-square-o')."'></i></div>
										<div id='val-pd' data-col='PD' class='val fs enlinea cur-poi waves-effect'><i class='fa fa-1x ".($row['_prima_dominical']==1?'fa-check-square-o':'fa-square-o')."'></i></div>
										<div id='val-he' data-col='HE' class='val fs enlinea cur-poi waves-effect'><i class='fa fa-1x ".($row['_horas_extras']==1?'fa-check-square-o':'fa-square-o')."'></i></div>
										<div id='val-hn' data-col='HN' class='val fs enlinea cur-poi waves-effect'><i class='fa fa-1x ".($row['_horas_nocturnas']==1?'fa-check-square-o':'fa-square-o')."'></i></div>
										<div id='val-em' data-col='EM' class='val fs enlinea cur-poi waves-effect'><i class='fa fa-1x fa-users'></i></div>
                  </div>";

        }//end while
        $resp['status'] = 'ok';
        $resp['html'] = $html;
        $resp['msg'] = 'Usar html...';
      }else{
        $resp['stmt'] = $stmt;
        $resp['status'] = 'error';
        $resp['msg'] = 'Error en stmt';
        $resp['error_html'] = "<div class='fn bug rojo'><i class='fa fa-2x fa-bug'></i></div>";
        $resp['error'] = sqlsrv_errors();
        $resp['post'] = $_POST;
      }//end if
    }elseif(isset($_POST['nav'], $_POST['pos'], $_POST['col'], $_POST['val'],$_POST['action'])&&$ope!==null && $_POST['action']=='checkbox'){
      $nav = $_POST['nav'];
      $_full = intval($com->_get_permisos_nav($cnn, $nav,$ope, '_full'));
      $_write = intval($com->_get_permisos_nav($cnn, $nav,$ope, '_write'));
      if($_full == 1 || $_write == 1){
        $pos = $_POST['pos'];
        $val = $_POST['val'];
        switch ($_POST['col']){
          case 'FE':
            $col ="_festivos";
            break;
          case 'PD':
            $col ="_prima_dominical";
            break;
          case 'HE':
            $col ="_horas_extras";
            break;
          case 'HN':
            $col ="_horas_nocturnas";
            break;
          default:
            return;
            break;
        }//end sw
        $query ="execute cat.proc_set_posicion_value
                	 @column = ?
                	,@pos = ?
                	,@val = ?
                	,@do_ope = ?
                	,@log = 1";
        $params = array(&$col,&$pos,&$val,&$ope);
        $resp = $com->_exec_non_query($cnn, $query, $params);
      }else{
        $resp['status'] = 'permisos';
        $resp['msg'] = 'Permisos insuficientes';
        $resp['post'] = $_POST;
      }//end if
    }else{
      $resp['status'] = 'error';
      $resp['msg'] = 'Posteo Incompleto...';
      $resp['post'] = $_POST;
    }//end if
  }else{
    $url = $com->_get_param($cnn, 'raiz');
    $resp['status'] = 'login';
    $resp['url'] = $url;
    $resp['msg'] = 'Sesion Caducada...';
  }//end if

$com->_desconectar($cnn);
echo json_encode($resp);
?>
