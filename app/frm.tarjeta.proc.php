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
    if(isset($_POST['action'],$_POST['ope'])&&$_POST['action']==='get::employees'){
      $query='exec cat.proc_get_employees_by_ope @ope = ?';
      $ope = $_POST['ope'];
      $params= array(array(&$ope, SQLSRV_PARAM_IN));
      $html='';
      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $html.="<div id='".$row['employee_id']."'
                       class='row fs bloque for-fildiva'
                       data-alter='".$row['_alter_id']."'
                       data-name='".$row['_nombre']."'
                       data-departamento='".$row['_departamento_name']."'
                       data-departamento-code='".$row['_departamento_code']."'
                       data-posicion='".$row['_posicion_name']."'
                       data-posicion-code='".$row['_posicion_code']."'
                       data-locacion-code='".$row['_locacion_code']."'
                       data-clase='".$row['_clase']."'
                       data-hire='".$row['_hire_date']."'>
                       <div class='_alter_id fs enlinea floL'>".$row['_alter_id']."</div>
                       <div class='_nombre fs enlinea floL'>".$row['_nombre']."</div>
                  </div>";
        }//end while
        $resp['status'] = 'ok';
        $resp['html'] = $html;
        $resp['post'] = $_POST;
        sqlsrv_free_stmt($stmt);
      }else{
        $resp['status'] = 'error';
        $resp['error'] = sqlsrv_errors();
        $resp['msg'] = 'error sql...';
        $resp['post'] = $_POST;
      };
    }elseif(isset($_POST['action'],$_POST['emp'],$_POST['per'])&&$_POST['action']==='get::data::asistencia'){
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      //---------------------------------
      $resp['status'] = '';
      $resp['asis_rows'] = '';
      $resp['chec_rows'] = '';
      $resp['jorn_rows'] = '';
      $resp['hora_rows'] = '';
      $resp['asis_head'] = '';
      $resp['chec_head'] = '';
      $resp['jorn_head'] = '';
      $resp['hora_head'] = '';
      //---------------------------------
      $tablas='';
      $resp['post'] = $_POST;
      $query = "exec tra.proc_get_info_employee_for_tarjeta @per = ?, @emp = ? ";
      $params= array(array(&$per, SQLSRV_PARAM_IN)
                    ,array(&$emp, SQLSRV_PARAM_IN));
      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $resp['emp_data'] = $row;
          $ocultos_tabla="<td>".$row['_periodo']."</td>
                      <td>".$row['_alter_id']."</td>
                      <td>".$row['_nombre']."</td>
                      <td>".$row['_hire_date']."</td>
                      <td>".$row['_clase']."</td>
                      <td>".$row['_posicion_code']."</td>
                      <td>".$row['_posicion_name']."</td>
                      <td>".$row['_departamento_code']."</td>
                      <td>".$row['_departamento_name']."</td>";

          $ocultos = "<div class='fn oculto enlinea to-excel'>".$row['_periodo']."</div>
                      <div class='fn oculto enlinea to-excel'>".$row['_alter_id']."</div>
                      <div class='fn oculto enlinea to-excel'>".$row['_nombre']."</div>
                      <div class='fn enlinea oculto to-excel'>".$row['_hire_date']."</div>
                      <div class='fn enlinea oculto to-excel'>".$row['_clase']."</div>
                      <div class='fn enlinea oculto to-excel'>".$row['_posicion_code']."</div>
                      <div class='fn enlinea oculto to-excel'>".$row['_posicion_name']."</div>
                      <div class='fn enlinea oculto to-excel'>".$row['_departamento_code']."</div>
                      <div class='fn enlinea oculto to-excel'>".$row['_departamento_name']."</div>";
        }//end while
        sqlsrv_free_stmt($stmt);
      }else{
        $resp['error'] = sqlsrv_errors();
        $resp['status'] = 'error';
        $com->_desconectar($cnn);
        echo json_encode($resp);
        return;
      }//end if
      //---------------
      //Asistencia
      //---------------
      $query = "exec tra.proc_get_asistencia_by_emp @per = ?, @emp = ?";
      $params= array(array(&$per, SQLSRV_PARAM_IN)
                    ,array(&$emp, SQLSRV_PARAM_IN));
      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        $tablas.= "<table id='tabla-asis'>
                    <thead><tr>
                      <th>Periodo</th>
                      <th>Codigo</th>
                      <th>Nombre</th>
                      <th>Ingreso</th>
                      <th>Clase</th>
                      <th>Posicion</th>
                      <th>PosicionDesc</th>
                      <th>Departamento</th>
                      <th>DepartamentoDesc</th>
                      <th>Etiqueta</th>
                      <th>Dia</th>
                      <th>Fecha</th>
                      <th>Letra</th>
                      <th>Entrada</th>
                      <th>Salida</th>
                      <th>Jornada</th>
                    </tr></thead>";

        $resp['asis_head'].= "<div class='fn head'>
                                <div class='fn enlinea oculto to-excel'>Periodo</div>
                                <div class='fn enlinea oculto to-excel'>Codigo</div>
                                <div class='fn enlinea oculto to-excel'>Nombre</div>
                                <div class='fn enlinea oculto to-excel'>Ingreso</div>
                                <div class='fn enlinea oculto to-excel'>Clase</div>
                                <div class='fn enlinea oculto to-excel'>Posicion</div>
                                <div class='fn enlinea oculto to-excel'>PosicionDesc</div>
                                <div class='fn enlinea oculto to-excel'>Departamento</div>
                                <div class='fn enlinea oculto to-excel'>DepartamentoDesc</div>
                                <div style='width:calc(8% - 10px);' class='fs cell-min enlinea floL to-excel'>Etiqueta</div>
                                <div style='width:calc(10% - 10px);' class='fs cell-min enlinea floL to-excel'>Dia</div>
                                <div style='width:calc(14% - 10px);' class='fs cell-min enlinea floL to-excel'>Fecha</div>
                                <div style='width:calc(8% - 10px);' class='fs cell-min enlinea floL to-excel'>Letra</div>
                                <div style='width:calc(25% - 10px);' class='fs cell-min enlinea floL to-excel'>Entrada</div>
                                <div style='width:calc(25% - 10px);' class='fs cell-min enlinea floL to-excel'>Salida</div>
                                <div style='width:calc(10% - 10px);' class='fs cell-min enlinea floL to-excel'>Jornada</div>
                              </div>";
        $tablas.= "<tbody>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $tablas.= "<tr>
                        ".$ocultos_tabla."
                        <td>".$row['_etiqueta']."</td>
                        <td>".$row['_day_txt']."</td>
                        <td>".$row['_fecha']."</td>
                        <td>".$row['_letra']."</td>
                        <td>".$row['_entrada']."</td>
                        <td>".$row['_salida']."</td>
                        <td>".(gmdate('H:i', floor(($row['_jornada']/60) * 3600)))."</td>
                      </tr>";
          $resp['asis_rows'].= "<div class='fn row'>
                                  ".$ocultos."
                                  <div style='width:calc(8% - 10px);' class='fs cell-min enlinea floL to-excel'>".$row['_etiqueta']."</div>
                                  <div style='width:calc(10% - 10px);' class='fs cell-min enlinea floL to-excel'>".$row['_day_txt']."</div>
                                  <div style='width:calc(14% - 10px);' class='fs cell-min enlinea floL to-excel'>".$row['_fecha']."</div>
                                  <div style='width:calc(8% - 10px);' class='fs cell-min enlinea floL to-excel'>".$row['_letra']."</div>
                                  <div style='width:calc(25% - 10px);' class='fs cell-min enlinea floL to-excel'>".$row['_entrada']."</div>
                                  <div style='width:calc(25% - 10px);' class='fs cell-min enlinea floL to-excel'>".$row['_salida']."</div>
                                  <div style='width:calc(10% - 10px);' class='fs cell-min enlinea floL to-excel'>".(gmdate('H:i', floor(($row['_jornada']/60) * 3600)))."</div>
                                </div>";
        }//end while
        $tablas.= "</tbody></table>";
        sqlsrv_free_stmt($stmt);
      }else{
        $resp['error'] = sqlsrv_errors();
        $resp['status'] = 'error';
        $com->_desconectar($cnn);
        echo json_encode($resp);
        return;
      }//end if

      //---------------
      $query = "exec tra.proc_get_checadas_by_employee_tarjeta @per = ?, @emp = ?";
      $params= array(array(&$per, SQLSRV_PARAM_IN)
                    ,array(&$emp, SQLSRV_PARAM_IN));
      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        $tablas.= "<table id='tabla-chec'>
                    <thead><tr>
                      <th>Periodo</th>
                      <th>Codigo</th>
                      <th>Nombre</th>
                      <th>Ingreso</th>
                      <th>Clase</th>
                      <th>Posicion</th>
                      <th>PosicionDesc</th>
                      <th>Departamento</th>
                      <th>DepartamentoDesc</th>
                      <th>Checada</th>
                      <th>Tipo</th>
                      <th>Dispositivo</th>
                    </tr></thead>";

        $resp['chec_head'].= "<div class='fn head'>
                                <div class='fn enlinea oculto to-excel'>Periodo</div>
                                <div class='fn enlinea oculto to-excel'>Codigo</div>
                                <div class='fn enlinea oculto to-excel'>Nombre</div>
                                <div class='fn enlinea oculto to-excel'>Ingreso</div>
                                <div class='fn enlinea oculto to-excel'>Clase</div>
                                <div class='fn enlinea oculto to-excel'>Posicion</div>
                                <div class='fn enlinea oculto to-excel'>PosicionDesc</div>
                                <div class='fn enlinea oculto to-excel'>Departamento</div>
                                <div class='fn enlinea oculto to-excel'>DepartamentoDesc</div>
                                <div style='width:calc(40% - 40px);' class='fn cell enlinea floL to-excel'>Checada</div>
                                <div style='width:calc(20% - 40px);' class='fn cell enlinea floL to-excel'>Tipo</div>
                                <div style='width:calc(40% - 40px);' class='fn cell enlinea floL to-excel'>Dispositivo</div>
                              </div>";
        $tablas.= "<tbody>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $tablas.= "<tr>
                        ".$ocultos_tabla."
                        <td>".$row['_checada']."</td>
                        <td>".$row['_tipo']."</td>
                        <td>".$row['_dispositivo_code']."</td>
                      </tr>";
          $resp['chec_rows'].= "<div class='fn row'>
                                  ".$ocultos."
                                  <div style='width:calc(40% - 40px);' class='fn cell floL enlinea to-excel'>".$row['_checada']."</div>
                                  <div style='width:calc(20% - 40px);' class='fn cell floL enlinea to-excel'>".$row['_tipo']."</div>
                                  <div style='width:calc(40% - 40px);' class='fn cell floL enlinea to-excel'>".$row['_dispositivo_code']."</div>
                                </div>";
        }//end while
        $tablas.= "</tbody></table>";
        sqlsrv_free_stmt($stmt);
      }else{
        $resp['error'] = sqlsrv_errors();
        $resp['status'] = 'error';
        $com->_desconectar($cnn);
        echo json_encode($resp);
        return;
      }//end if

      $query = "exec [tra].[proc_get_jornadas_by_employee_tarjeta] @per = ?, @emp = ?";
      $params= array(array(&$per, SQLSRV_PARAM_IN)
                    ,array(&$emp, SQLSRV_PARAM_IN));
      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        //$tabla
        $tablas.= "<table id='tabla-jorn'>
                    <thead><tr>
                      <th>Periodo</th>
                      <th>Codigo</th>
                      <th>Nombre</th>
                      <th>Ingreso</th>
                      <th>Clase</th>
                      <th>Posicion</th>
                      <th>PosicionDesc</th>
                      <th>Departamento</th>
                      <th>DepartamentoDesc</th>
                      <th>Entrada</th>
                      <th>Salida</th>
                      <th>Jornada</th>
                    </tr></thead>";
        $resp['jorn_head'].= "<div class='fn head'>
                                <div class='fn enlinea oculto to-excel'>Periodo</div>
                                <div class='fn enlinea oculto to-excel'>Codigo</div>
                                <div class='fn enlinea oculto to-excel'>Nombre</div>
                                <div class='fn enlinea oculto to-excel'>Ingreso</div>
                                <div class='fn enlinea oculto to-excel'>Clase</div>
                                <div class='fn enlinea oculto to-excel'>Posicion</div>
                                <div class='fn enlinea oculto to-excel'>PosicionDesc</div>
                                <div class='fn enlinea oculto to-excel'>Departamento</div>
                                <div class='fn enlinea oculto to-excel'>DepartamentoDesc</div>
                                <div style='width:calc(40% - 40px);' class='fn cell enlinea floL to-excel'>Entrada</div>
                                <div style='width:calc(40% - 40px);' class='fn cell enlinea floL to-excel'>Salida</div>
                                <div style='width:calc(20% - 40px);' class='fn cell enlinea floL to-excel'>Jornada</div>
                              </div>";
        $tablas.= "<tbody>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $tablas.= "<tr>
                        ".$ocultos_tabla."
                        <td>".$row['_entrada']."</td>
                        <td>".$row['_salida']."</td>
                        <td>".(gmdate('H:i', floor(($row['_jornada']/60) * 3600)))."</td>
                      </tr>";
          $resp['jorn_rows'].= "<div class='fn row'>
                                  ".$ocultos."
                                  <div style='width:calc(40% - 40px);' class='fn cell floL enlinea to-excel'>".$row['_entrada']."</div>
                                  <div style='width:calc(40% - 40px);' class='fn cell floL enlinea to-excel'>".$row['_salida']."</div>
                                  <div style='width:calc(20% - 40px);' class='fn cell floL enlinea to-excel'>".(gmdate('H:i', floor(($row['_jornada']/60) * 3600)))."</div>
                                </div>";
        }//end while
        $tablas.= "</tbody></table>";
        sqlsrv_free_stmt($stmt);
      }else{
        $resp['error'] = sqlsrv_errors();
        $resp['status'] = 'error';
        $com->_desconectar($cnn);
        echo json_encode($resp);
        return;
      }//endif
      $resp['tablas'] = $tablas;
      $resp['status'] = 'ok';
    }elseif(isset($_POST['action'],$_POST['ope'],$_POST['alter'])&&$_POST['action']==='get::employee::by::enter'){
        $query='exec cat.proc_get_employees_by_ope_by_alter @ope = ?, @alter = ? , @result= ?, @msg = ?';
        $ope = $_POST['ope'];
        $alter = $_POST['alter'];
        $msg='';
        $result=0;
        $params= array(array(&$ope, SQLSRV_PARAM_IN)
                      ,array(&$alter, SQLSRV_PARAM_IN)
                      ,array(&$result, SQLSRV_PARAM_OUT)
                      ,array(&$msg, SQLSRV_PARAM_OUT));
        //$html='';

        if($stmt = $com->_create_stmt($cnn, $query, $params)){

          while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $resp['row'] = $row;
          }//end while
          sqlsrv_next_result($stmt);
          $resp['result'] = $result;
          $resp['msg'] = $msg;
          $resp['status'] = 'ok';
          $resp['post'] = $_POST;
          sqlsrv_free_stmt($stmt);
        }else{
          $resp['result'] = 0;
          $resp['msg'] = 'Inexistente...';
          $resp['status'] = 'error';
          $resp['error'] = sqlsrv_errors();
          $resp['post'] = $_POST;
        };
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
