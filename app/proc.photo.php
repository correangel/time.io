<?php
  if(isset($_GET['id'])){
    include_once $_SERVER['DOCUMENT_ROOT']."/login/class.login.php";
  	include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
  	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";
  	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.creates.php";
  	$com = new com_mssql();
  	$cnn = $com->_conectar_win(HOST,DATA);

  	$lifetime = $com->_get_param($cnn,  'session_lifetime');
    $clogin = new _login();
  	$clogin->iniciar_sesion('TimeIO', $lifetime);
    if($clogin->_logeado()){
      $id = $_GET['id'];
      $query = 'exec cat.proc_get_employee_photo_by_id ?;';
      $params = array(&$id);
      $stmt = sqlsrv_query($cnn, $query, $params);
      if( ! $stmt === false ) {
        if ( sqlsrv_fetch( $stmt ) ) {
           $image = sqlsrv_get_field( $stmt, 0, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY));
           header("Content-Type: image/jpg");
           fpassthru($image);
        }else{
          $resp['status'] = 'error';
          $resp['msg'] = 'No fetch.';
          echo json_encode($resp);
        }//end if
      }else{
        $resp['status'] = 'error';
        $resp['msg'] = 'No stmt.';
        echo json_encode($resp);
      }//end if
      //$resp['status'] = 'ok';
      //$resp['id'] = $id;
    }else{
      $url = $com->_get_param($cnn, 'raiz');
      $resp['status'] = 'login';
      $resp['url'] = $url;
      $resp['msg'] = 'Sesion Caducada...';
      echo json_encode($resp);
    }//end if


  }else{
    $resp['status'] = 'error';
    $resp['msg'] = 'No se especificó un ID...';
    echo json_encode($resp);
  }//end if
 ?>
