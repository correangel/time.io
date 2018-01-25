<?php
  header('Content-type: application/json');

  include_once $_SERVER['DOCUMENT_ROOT']."/login/class.login.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.creates.php";
  include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.ldap.php";

  $com = new com_mssql();
  $cnn = $com->_conectar_win(HOST,DATA);

  $lifetime = $com->_get_param($cnn,  'session_lifetime'); // in minutes

  //Login
  $clogin = new _login();
  $clogin->iniciar_sesion('TimeIO', $lifetime);
  //echo $_POST['id_nav'];
  if($clogin->_logeado()){
    /*$id_navigator = (isset($_POST['id_nav'])?$_POST['id_nav']:$_POST['id_nav_proc']);
    $id_operator = $_SESSION['id_operator'];
    $_full = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_full'));
    $_read = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_read'));
    $_write = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_write'));
    $_special = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_special'));*/
    if(isset($_POST['nav'],$_POST['action'], $_POST['loc'])&&$_POST['action']=='get::departamentos'){
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
          $html.="<div class='fn departamento-item for-filtra waves-effect elli bloque' data-name='".$row['_departamento_name']."' data-code='".$row['_departamento_code']."' id='".$row['departamento_id']."'>
                    <div class='fn check floL enlinea'><i class='fa fa-1x fa-square-o'></i></div>
                    <div class='fs txt floL enlinea'>".$row['_departamento_code']." - ".$row['_departamento_name']."</div>
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
    }elseif(isset($_POST['nav']
                  ,$_POST['action']
                  ,$_POST['ope']
                  ,$_POST['emp']
                  ,$_POST['loc']
                  ,$_POST['rol']
                  ,$_POST['correo']
                  ,$_POST['dominio']
                  ,$_POST['id_mail']
                  ,$_POST['departamentos'])&& $_POST['action'] === 'insert::solicitud'){
      $correo = $_POST['correo'].$_POST['dominio'];
      if($com->_isvalid_email($correo)){
        $ope = $_POST['ope'];
        $emp = $_POST['emp'];
        $loc = $_POST['loc'];
        $rol = $_POST['rol'];
        $mai = $_POST['id_mail'];
        $correo = $_POST['correo'];
        $departamentos = $_POST['departamentos'];
        $resp['status']='ok';
        $resp['insertado']=true;
        $result = 0;
        $msg = "";
        $sql_error = "";
        $id = 0;
        $query=' [adm].[proc_add_operator_request]
                  	 @ope = ?
                  	,@emp = ?
                  	,@loc = ?
                  	,@rol = ?
                  	,@correo = ?
                    ,@id_mail = ?
                  	,@departamentos = ?
                  	,@result = ?
                  	,@msg = ?
                  	,@sql_error = ?
                  	,@id = ?';
        $params = array(array( &$ope,SQLSRV_PARAM_IN)
                        ,array( &$emp,SQLSRV_PARAM_IN)
                        ,array( &$loc,SQLSRV_PARAM_IN)
                        ,array( &$rol,SQLSRV_PARAM_IN)
                        ,array( &$correo,SQLSRV_PARAM_IN)
                        ,array( &$mai,SQLSRV_PARAM_IN)
                        ,array( &$departamentos,SQLSRV_PARAM_IN)
                        ,array( &$result,SQLSRV_PARAM_OUT)
                        ,array( &$msg,SQLSRV_PARAM_OUT)
                        ,array( &$sql_error,SQLSRV_PARAM_OUT)
                        ,array( &$id,SQLSRV_PARAM_OUT));
        if($stmt = sqlsrv_query($cnn, $query, $params)){
          sqlsrv_next_result($stmt);
          sqlsrv_free_stmt($stmt);
          $resp['status']='ok';
          if($result === 1) $resp['insertado']=true; else $resp['insertado']=false;
          $resp['msg']= $msg;
          $resp['error']= $sql_error;
          $resp['id']=$id;
        }else{
          $resp['status']='error';
          $resp['post']= $_POST;
          $resp['error']= sqlsrv_errors();
        }//end if
      }else{
        $resp['status']='ok';
        $resp['insertado']=false;
        $resp['msg']='Correo Inv&aacute;lido...';
        $resp['focus']='#_correo';
      }//end if
    }elseif(isset($_POST['action'], $_POST['code'])&& $_POST['action'] === 'get::data::employee'){
      $query = 'exec cat.proc_get_data_employee	 @id =?
                                            ,@emp  =?
                                          	,@nombre  =?
                                          	,@locacion  =?
                                          	,@loc =?
                                          	,@departamento  =?
                                          	,@dep  =?
                                          	,@posicion  =?
                                          	,@pos  =?
                                            ,@msg  =?';
      $code= $_POST['code'];
      $res['emp'] = '';
      $res['nombre'] = '';
      $res['locacion'] = '';
      $res['loc'] = '';
      $res['departamento'] = '';
      $res['dep'] = '';
      $res['posicion'] = '';
      $res['pos'] = '';
      $res['msg'] = '';
      $params=array(array(&$code,SQLSRV_PARAM_IN)
                      ,array(&$res['emp'],SQLSRV_PARAM_OUT)
                      ,array(&$res['nombre'],SQLSRV_PARAM_OUT)
                      ,array(&$res['locacion'],SQLSRV_PARAM_OUT)
                      ,array(&$res['loc'],SQLSRV_PARAM_OUT)
                      ,array(&$res['departamento'],SQLSRV_PARAM_OUT)
                      ,array(&$res['dep'],SQLSRV_PARAM_OUT)
                      ,array(&$res['posicion'],SQLSRV_PARAM_OUT)
                      ,array(&$res['pos'] ,SQLSRV_PARAM_OUT)
                      ,array(&$res['msg'] ,SQLSRV_PARAM_OUT));

      $stmt= sqlsrv_query($cnn, $query, $params);
      if( $stmt !== false ) {
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt($stmt);

        $resp['result'] = $res;
        $resp['status'] = 'ok';
        $resp['post'] = $_POST;
      }else{
        $resp['error'] = sqlsrv_errors();
        $resp['status'] = 'error';
        $resp['post'] = $_POST;
      }//end if
    }elseif(isset($_POST['action'],$_POST['id'])&& $_POST['action'] === 'get::users::dominio'){
      set_time_limit(30);
  		//set_error_handler('error_hand', E_ALL);
      $html='';
      $dominio = $_POST['id'];
      $user = $com->_get_val($cnn
  													, '_bind_user'
  													, 'adm.dominios'
  													, 'id_dominio'
  													, $dominio
  													, 'nvarchar'
  													, '1');//$com->_get_param($cnn, 'ldap_bind_user'); // param?
      $pass = $com->_get_val($cnn
  													, '_bind_pass'
  													, 'adm.dominios'
  													, 'id_dominio'
  													, $dominio
  													, 'nvarchar'
  													, '1');//$com->_get_param($cnn, 'ldap_bind_pass'); // param?
      //$com->_get_param($cnn, 'dominio_cliente');
      //$host = $dominio ?: $com->_get_param($cnn, 'ldap_host');
      $host = $com->_get_val($cnn
  													, '_host'
  													, 'adm.dominios'
  													, 'id_dominio'
  													, $dominio
  													, 'nvarchar'
  													, '1');
      $ldap = new _ldap();

      $ad_user= $user.'@'.$dominio;
      $port = $com->_get_val($cnn
  													, '_port'
  													, 'adm.dominios'
  													, 'id_dominio'
  													, $dominio
  													, 'nvarchar'
  													, '1');
      $ad = $ldap->connect($host,$port);
      if ($ldap->login($ad, $ad_user, $pass) == 1 ){
        //$grupo = $com->_get_param($cnn, 'ldap_grupo');
        //$bdn= $com->_get_param($cnn, 'OU_grupos');
        $grupo = $com->_get_val($cnn
  														, '_grupo'
  														, 'adm.dominios'
  														, 'id_dominio'
  														, $dominio
  														, 'nvarchar'
  														, '1');
        $bdn = $com->_get_val($cnn
  															, '_ou_grupo'
  															, 'adm.dominios'
  															, 'id_dominio'
  															, $dominio
  															, 'nvarchar'
  															, '1');
        $members = $ldap->_get_members($ad,$bdn, $grupo, true);
        $members_string= $members['string'];

        $query = 'exec cat.proc_get_available_users @users= ?';
        $params = array(&$members_string);
        if($stmt = $com->_create_stmt($cnn, $query, $params)){
          while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $html.= "<div data-id='".$row['_username']."' class='fs option available-user bloque' data-parent='#cont-usuarios'>".$row['_username']."</div>";
          }// end while

          $resp['status'] = 'ok';
          $resp['html'] = $html;
          $resp['post'] = $_POST;
        }else{
          $resp['error'] = sqlsrv_errors();
          $resp['status'] = 'error';
          $resp['post'] = $_POST;

        }//end if
      }//end if
    }elseif(isset($_POST['action'], $_POST['id'])&& $_POST['action'] === 'get::data::request'){
      $query = 'exec adm.proc_get_data_request	 @id =?
                                            ,@code = ?
                                            ,@emp  =?
                                            ,@nombre  =?
                                            ,@locacion  =?
                                            ,@loc =?
                                            ,@departamento  =?
                                            ,@dep  =?
                                            ,@posicion  =?
                                            ,@pos  =?
                                            ,@id_role = ?
                                            ,@correo = ?
                                            ,@id_email = ?
                                            ,@msg  =?';
      $id= $_POST['id'];
      $res['code'] = '';
      $res['emp'] = '';
      $res['nombre'] = '';
      $res['locacion'] = '';
      $res['loc'] = '';
      $res['departamento'] = '';
      $res['dep'] = '';
      $res['posicion'] = '';
      $res['pos'] = '';
      $res['id_role'] = '';
      $res['correo'] = '';
      $res['id_email'] = '';
      $res['msg'] = '';
      $res['deptos'] = '';
      $params=array(array(&$id,SQLSRV_PARAM_IN)
                      ,array(&$res['code'],SQLSRV_PARAM_OUT)
                      ,array(&$res['emp'],SQLSRV_PARAM_OUT)
                      ,array(&$res['nombre'],SQLSRV_PARAM_OUT)
                      ,array(&$res['locacion'],SQLSRV_PARAM_OUT)
                      ,array(&$res['loc'],SQLSRV_PARAM_OUT)
                      ,array(&$res['departamento'],SQLSRV_PARAM_OUT)
                      ,array(&$res['dep'],SQLSRV_PARAM_OUT)
                      ,array(&$res['posicion'],SQLSRV_PARAM_OUT)
                      ,array(&$res['pos'] ,SQLSRV_PARAM_OUT)
                      ,array(&$res['id_role'] ,SQLSRV_PARAM_OUT)
                      ,array(&$res['correo'] ,SQLSRV_PARAM_OUT)
                      ,array(&$res['id_email'] ,SQLSRV_PARAM_OUT)
                      ,array(&$res['msg'] ,SQLSRV_PARAM_OUT));

      $stmt= sqlsrv_query($cnn, $query, $params);
      if( $stmt !== false ) {
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt($stmt);

        $query = "exec adm.proc_get_deptos_by_request @id = ?";
        $params= array(array(&$id,SQLSRV_PARAM_IN));
        $stmt = $com->_create_stmt($cnn,$query,$params);
        $html = '';
        if($stmt !== false){
          while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
            $html.="<div id='".$row['id_departamento']."' data-code='".$row['_departamento_code']."'  class='fs option depto-selected bloque'>
                <div class='txt fs floL enlinea'>".$row['_departamento']."</div>
                <i class='fa fa-1x fa-trash-o'></i>
            </div>";
          }//end while
          $res['deptos'] = $html;
          sqlsrv_free_stmt($stmt);
        }//end if
        //$resp['html'] = $html;
        $resp['result'] = $res;
        $resp['status'] = 'ok';
        $resp['post'] = $_POST;
      }else{
        $resp['error'] = sqlsrv_errors();
        $resp['status'] = 'error';
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

  function error_hand($errno){
		if  ($errno == 2){

      $response_array['status'] = 'error';
			$response_array['error'] = 'binding';
			$response_array['msg'] = 'Credenciales inv&aacute;lidas';
			echo json_encode($response_array);
		}else{
			echo 'error';//$errmsg;
		}//end if
	}//end function
 ?>
