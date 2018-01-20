
<?php

  //if (isset($_POST['ope'],$_POST['page'])){
  if (isset($_GET['ope'],$_GET['page'], $_GET['c'])){
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
      //$ope = $_POST['ope'];
      //$page = $_POST['page'];
      $html = '';
      $ope = $_GET['ope'];
      $page = $_GET['page'];
      $c = $_GET['c'];
      $query = 'exec tra.proc_create_lista_by_ope @ope = ?, @per = null, @pagenum = ?, @pagesize = 50;';
      $params = array(&$ope,&$page);
      $stmt = $com->_create_stmt($cnn, $query, $params);
      if($stmt){
        $i=0;
        $create = new _creates();
        while( $value = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
          $i++;
          $html.="	<div id='".$value['id_lista']."' data-id='$i'
                data-periodo='".$value['id_periodo']."'
                data-employee='".$value['id_employee']."'
                class='fm bloque empl row'>
              <div class='floL fm cell enlinea elli id'>".$value['_alter_id']."</div>
              <div class='floL fm cell enlinea elli name'>".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."</div>
              <div class='floL fm cell enlinea elli ".($c > 15? 'last16': 'last')."'>".$value['_posicion_name']."</div>
              <div tabindex='1' class='der floL fm cell dia enlinea work waves-effect'><span class='fm tran-bez-5s border-interno'>". $value['_days'] ."</span></div>
              <div tabindex='2' data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c01']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c01'] ."</span></div>
              <div tabindex='3' data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c02']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c02'] ."</span></div>
              <div tabindex='4' data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c03']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c03'] ."</span></div>
              <div tabindex='5' data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c04']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c04'] ."</span></div>
              <div tabindex='6' data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c05']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c05'] ."</span></div>
              <div tabindex='7' data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c06']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c06'] ."</span></div>
              <div tabindex='8' data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c07']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c07'] ."</span></div>
              <div tabindex='9' data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c08']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c08'] ."</span></div>
              <div tabindex='10' data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c09']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c09'] ."</span></div>
              <div tabindex='11' data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c10']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c10'] ."</span></div>
              <div tabindex='12' data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c11']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c11'] ."</span></div>
              <div tabindex='13' data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c12']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c12'] ."</span></div>
              <div tabindex='14' data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c13']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c13'] ."</span></div>
              <div tabindex='15' data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea".($create->_color_from_letra($cnn, $com, $value['_c14']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c14'] ."</span></div>
              <div tabindex='16' data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c15']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c15'] ."</span></div>
              ".($c == 16 ? "<div tabindex='17' data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c16']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c16'] ."</span></div>":"")."
            </div>";
        }//end while
        sqlsrv_free_stmt( $stmt);
        $resp['status'] = 'ok';
        $resp['html'] = $html;

      }else{
        $resp['status'] = 'error';
        $resp['msg'] = '1.stmt';
      }//end if
    }else{
      $url = $com->_get_param($cnn, 'raiz');
      $resp['status'] = 'login';
      $resp['url'] = $url;
      $resp['msg'] = 'Sesion Caducada...';

    }//end if

  }else{
    $resp['status'] = 'error';
    $resp['msg'] = 'Datos insuficientes...';

  }//end if
  echo json_encode($resp);
?>
