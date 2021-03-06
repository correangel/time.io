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
    /*$id_navigator = (isset($_POST['id_nav'])?$_POST['id_nav']:$_POST['id_nav_proc']);
		$id_operator = $_SESSION['id_operator'];
		$_full = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_full'));
		$_read = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_read'));
		$_write = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_write'));
		$_special = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_special'));*/
    if(isset($_POST['action'],$_POST['emp'],$_POST['per'])&& $_POST['action'] === 'data::employee'){
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $query = 'exec [cat].[proc_get_info_employee] @emp = ?';
      $params = array(array(&$emp,SQLSRV_PARAM_IN));
      if($stmt = sqlsrv_query($cnn, $query, $params)){
        $info = '';
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $info.="<div class='fs g'>";
            $info.= "<div class='fs floL label enlinea elli'>Ingreso:</div>";
            $info.= "<div class='fs value enlinea'>".$row['_hire_date']."</div>";
          $info.="</div>";
          $info.="<div class='fs g change'>";
            $info.= "<div class='fs floL label enlinea elli'>Departamento:</div>";
            $info.= "<div class='fs value enlinea elli'  data-code='".$row['_departamento_code']."' data-name='".$row['_departamento_name']."' >".$row['_departamento_code']."</div>";
          $info.="</div>";
          $info.="<div class='fs g change'>";
            $info.= "<div class='fs floL label enlinea elli'>Posición:</div>";
            $info.= "<div class='fs value enlinea elli' data-code='".$row['_posicion_code']."' data-name='".$row['_posicion_name']."'>".$row['_posicion_code']."</div>";
          $info.="</div>";
          $info.="<div class='fs b enlinea'>";
            $info.= "<div class='fs label' title='Pago de Festivos Trabajados'>FE</div>";
            $info.= "<div class='fs value enlinea'><i class='fa fa-1x ".($row['_festivos']===1?'fa-check-square-o':'fa-square-o')."'></i></div>";
          $info.="</div>";
          $info.="<div class='fs b enlinea'>";
            $info.= "<div class='fs label ' title='Pago de Horas Extras'>HE</div>";
            $info.= "<div class='fs value enlinea'><i class='fa fa-1x ".($row['_horas_extras']===1?'fa-check-square-o':'fa-square-o')."'></i></div>";
          $info.="</div>";
          $info.="<div class='fs b enlinea'>";
            $info.= "<div class='fs label' title='Pago de Prima Dominical'>PD</div>";
            $info.= "<div class='fs value enlinea'><i class='fa fa-1x ".($row['_prima_dominical']===1?'fa-check-square-o':'fa-square-o')."'></i></div>";
          $info.="</div>";
          $info.="<div class='fs c enlinea'>";
            $info.= "<div class='fs label 'title='Pago de Horas Nocturnas'>HN</div>";
            $info.= "<div class='fs value enlinea'><i class='fa fa-1x ".($row['_horas_nocturnas']===1?'fa-check-square-o':'fa-square-o')."'></i></div>";
          $info.="</div>";
        }//end while
        $resp['info'] = $info;
        sqlsrv_free_stmt($stmt);

        $query = 'exec tra.proc_get_jornadas_by_employee @per= ?, @emp = ?';
        $params = array(array(&$per,SQLSRV_PARAM_IN),array(&$emp,SQLSRV_PARAM_IN));
        if($stmt = sqlsrv_query($cnn, $query, $params)){
          $jornadas = '';
          while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $jornadas.="<div class='fs g'>";
              $jornadas.= "<div class='fs floL enlinea fecha-corta'>".$row['_entrada']."</div>";
              $jornadas.= "<div class='fs floL enlinea fecha-corta'>".$row['_salida']."</div>";
              $jornadas.= "<div class='fs enlinea jornada'>".(gmdate('H:i', floor(($row['_jornada']/60) * 3600)))."</div>";
            $jornadas.="</div>";
          }//end while
          $resp['jornadas'] = $jornadas;
          sqlsrv_free_stmt($stmt);
        }//end if

        $query = 'exec tra.proc_get_checadas_by_employee @per= ?, @emp = ?';
        $params = array(array(&$per,SQLSRV_PARAM_IN),array(&$emp,SQLSRV_PARAM_IN));
        if($stmt = sqlsrv_query($cnn, $query, $params)){
          $checadas = '';
          while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $checadas.="<div class='fs g'>";
              $checadas.= "<div class='fs floL enlinea tipoA'>".($row['_tipo']==='E' ? 'Entrada': 'Salida')."</div>";
              $checadas.= "<div class='fs floL enlinea fecha'>".$row['_checada']."</div>";
              $checadas.= "<div class='fs enlinea quien oculto'>".$row['_dispositivo_code']."</div>";
            $checadas.="</div>";
          }//end while
          $resp['checadas'] = $checadas;
          sqlsrv_free_stmt($stmt);

          $query = 'exec tra.proc_get_ausentismos_by_employee @per = ?, @emp = ?';
          $params = array(array(&$per,SQLSRV_PARAM_IN),array(&$emp,SQLSRV_PARAM_IN));
          if($stmt = sqlsrv_query($cnn, $query, $params)){
            $ausentismos = '';
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
              $ausentismos.="<div class='fs g' data-causa='".$row['_causa']."'>";
                $ausentismos.="<div class='fs visible has-causa'>";
                  $ausentismos.= "<div class='fs floL enlinea tipoB'>".$row['_letra']."</div>";
                  $ausentismos.= "<div class='fs floL enlinea fecha-corta'>".$row['_ausentismo_date']."</div>";
                  $ausentismos.= "<div class='fs enlinea quien-corto elli'>".$row['_operator']."</div>";
                $ausentismos.="</div>";
                if ($row['_causa'] !== 'undefined'){
                  $ausentismos.="<div class='fs le-causa oculto'>";
                    $ausentismos.= "<div class='fs floL enlinea tipoB'>".$row['_letra']."</div>";
                    $ausentismos.= "<div class='fs floL enlinea elli causa'>".$row['_causa']."</div>";
                    $ausentismos.= "<div class='fs floL enlinea fecha'>".$row['_insert_date']."</div>";
                    //$ausentismos.= "".($row['_comentarios']!=='undefined' ? "<div class='fs floL enlinea comentarios'><i class='fa fa-1x fa-commenting-o'></i></div>":'')."";
                  $ausentismos.="</div>";
                }else{
                  $ausentismos.="<div class='fs le-causa oculto'>";
                    $ausentismos.= "<div class='fs floL enlinea tipoB'>".$row['_letra']."</div>";
                    $ausentismos.= "<div class='fs floL enlinea elli causa'>".$row['_ausentismo_desc']."</div>";
                    $ausentismos.= "<div class='fs floL enlinea fecha'>".$row['_insert_date']."</div>";
                  $ausentismos.="</div>";
                }//end if
              $ausentismos.="</div>";
            }//end while
            $resp['ausentismos'] = $ausentismos;
            $resp['status'] = 'ok';
            sqlsrv_free_stmt($stmt);
          }else{
            $resp['status'] = 'error';
            $resp['errores'] = sqlsrv_errors();
            $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
          }//end if
        }else{
          $resp['status'] = 'error';
          $resp['errores'] = sqlsrv_errors();
          $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
        }//end if
      }else{
        $resp['status'] = 'error';
        $resp['errores'] = sqlsrv_errors();
        $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
      }//end if
    }elseif (isset($_POST['action'],$_POST['emp'],$_POST['per'],$_POST['alter'] )&& $_POST['action'] === 'lista::expand::ausentismos') {
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $alter = $_POST['alter'];
      $query = 'exec tra.proc_get_ausentismos_by_employee_expand @per = ?, @emp = ?';
      $params = array(array(&$per,SQLSRV_PARAM_IN),array(&$emp,SQLSRV_PARAM_IN));
      if($stmt = sqlsrv_query($cnn, $query, $params)){
        $c=0;
        $table= "<table title='Detalle Ausentismos [$alter]' id='rep-grid-ausentismos' class='fs table table-condensed table-hover table-striped'>
                        <thead class='fs'><tr>
                          <th class='fs oculto' data-column-id='IDTraAusentismo' >IDTraAusentismo</th>
                          <th class='fs' data-column-id='Codigo' data-type='numeric' data-order='asc'>Codigo</th>
                          <th class='fs' data-column-id='FechaAusentismo' >FechaAusentismo</th>
                          <th class='fs' data-column-id='Letra' >Letra</th>
                          <th class='fs' data-column-id='DescripcionAusentismo' >DescripcionAusentismo</th>
                          <th class='fs' data-column-id='Causa' >Causa</th>
                          <th class='fs' data-column-id='FechaCaptura' >FechaCaptura</th>
                          <th class='fs' data-column-id='UsuarioCaptura' >UsuarioCaptura</th>
                          <th class='fs' data-column-id='Comentarios' >Comentarios</th>
                          <th class='fs' data-column-id='FechaTomada' >FechaTomada</th>
                     </tr></thead>
                  <tbody class='fs'>";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $c++;
          $table.= "<tr class='fs' >
                    <td class='fs oculto'>".$row['IDTraAusentismo']."</td>
                    <td class='fs'>".$row['Codigo']."</td>
                    <td class='fs'>".$row['FechaAusentismo']."</td>
                    <td class='fs'>".$row['Letra']."</td>
                    <td class='fs'>".$row['DescripcionAusentismo']."</td>
                    <td class='fs'>".$row['Causa']."</td>
                    <td class='fs'>".$row['FechaCaptura']."</td>
                    <td class='fs'>".$row['UsuarioCaptura']."</td>
                    <td class='fs'>".$row['Comentarios']."</td>
                    <td class='fs'>".$row['FechaTomada']."</td>
                </tr>";
        }// end while
        $table.= "</tbody></table>";
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['table'] = $table;
        $resp['count'] = $c;
        //$resp['body'] = $body;
        //$resp['head'] = $head;
        $resp['post'] = $_POST;
      }else{
        $resp['status'] = 'error';
        $resp['errores'] = sqlsrv_errors();
        $resp['post'] = $_POST;
        $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
      }//end if

    }elseif (isset($_POST['action'],$_POST['emp'],$_POST['per'],$_POST['cn'] )&& $_POST['action'] === 'get::cn') {
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $cn = $_POST['cn'];
      $query = "exec tra.proc_get_jornada_by_cn @cn = ?, @emp = ?, @per = ?,@fec = ?,@ent =?	,@sal =?,@jor = ?, @aus = ?";
      $fec = '';
      $ent = '';
      $sal = '';
      $jor = '';
      $aus = '';
      $params = array(array(&$cn, SQLSRV_PARAM_IN)
                    , array(&$emp, SQLSRV_PARAM_IN)
                    , array(&$per, SQLSRV_PARAM_IN)
                    , array(&$fec, SQLSRV_PARAM_OUT)
                    , array(&$ent, SQLSRV_PARAM_OUT)
                    , array(&$sal, SQLSRV_PARAM_OUT)
                    , array(&$jor, SQLSRV_PARAM_OUT)
                    , array(&$aus, SQLSRV_PARAM_OUT));
      $stmt= sqlsrv_query($cnn, $query, $params);
      if( $stmt !== false ) {
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['fec'] = $fec;
        $resp['ent'] = $ent;
        $resp['sal'] = $sal;
        $resp['jor'] = gmdate('H:i', floor(($jor/60) * 3600));
        $resp['aus'] = $aus;
        $resp['post'] = $_POST;
      }else{
        $resp['status'] = 'error';
        $resp['errores'] = sqlsrv_errors();
        $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
      }
    }elseif (isset($_POST['action'],$_POST['letra'],$_POST['per'], $_POST['ope'])&& $_POST['action'] === 'causa::letra') {
      $letra = $_POST['letra'];
      $per = $_POST['per'];
      $query = "exec cat.proc_letra_need_causa @letra = ?, @per = ?, @result = ?";
      $result = 0;
      $params = array(array(&$letra, SQLSRV_PARAM_IN)
                    , array(&$per, SQLSRV_PARAM_IN)
                    , array(&$result, SQLSRV_PARAM_OUT));
      $stmt= sqlsrv_query($cnn, $query, $params);
      if( $stmt !== false ) {
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt($stmt);
        if ($result === 1){
          $ope = $_POST['ope'];
          $query = "exec cat.proc_get_causas_by_letra @letra = ?, @ope = ?";
          $params = array(array(&$letra, SQLSRV_PARAM_IN)
                          ,array(&$ope, SQLSRV_PARAM_IN));
          $stmt= sqlsrv_query($cnn, $query, $params);
          if( $stmt !== false ) {
            $html="<div id='causas-cont' class='fn noselect' title='Selecciona la Causa'>";
              $html.="<div id='letra-causas' class='fn'><select id='sel-causa' class='fs'>";
              $html.="<option data-comentarios='0' class='fs' selected></option>";
              while ($row = sqlsrv_fetch_array($stmt , SQLSRV_FETCH_ASSOC)) {
                $html.="<option value='".$row['id_causa']."'
                                data-comentarios='".$row['_comentarios']."'
                                data-requiere-fecha='".$row['_requiere_fecha']."'
                                class='fs'>".$row['_causa']."</option>";
              }//end if
              $html.="</select></div>";
              $html.="<div id='letra-causas-comentario' class='fn oculto'>";
                $html.= "<textarea id='causa-comentarios' class='fs' placeholder='Comentarios'></textarea>";
              $html.="</div>";
              $html.="<div id='letra-causas-fecha' class='fn oculto'>";
                $html.= "<input id='causa-fecha' class='fs' type='date' min='01/01/2017' max='30/01/2018'/>"; //min='".(strlen($row['_min_fecha']))."' max='".(strlen($row['_max_fecha']))."'/>";
              $html.="</div>";
            $html.="</div>";
            $resp['html'] = $html;
            sqlsrv_free_stmt($stmt);
          }else{
            $resp['status'] = 'error';
            $resp['errores'] = sqlsrv_errors();
            $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
          }//end if
        }//end if
        $resp['result'] = $result;
        $resp['status'] = 'ok';

      }else{
        $resp['status'] = 'error';
        $resp['errores'] = sqlsrv_errors();
        $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
      }//end if
    }elseif (isset($_POST['action'], $_POST['ope'], $_POST['per'])&& $_POST['action'] === 'get::lista::toexcel') {
      $per = $_POST['per'];
      $ope = $_POST['ope'];
      $query = 'exec [tra].[proc_get_lista_by_ope_toexcel] @ope = ? ,@per = ?';
      $params =array(array(&$ope, SQLSRV_PARAM_IN)
                    ,array(&$per, SQLSRV_PARAM_IN));
      $stmt= sqlsrv_query($cnn, $query, $params);
      if( $stmt === false ) {
        $resp['status'] = 'error';
        $resp['errores'] = sqlsrv_errors();
        $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
        $resp['post'] = $_POST;
      }else{
        $c = 0;
        $table= "<table id='lista-grid' class='fs oculto'>
                        <thead ><tr>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>ApellidoPaterno</th>
                          <th>ApellidoMaterno</th>
                          <th>Periodo</th>
                          <th>CodigoLocacion</th>
                          <th>Locacion</th>
                          <th>CodigoDepto</th>
                          <th>Departamento</th>
                          <th>CodigoPosicion</th>
                          <th>Posicion</th>
                          <th>_c01 </th>
                          <th>_c02 </th>
                          <th>_c03 </th>
                          <th>_c04 </th>
                          <th>_c05 </th>
                          <th>_c06 </th>
                          <th>_c07 </th>
                          <th>_c08 </th>
                          <th>_c09 </th>
                          <th>_c10 </th>
                          <th>_c11 </th>
                          <th>_c12 </th>
                          <th>_c13 </th>
                          <th>_c14 </th>
                          <th>_c15 </th>
                          <th>_c16 </th>
                     </tr></thead>
                  <tbody>";
        while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
          $c++;
          $table.= "<tr>
                    <td>".$row['Codigo']."</td>
                    <td>".$row['Nombre']."</td>
                    <td>".$row['ApellidoPaterno']."</td>
                    <td>".$row['ApellidoMaterno']."</td>
                    <td>".$row['Periodo']."</td>
                    <td>".$row['CodigoLocacion']."</td>
                    <td>".$row['Locacion']."</td>
                    <td>".$row['CodigoDepto']."</td>
                    <td>".$row['Departamento']."</td>
                    <td>".$row['CodigoPosicion']."</td>
                    <td>".$row['Posicion']."</td>
                    <td>".$row['_c01']."</td>
                    <td>".$row['_c02']."</td>
                    <td>".$row['_c03']."</td>
                    <td>".$row['_c04']."</td>
                    <td>".$row['_c05']."</td>
                    <td>".$row['_c06']."</td>
                    <td>".$row['_c07']."</td>
                    <td>".$row['_c08']."</td>
                    <td>".$row['_c09']."</td>
                    <td>".$row['_c10']."</td>
                    <td>".$row['_c11']."</td>
                    <td>".$row['_c12']."</td>
                    <td>".$row['_c13']."</td>
                    <td>".$row['_c14']."</td>
                    <td>".$row['_c15']."</td>
                    <td>".$row['_c16']."</td>
                </tr>";
        }//end while
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['table'] = $table;
        $resp['count'] = $c;
        //$resp['body'] = $body;
        //$resp['head'] = $head;
      }//end if

    }elseif (isset($_POST['action'], $_POST['emp'], $_POST['per'], $_POST['cn'])&& $_POST['action'] === 'delete::letra') {
      $id_operator = $_SESSION['id_operator'];
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $cn = $_POST['cn'];
      $result = 0;
      $ant = '';
      $color = '';
      $msg = '';
      $query = 'tra.proc_delete_ausentismo
                	 @emp = ?
                	,@per = ?
                	,@cn = ?
                	,@ope = ?
                	,@ant = ?
                	,@color = ?
                	,@msg =?
                	,@result =  ?
                  ,@verbose = 0';
      $params =array(array(&$emp, SQLSRV_PARAM_IN)
                    ,array(&$per, SQLSRV_PARAM_IN)
                    ,array(&$cn, SQLSRV_PARAM_IN)
                    ,array(&$id_operator, SQLSRV_PARAM_IN)
                    ,array(&$ant, SQLSRV_PARAM_OUT)
                    ,array(&$color, SQLSRV_PARAM_OUT)
                    ,array(&$msg, SQLSRV_PARAM_OUT)
                    ,array(&$result, SQLSRV_PARAM_OUT));
      $stmt= sqlsrv_query($cnn, $query, $params);
      if( $stmt === false ) {
        //echo "Error in executing statement 3.\n";
        $resp['status'] = 'error';
        $resp['errores'] = sqlsrv_errors();
        $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';

      }else{
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['letra'] = $ant;
        $resp['color'] = $color;
        $resp['msg'] = $msg;
        $resp['result'] = $result;
      }//end if

    }elseif(isset($_POST['action'],$_POST['ope'],$_POST['anterior'], $_POST['emp'], $_POST['per'], $_POST['cn'])&& $_POST['action'] === 'validar::atras::lista') {
      $id_operator = $_SESSION['id_operator'];
      $ope = $_POST['ope'];
      $anterior = $_POST['anterior'];
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $cn = $_POST['cn'];
      $result = 0;
      $query = 'exec [tra].[proc_validar_dias_atras]   @emp = ?
                                                    	,@per = ?
                                                    	,@ope = ?
                                                    	,@cn = ?
                                                    	,@letra = ?
                                                    	,@res = ?';

      $params = array(array(&$emp, SQLSRV_PARAM_IN)
                    , array(&$per, SQLSRV_PARAM_IN)
                    , array(&$ope, SQLSRV_PARAM_IN)
                    , array(&$cn, SQLSRV_PARAM_IN)
                    , array(&$anterior, SQLSRV_PARAM_IN)
                    , array(&$result, SQLSRV_PARAM_OUT));
      //$stmt = $com->_create_stmt($cnn, $query, $params);
      $stmt= sqlsrv_query($cnn, $query, $params);
      if( $stmt === false ) {
        $resp['status'] = 'error';
        $resp['errores'] = sqlsrv_errors();
        $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
        $resp['post'] = $_POST;
      }else{
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['result'] = $result;
      }//end if
    }elseif(isset($_POST['action'],$_POST['ope'],$_POST['anterior'],$_POST['letra'], $_POST['emp'], $_POST['per'], $_POST['cn'])&& $_POST['action'] === 'validar::letra::lista') {

      $id_operator = $_SESSION['id_operator'];
      $ope = $_POST['ope'];
      $anterior = $_POST['anterior'];
      $letra = $_POST['letra'];
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $cn = $_POST['cn'];
      $result = 0;
      $aus = '';
      $msg = '';
      $query = 'exec [tra].[proc_validar_candados_letra]   @letra_anterior = ?
                                                        	,@per = ?
                                                        	,@emp = ?
                                                        	,@cn = ?
                                                        	,@letra = ?
                                                        	,@ope = ?
                                                        	,@aus = ?
                                                        	,@result = ?
                                                        	,@msg = ?';

      $params = array(array(&$anterior, SQLSRV_PARAM_IN)
										, array(&$per, SQLSRV_PARAM_IN)
                    , array(&$emp, SQLSRV_PARAM_IN)
                    , array(&$cn, SQLSRV_PARAM_IN)
                    , array(&$letra, SQLSRV_PARAM_IN)
                    , array(&$ope, SQLSRV_PARAM_IN)
                    , array(&$aus, SQLSRV_PARAM_OUT)
                    , array(&$result, SQLSRV_PARAM_OUT)
                    , array(&$msg, SQLSRV_PARAM_OUT));
      //$stmt = $com->_create_stmt($cnn, $query, $params);
      $stmt= sqlsrv_query($cnn, $query, $params);
      if( $stmt === false ) {
        $resp['status'] = 'error';
        $resp['errores'] = sqlsrv_errors();
        $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
        $resp['post'] = $_POST;
      }else{
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['aus'] = $aus;
        $resp['msg'] = $msg;
        $resp['result'] = $result;
      }//end if

    }elseif(isset($_POST['action'],$_POST['letra'], $_POST['emp'], $_POST['per'], $_POST['cn'])&& $_POST['action'] === 'insert::letra') {
      $id_operator = $_SESSION['id_operator'];
      $letra = $_POST['letra'];
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $cn = $_POST['cn'];

      /*$query = 'exec cat.proc_get_permiso_by_letra ?, ? ,?';

      $params = array(array(&$letra, SQLSRV_PARAM_IN)
										, array(&$id_operator, SQLSRV_PARAM_IN)
                    , array(&$result, SQLSRV_PARAM_OUT));
      //$stmt = $com->_create_stmt($cnn, $query, $params);
      $stmt= sqlsrv_query($cnn, $query, $params);
      if( $stmt === false ) {
        //echo "Error in executing statement 3.\n";
        $resp['status'] = 'error';
        $resp['errores'] = sqlsrv_errors();
        $resp['msg'] = '1. Error de Programación contacte al administrador del Sistema';
        $resp['post'] = $_POST;
      }else{
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt($stmt);
        if($result == 1){
          $result = 0;
          $query = 'exec cat.proc_get_permiso_letra_by_count ?, ?,?, ?';

          $params = array(array(&$per, SQLSRV_PARAM_IN)
    										, array(&$emp, SQLSRV_PARAM_IN)
                        , array(&$letra, SQLSRV_PARAM_IN)
                        , array(&$result, SQLSRV_PARAM_OUT));
          //$stmt = $com->_create_stmt($cnn, $query, $params);
          $stmt = sqlsrv_query($cnn, $query, $params);
          if( $stmt === false ) {
            $resp['status'] = 'error';
            $resp['errores'] = sqlsrv_errors();
            $resp['msg'] = '2. Error de Programación contacte al administrador del Sistema';
          }else{
            sqlsrv_next_result($stmt);
            sqlsrv_free_stmt($stmt);
            if($result == 1){*/
              $result = 0;
              $color = '';
              $msg = '';
              $query = "exec tra.proc_set_ausentismo
                          	 @per =?
                          	,@emp = ?
                          	,@cn = ?
                          	,@letra = ?
                          	,@ope = ?
                          	,@cau = ".(isset($_POST['cau'])?'?':'null')."
                            ,@coment = ".(isset($_POST['coment'])?'?':'null')."
                            ,@fec = ".(isset($_POST['fec'])?'?':'null')."
                          	,@color = ?
                          	,@result = ?
                          	,@msg = ?";

              $params = array(array(&$per, SQLSRV_PARAM_IN)
        										, array(&$emp, SQLSRV_PARAM_IN)
                            , array(&$cn, SQLSRV_PARAM_IN)
                            , array(&$letra, SQLSRV_PARAM_IN)
                            , array(&$id_operator, SQLSRV_PARAM_IN));
              if (isset($_POST['cau'])){
                $causa = $_POST['cau'];
                array_push($params, array(&$causa, SQLSRV_PARAM_IN));
              }//end if
              if (isset($_POST['coment'])){
                $coment = $_POST['coment'];
                array_push($params, array(&$coment, SQLSRV_PARAM_IN));
              }//end if
              if (isset($_POST['fec'])){
                $fec = $_POST['fec'];
                array_push($params, array(&$fec, SQLSRV_PARAM_IN));
              }//end if
              array_push($params, array(&$color, SQLSRV_PARAM_OUT));
              array_push($params, array(&$result, SQLSRV_PARAM_OUT));
              array_push($params, array(&$msg, SQLSRV_PARAM_OUT));
              //$stmt = $com->_create_stmt($cnn, $query, $params);
              $stmt = sqlsrv_query($cnn, $query, $params);
              if( $stmt === false ) {
                $resp['status'] = 'error';
                $resp['errores'] = sqlsrv_errors();
                $resp['msg'] = '2. Error de Programación contacte al administrador del Sistema';
              }else{
                sqlsrv_next_result($stmt);
                sqlsrv_free_stmt($stmt);
                $resp['status'] = 'ok';
                $resp['letra'] = $letra;
                $resp['color'] = $color;
                $resp['msg'] = $msg;
                $resp['result'] = $result;
              }//end if
        /*    }else{
              $resp['status'] = 'error';
              $resp['letra'] = $letra;
              $resp['msg'] = 'Ha llegado al limite de calificaciones permitidas con la letra. ';
            }//end if
          }//end if
        }else{
          $resp['status'] = 'error';
          $resp['letra'] = $letra;
          $resp['msg'] = 'Operator sin permisos sobre el ausentismo.';
          $resp['post'] = $_POST;
        }//end if

      }//end if*/
    }elseif(isset($_POST['action'],$_POST['ope'])&&$_POST['action']==='get_departamentos_for_set'){
      $ope = $_POST['ope'];
      $query = 'exec cat.proc_get_departamentos_for_lista @ope = ?';
      $params= array(array(&$ope, SQLSRV_PARAM_IN));
      $stmt = $com->_create_stmt($cnn, $query, $params);
      if($stmt){
        $arry = array();
        while ($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
          array_push($arry , $row);
        }//end while
        sqlsrv_free_stmt( $stmt);
        $resp['status'] = 'ok';
        $resp['arreglo'] = $arry;
      }else{
        $resp['status'] = 'error';
        //$resp['query']= $query;
        $resp['msg'] = 'Error Ejecución.';
        $resp['error'] = sqlsrv_errors();
        $resp['post'] = $_POST;
      }//end if
      //$resp['post'] = $_POST;
    }elseif(isset($_POST['action'],$_POST['per'],$_POST['dep'],$_POST['page'], $_POST['dias'])&&$_POST['action']=='lista:horas:extras'){
      $per = $_POST['per'];
      $dep = $_POST['dep'];
      $page = intval($_POST['page']);
      $c = intval($_POST['dias']);
      $query = 'exec [tra].[proc_create_horas_by_dep] @pagenum = ?, @per = ?, @dep =? ';
      $params = array(array(&$page, SQLSRV_PARAM_IN)
                      ,array(&$per, SQLSRV_PARAM_IN)
                      ,array(&$dep, SQLSRV_PARAM_IN));
      if($stmt = $com->_create_stmt($cnn, $query, $params)){

        $html='';
        $i=0;
        $create = new _creates();
        $pages = 0;
        while( $value = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
          if ($i===0) $pages = $value['_pages'];
          $i++;
          $html.="	<div id='".$value['id_horas_extras']."' data-id='$i'
                data-periodo='".$value['id_periodo']."'
                data-employee='".$value['id_employee']."'
                data-_alter_id='".$value['_alter_id']."'
                data-code='".$value['_posicion_name']."'
                data-name='".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."'
                class='fm bloque empl row'>
              <div class='floL fm cell alter enlinea elli id'>".$value['_alter_id']."</div>
              <div class='floL fm cell nombre enlinea elli name'>".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."</div>
              <div class='floL fm cell posicion enlinea elli ".($c > 15 ? 'last16': ($c === 13? 'last13': 'last'))."'>".$value['_posicion_name']."</div>
              <div tabindex='1' class='der floL fm cell dia enlinea work waves-effect'><span class='fm tran-bez-5s border-interno'>". $value['_pagadas'] ."</span></div>
              <div tabindex='2'
                                 data-cn='_c01'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea'><span class='fm tran-bez-5s border-interno'>". (intval($value['_p01'])>0?intval($value['_p01']):'-') ."</span></div>
              <div tabindex='3'
                                 data-cn='_c02'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea'><span class='fm tran-bez-5s border-interno'>". (intval($value['_p02'])>0?intval($value['_p02']):'-') ."</span></div>
              <div tabindex='4'
                                 data-cn='_c03'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea'><span class='fm tran-bez-5s border-interno'>". (intval($value['_p03'])>0?intval($value['_p03']):'-') ."</span></div>
              <div tabindex='5'
                                 data-cn='_c04'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p04'])>0?intval($value['_p04']):'-') ."</span></div>
              <div tabindex='6'
                                 data-cn='_c05'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p05'])>0?intval($value['_p05']):'-') ."</span></div>
              <div tabindex='7'
                                 data-cn='_c06'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p06'])>0?intval($value['_p06']):'-') ."</span></div>
              <div tabindex='8'
                                 data-cn='_c07'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p07'])>0?intval($value['_p07']):'-') ."</span></div>
              <div tabindex='9'
                                 data-cn='_c08'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p08'])>0?intval($value['_p08']):'-') ."</span></div>
              <div tabindex='10'
                                 data-cn='_c09'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p09'])>0?intval($value['_p09']):'-') ."</span></div>
              <div tabindex='11'
                                 data-cn='_c10'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p10'])>0?intval($value['_p10']):'-') ."</span></div>
              <div tabindex='12'
                                 data-cn='_c11'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p11'])>0?intval($value['_p11']):'-') ."</span></div>
              ";if($c >= 12){$html.="
              <div tabindex='13'
                                  data-cn='_c12'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p12'])>0?intval($value['_p12']):'-') ."</span></div>
              ";}if($c >= 13){$html.="
              <div tabindex='14'
                                 data-cn='_c13'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p13'])>0?intval($value['_p13']):'-') ."</span></div>
              ";}if($c >= 14){$html.="
              <div tabindex='15'
                                 data-cn='_c14'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p14'])>0?intval($value['_p14']):'-') ."</span></div>
              ";}if($c >= 15){$html.="
              <div tabindex='16'
                                 data-cn='_c15'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p15'])>0?intval($value['_p15']):'-') ."</span></div>";
              }if($c === 16){
                  $html.="<div tabindex='17'
                                     data-cn='_c16'
                                     data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p16'])>0?intval($value['_p16']):'-') ."</span></div>";
              }//end if
            $html.="</div>";
          }//end while
          sqlsrv_free_stmt( $stmt);
          $resp['status'] = 'ok';
          $resp['html'] = $html;
          $resp['pages'] = $pages;
      }else{
        if(sqlsrv_errors()){
          $resp['status'] = 'error';
          //$resp['query']= $query;
          $resp['msg'] = 'Error Ejecución.';
          $resp['error'] = sqlsrv_errors();
          $resp['post'] = $_POST;
        }else{
          $resp['status'] = 'ok';
          $resp['msg'] = 'No rows...';
        }//end if
      }//endif
    }elseif(isset($_POST['action'],$_POST['ope'],$_POST['page'],$_POST['dias'])&&$_POST['action']=='pagination'){
      $ope = $_POST['ope'];
      $page = $_POST['page'];
      $c = $_POST['dias'];
      $dep = $_POST['dep'];
      $per = $_POST['per'];
      $c=0;
      if(isset($_POST['per'])){
        $query = 'exec cat.proc_create_etiquetas_lista @per = ?';
        $params = array(&$per);
        $stmt = $com->_create_stmt($cnn, $query, $params);
        $columnas='';
        if($stmt){
          $rows = array();
          while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
            array_push($rows, $row);
            $c++;
          }//end while
          $columnas.= "<div id='list-filter-cont' class='floL fs hide closed cur-poi'>
                      <div id='filter-ico-cont' class='fs floL enlinea waves-effect'><i class='fa fa-1x fa-filter' ></i></div>
                      <div id='filter-inp-cont' class='fs floL enlinea'>
                        <input id='filter-input' type='text' class='fs'
                               data-source='#rows-body'	/>
                      </div>
                  </div>";
          $columnas.= "<div id='code' data-orden-sou='alter' data-orden-dir='desc' data-orden-tip='numerico' class='floL fs colm enlinea id'><span class='fs eti'>C&oacute;digo</span><i class='fa removable fa-1x fa-angle-down'></i></div>
                  <div id='name' data-orden-sou='nombre' data-orden-dir='asc' data-orden-tip='alfabetico' class='floL fs colm enlinea name'><span class='fs eti'>Nombre </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>
                  <div id='posi' data-orden-sou='posicion' data-orden-dir='asc' data-orden-tip='alfabetico' data-count='$c' class='floL fs colm enlinea elli ".($c > 15 ? 'last16': ($c === 13? 'last13': 'last'))."'><span class='fs eti'>Posici&oacute;n </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>
                  <div id='work' class='floL fs colm enlinea work'><span class='fs eti'>W </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>";

          $c=0;
          foreach ($rows as $row => $value) {
            $c++;
            $columnas.= "<div id='dias' data-dia='$c' class='floL fs colm dia enlinea' title='".$value['_title']."'><span class='fs eti'>".$value['_etiqueta']." </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>";
          }//end while
          sqlsrv_free_stmt( $stmt);
        }//end if
      }//end if


      $query = "exec [tra].[proc_create_lista_by_ope] @ope = ?, @pagenum = ? ".(isset($_POST['dep'])? ', @dep = ?': ', null')." ".(isset($_POST['per'])? ', @per = ?': ', null')." ";

      $params = array(array(&$ope, SQLSRV_PARAM_IN)
                      ,array(&$page, SQLSRV_PARAM_IN));
      if(isset($_POST['dep'])) array_push($params, array(&$dep,SQLSRV_PARAM_IN));
      if(isset($_POST['per'])) array_push($params, array(&$per,SQLSRV_PARAM_IN));

      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        $html='';
        $i=0;
        $create = new _creates();
        while( $value = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
          $i++;
          $html.="	<div id='".$value['id_lista']."' data-id='$i'
                data-periodo='".$value['id_periodo']."'
                data-employee='".$value['id_employee']."'
                data-_alter_id='".$value['_alter_id']."'
                data-code='".$value['_posicion_name']."'
                data-name='".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."'
                class='fm bloque empl row'>
              <div class='floL fm cell alter enlinea elli id'>".$value['_alter_id']."</div>
              <div class='floL fm cell nombre enlinea elli name'>".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."</div>
              <div class='floL fm cell posicion enlinea elli ".($c > 15 ? 'last16': ($c === 13? 'last13': 'last'))."'>".$value['_posicion_name']."</div>
              <div tabindex='1' class='der floL fm cell dia enlinea work waves-effect'><span class='fm tran-bez-5s border-interno'>". $value['_days'] ."</span></div>
              <div tabindex='2'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c01']))."' data-cn='_c01'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c01']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c01'] ."</span></div>
              <div tabindex='3'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c02']))."' data-cn='_c02'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c02']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c02'] ."</span></div>
              <div tabindex='4'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c03']))."' data-cn='_c03'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c03']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c03'] ."</span></div>
              <div tabindex='5'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c04']))."' data-cn='_c04'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c04']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c04'] ."</span></div>
              <div tabindex='6'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c05']))."' data-cn='_c05'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c05']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c05'] ."</span></div>
              <div tabindex='7'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c06']))."' data-cn='_c06'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c06']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c06'] ."</span></div>
              <div tabindex='8'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c07']))."' data-cn='_c07'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c07']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c07'] ."</span></div>
              <div tabindex='9'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c08']))."' data-cn='_c08'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c08']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c08'] ."</span></div>
              <div tabindex='10'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c09']))."' data-cn='_c09'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c09']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c09'] ."</span></div>
              <div tabindex='11'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c10']))."' data-cn='_c10'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c10']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c10'] ."</span></div>
              <div tabindex='12'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c11']))."' data-cn='_c11'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c11']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c11'] ."</span></div>
              ";if($c >= 12){$html.="
              <div tabindex='13'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c12']))."' data-cn='_c12'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c12']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c12'] ."</span></div>
              ";}if($c >= 13){$html.="
              <div tabindex='14'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c13']))."' data-cn='_c13'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c13']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c13'] ."</span></div>
              ";}if($c >= 14){$html.="
              <div tabindex='15'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c14']))."' data-cn='_c14'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c14']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c14'] ."</span></div>
              ";}if($c >= 15){$html.="
              <div tabindex='16'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c15']))."' data-cn='_c15'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c15']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c15'] ."</span></div>";
              }if($c == 16){
                  $html.="<div tabindex='17'
                                     data-color='".($create->_color_from_letra($cnn, $com, $value['_c16']))."' data-cn='_c16'
                                     data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c16']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c16'] ."</span></div>";
              }//end if
            $html.="</div>";
        }//end while
        sqlsrv_free_stmt( $stmt);
        $pages = 0;
				$query ="exec tra.proc_get_pages_lista_bydep @ope = ?, @pages = ?, @dep = ? ".(isset($_POST['per'])? ',@per = ?':'') ."";
				$params = array(array(&$ope, SQLSRV_PARAM_IN)
											, array(&$pages, SQLSRV_PARAM_OUT,SQLSRV_PHPTYPE_INT)
                      , array(&$dep, SQLSRV_PARAM_IN));
        if(isset($_POST['per'])) array_push($params, array(&$per, SQLSRV_PARAM_IN));
				$stmt_pages= sqlsrv_query($cnn, $query, $params);
				if ($stmt_pages === true) sqlsrv_next_result($stmt_pages);
				sqlsrv_free_stmt($stmt_pages);
        //----------------------------------------------------------------


        $resp['status'] = 'ok';
        $resp['html'] = $html;
        $resp['pages'] = $pages;
        $resp['columnas'] = $columnas;
        $resp['dias'] = $c;
      }else{
        if(sqlsrv_errors()){
          $resp['status'] = 'error';
          //$resp['query']= $query;
          $resp['msg'] = 'Error Ejecución.';
          $resp['error'] = sqlsrv_errors();
          $resp['post'] = $_POST;
        }else{
          $resp['status'] = 'ok';
          $resp['msg'] = 'No rows...';
        }//end if
      }//end if

    }elseif(isset($_POST['action'],$_POST['ope'],$_POST['per'],$_POST['emp'],$_POST['cn'])&&$_POST['action']=='insert::lista::horas-extras'){
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $cn = $_POST['cn'];
      $ope = $_POST['ope'];
      $val = intval($_POST['val']);
      $msg= '';
      $res = 0;
      $pagadas = 0;
      $query = 'exec tra.proc_insert_horas_extras @per = ?
                                                 ,@emp = ?
                                                 ,@ope = ?
                                                 ,@cn = ?
                                                 ,@val = ?
                                                 ,@msg = ?
                                                 ,@res = ?
                                                 ,@pagadas = ?';
      $params = array(array(&$per, SQLSRV_PARAM_IN)
                      ,array(&$emp, SQLSRV_PARAM_IN)
                      ,array(&$ope, SQLSRV_PARAM_IN)
                      ,array(&$cn, SQLSRV_PARAM_IN)
                      ,array(&$val, SQLSRV_PARAM_IN)
                      ,array(&$msg, SQLSRV_PARAM_OUT)
                      ,array(&$res, SQLSRV_PARAM_OUT)
                      ,array(&$pagadas, SQLSRV_PARAM_OUT));

      $stmt = sqlsrv_query($cnn, $query, $params);
      if($stmt){
        sqlsrv_next_result($stmt);
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['pagadas'] = $pagadas;
        $resp['msg'] = $msg;
        $resp['res'] = $res;
      }else{
        $resp['stmt'] = $stmt;
        $resp['status'] = 'error';
        $resp['error'] = sqlsrv_errors();
      }//end if
      $resp['post'] = $_POST;
    }elseif(isset($_POST['action'],$_POST['per'],$_POST['emp'],$_POST['cn'])&&$_POST['action']=='get::lista::tooltip'){

      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $cn = $_POST['cn'];
      $create = new _creates();
      $title = $create->_get_title($cnn,$com,$per, $emp ,$cn);
      if ($title !== 'error'){

        $html="<div class='fn cell-tooltip abajo'>
                <div class='fn bloque flecha-cont-up visible'><i class='fa fa-1x fa-caret-up'></i></div>
                <div class='fs bloque txt elli'>$title</div>
                <div class='fn bloque flecha-cont-down oculto'><i class='fa fa-1x fa-caret-down'></i></div>
              </div>";
        $resp['status'] = 'ok';
        $resp['html'] = $html;
      }else{
        $resp['status'] = 'error';
        $resp['msg'] = 'Error Ejecución.';
        //$resp['error'] = sqlsrv_errors();
        $resp['post'] = $_POST;
      }//end if

    }elseif(isset($_POST['action'],$_POST['per'],$_POST['emp'],$_POST['ope'],$_POST['dias'])&&$_POST['action']=='get::lista::employee'){
      $ope = $_POST['ope'];
      //$page = $_POST['page'];
      $c = $_POST['dias'];
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $c=0;
      if(isset($_POST['per'])){
        $query = 'exec cat.proc_create_etiquetas_lista @per = ?';
        $params = array(&$per);
        $stmt = $com->_create_stmt($cnn, $query, $params);
        $columnas='';
        if($stmt){
          $rows = array();
          while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
            array_push($rows, $row);
            $c++;
          }//end while
          $columnas.= "<div id='list-filter-cont' class='floL fs hide closed cur-poi'>
                      <div id='filter-ico-cont' class='fs floL enlinea waves-effect'><i class='fa fa-1x fa-filter' ></i></div>
                      <div id='filter-inp-cont' class='fs floL enlinea'>
                        <input id='filter-input' type='text' class='fs'
                               data-source='#rows-body'	/>
                      </div>
                  </div>";
          $columnas.= "<div id='code' data-orden-sou='alter' data-orden-dir='desc' data-orden-tip='numerico' class='floL fs colm enlinea id'><span class='fs eti'>C&oacute;digo</span><i class='fa removable fa-1x fa-angle-down'></i></div>
                  <div id='name' data-orden-sou='nombre' data-orden-dir='asc' data-orden-tip='alfabetico' class='floL fs colm enlinea name'><span class='fs eti'>Nombre </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>
                  <div id='posi' data-orden-sou='posicion' data-orden-dir='asc' data-orden-tip='alfabetico' data-count='$c' class='floL fs colm enlinea elli ".($c > 15 ? 'last16': ($c === 13? 'last13': 'last'))."'><span class='fs eti'>Posici&oacute;n </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>
                  <div id='work' class='floL fs colm enlinea work'><span class='fs eti'>W </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>";

          $c=0;
          foreach ($rows as $row => $value) {
            $c++;
            $columnas.= "<div id='dias' data-dia='$c' class='floL fs colm dia enlinea' title='".$value['_title']."'><span class='fs eti'>".$value['_etiqueta']." </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>";
          }//end while
          sqlsrv_free_stmt( $stmt);
        }//end if
      }//end if


      //$query = "exec [tra].[proc_create_lista_by_ope] @ope = ?, @pagenum = ? ".(isset($_POST['dep'])? ', @dep = ?': ', null')." ".(isset($_POST['per'])? ', @per = ?': ', null')." ";
      $query = 'exec tra.proc_get_lista_by_emp_by_per @emp = ? ,@per = ? , @ope = ?';
      $params = array(array(&$emp, SQLSRV_PARAM_IN)
                      ,array(&$per, SQLSRV_PARAM_IN)
                      ,array(&$ope, SQLSRV_PARAM_IN));
      //if(isset($_POST['dep'])) array_push($params, array(&$dep,SQLSRV_PARAM_IN));
      //if(isset($_POST['per'])) array_push($params, array(&$per,SQLSRV_PARAM_IN));

      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        $html='';
        $i=0;
        $create = new _creates();
        while( $value = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
          $i++;
          $html.="	<div id='".$value['id_lista']."' data-id='$i'
                data-periodo='".$value['id_periodo']."'
                data-employee='".$value['id_employee']."'
                data-_alter_id='".$value['_alter_id']."'
                data-code='".$value['_posicion_name']."'
                data-name='".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."'
                class='fm bloque empl row'>
              <div class='floL fm cell alter enlinea elli id'>".$value['_alter_id']."</div>
              <div class='floL fm cell nombre enlinea elli name'>".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."</div>
              <div class='floL fm cell posicion enlinea elli ".($c > 15 ? 'last16': ($c === 13? 'last13': 'last'))."'>".$value['_posicion_name']."</div>
              <div tabindex='1' class='der floL fm cell dia enlinea work waves-effect'><span class='fm tran-bez-5s border-interno'>". $value['_days'] ."</span></div>
              <div tabindex='2'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c01']))."' data-cn='_c01'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c01']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c01'] ."</span></div>
              <div tabindex='3'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c02']))."' data-cn='_c02'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c02']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c02'] ."</span></div>
              <div tabindex='4'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c03']))."' data-cn='_c03'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c03']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c03'] ."</span></div>
              <div tabindex='5'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c04']))."' data-cn='_c04'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c04']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c04'] ."</span></div>
              <div tabindex='6'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c05']))."' data-cn='_c05'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c05']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c05'] ."</span></div>
              <div tabindex='7'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c06']))."' data-cn='_c06'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c06']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c06'] ."</span></div>
              <div tabindex='8'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c07']))."' data-cn='_c07'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c07']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c07'] ."</span></div>
              <div tabindex='9'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c08']))."' data-cn='_c08'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c08']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c08'] ."</span></div>
              <div tabindex='10'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c09']))."' data-cn='_c09'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c09']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c09'] ."</span></div>
              <div tabindex='11'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c10']))."' data-cn='_c10'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c10']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c10'] ."</span></div>
              <div tabindex='12'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c11']))."' data-cn='_c11'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c11']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c11'] ."</span></div>
              ";if($c >= 12){$html.="
              <div tabindex='13'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c12']))."' data-cn='_c12'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c12']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c12'] ."</span></div>
              ";}if($c >= 13){$html.="
              <div tabindex='14'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c13']))."' data-cn='_c13'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c13']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c13'] ."</span></div>
              ";}if($c >= 14){$html.="
              <div tabindex='15'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c14']))."' data-cn='_c14'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c14']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c14'] ."</span></div>
              ";}if($c >= 15){$html.="
              <div tabindex='16'
                                 data-color='".($create->_color_from_letra($cnn, $com, $value['_c15']))."' data-cn='_c15'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c15']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c15'] ."</span></div>";
              }if($c == 16){
                  $html.="<div tabindex='17'
                                     data-color='".($create->_color_from_letra($cnn, $com, $value['_c16']))."' data-cn='_c16'
                                     data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c16']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c16'] ."</span></div>";
              }//end if
            $html.="</div>";
        }//end while
        sqlsrv_free_stmt( $stmt);
        $pages = 1;
				/*$query ="exec tra.proc_get_pages_lista_bydep @ope = ?, @pages = ?, @dep = ? ".(isset($_POST['per'])? ',@per = ?':'') ."";
				$params = array(array(&$ope, SQLSRV_PARAM_IN)
											, array(&$pages, SQLSRV_PARAM_OUT,SQLSRV_PHPTYPE_INT)
                      , array(&$dep, SQLSRV_PARAM_IN));
        if(isset($_POST['per'])) array_push($params, array(&$per, SQLSRV_PARAM_IN));
				$stmt_pages= sqlsrv_query($cnn, $query, $params);
				if ($stmt_pages === true) sqlsrv_next_result($stmt_pages);
				sqlsrv_free_stmt($stmt_pages);*/
        //----------------------------------------------------------------


        $resp['status'] = 'ok';
        $resp['html'] = $html;
        $resp['pages'] = $pages;
        $resp['columnas'] = $columnas;
        $resp['dias'] = $c;
      }else{
        if(sqlsrv_errors()){
          $resp['status'] = 'error';
          //$resp['query']= $query;
          $resp['msg'] = 'Error Ejecución.';
          $resp['error'] = sqlsrv_errors();
          $resp['post'] = $_POST;
        }else{
          $resp['status'] = 'ok';
          $resp['msg'] = 'No rows...';
        }//end if
      }//end if
    }elseif(isset($_POST['action'],$_POST['per'],$_POST['emp'],$_POST['ope'],$_POST['dias'])&&$_POST['action']=='get::horas::employee'){
      $ope = $_POST['ope'];
      //$page = $_POST['page'];
      $c = $_POST['dias'];
      $emp = $_POST['emp'];
      $per = $_POST['per'];
      $c=0;
      if(isset($_POST['per'])){
        $query = 'exec cat.proc_create_etiquetas_lista @per = ?';
        $params = array(&$per);
        $stmt = $com->_create_stmt($cnn, $query, $params);
        $columnas='';
        if($stmt){
          $rows = array();
          while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
            array_push($rows, $row);
            $c++;
          }//end while
          $columnas.= "<div id='list-filter-cont' class='floL fs hide closed cur-poi'>
                      <div id='filter-ico-cont' class='fs floL enlinea waves-effect'><i class='fa fa-1x fa-filter' ></i></div>
                      <div id='filter-inp-cont' class='fs floL enlinea'>
                        <input id='filter-input' type='text' class='fs'
                               data-source='#rows-body'	/>
                      </div>
                  </div>";
          $columnas.= "<div id='code' data-orden-sou='alter' data-orden-dir='desc' data-orden-tip='numerico' class='floL fs colm enlinea id'><span class='fs eti'>C&oacute;digo</span><i class='fa removable fa-1x fa-angle-down'></i></div>
                  <div id='name' data-orden-sou='nombre' data-orden-dir='asc' data-orden-tip='alfabetico' class='floL fs colm enlinea name'><span class='fs eti'>Nombre </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>
                  <div id='posi' data-orden-sou='posicion' data-orden-dir='asc' data-orden-tip='alfabetico' data-count='$c' class='floL fs colm enlinea elli ".($c > 15 ? 'last16': ($c === 13? 'last13': 'last'))."'><span class='fs eti'>Posici&oacute;n </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>
                  <div id='work' class='floL fs colm enlinea work'><span class='fs eti'>W </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>";

          $c=0;
          foreach ($rows as $row => $value) {
            $c++;
            $columnas.= "<div id='dias' data-dia='$c' class='floL fs colm dia enlinea' title='".$value['_title']."'><span class='fs eti'>".$value['_etiqueta']." </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>";
          }//end while
          sqlsrv_free_stmt( $stmt);
        }//end if
      }//end if


      //$query = "exec [tra].[proc_create_lista_by_ope] @ope = ?, @pagenum = ? ".(isset($_POST['dep'])? ', @dep = ?': ', null')." ".(isset($_POST['per'])? ', @per = ?': ', null')." ";
      $query = 'exec tra.proc_get_horas_by_emp_by_per @emp = ? ,@per = ? , @ope = ?';
      $params = array(array(&$emp, SQLSRV_PARAM_IN)
                      ,array(&$per, SQLSRV_PARAM_IN)
                      ,array(&$ope, SQLSRV_PARAM_IN));
      //if(isset($_POST['dep'])) array_push($params, array(&$dep,SQLSRV_PARAM_IN));
      //if(isset($_POST['per'])) array_push($params, array(&$per,SQLSRV_PARAM_IN));

      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        $html='';
        $i=0;
        $create = new _creates();
        while( $value = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
          $i++;
          $html.="	<div id='".$value['id_horas_extras']."' data-id='$i'
                data-periodo='".$value['id_periodo']."'
                data-employee='".$value['id_employee']."'
                data-_alter_id='".$value['_alter_id']."'
                data-code='".$value['_posicion_name']."'
                data-name='".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."'
                class='fm bloque empl row'>
              <div class='floL fm cell alter enlinea elli id'>".$value['_alter_id']."</div>
              <div class='floL fm cell nombre enlinea elli name'>".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."</div>
              <div class='floL fm cell posicion enlinea elli ".($c > 15 ? 'last16': ($c === 13? 'last13': 'last'))."'>".$value['_posicion_name']."</div>
              <div tabindex='1' class='der floL fm cell dia enlinea work waves-effect'><span class='fm tran-bez-5s border-interno'>". $value['_pagadas'] ."</span></div>
              <div tabindex='2'
                                 data-cn='_c01'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea'><span class='fm tran-bez-5s border-interno'>". (intval($value['_p01'])>0?intval($value['_p01']):'-') ."</span></div>
              <div tabindex='3'
                                 data-cn='_c02'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea'><span class='fm tran-bez-5s border-interno'>". (intval($value['_p02'])>0?intval($value['_p02']):'-') ."</span></div>
              <div tabindex='4'
                                 data-cn='_c03'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea'><span class='fm tran-bez-5s border-interno'>". (intval($value['_p03'])>0?intval($value['_p03']):'-') ."</span></div>
              <div tabindex='5'
                                 data-cn='_c04'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p04'])>0?intval($value['_p04']):'-') ."</span></div>
              <div tabindex='6'
                                 data-cn='_c05'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p05'])>0?intval($value['_p05']):'-') ."</span></div>
              <div tabindex='7'
                                 data-cn='_c06'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p06'])>0?intval($value['_p06']):'-') ."</span></div>
              <div tabindex='8'
                                 data-cn='_c07'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p07'])>0?intval($value['_p07']):'-') ."</span></div>
              <div tabindex='9'
                                 data-cn='_c08'
                                 data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p08'])>0?intval($value['_p08']):'-') ."</span></div>
              <div tabindex='10'
                                 data-cn='_c09'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p09'])>0?intval($value['_p09']):'-') ."</span></div>
              <div tabindex='11'
                                 data-cn='_c10'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p10'])>0?intval($value['_p10']):'-') ."</span></div>
              <div tabindex='12'
                                 data-cn='_c11'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p11'])>0?intval($value['_p11']):'-') ."</span></div>
              ";if($c >= 12){$html.="
              <div tabindex='13'
                                  data-cn='_c12'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p12'])>0?intval($value['_p12']):'-') ."</span></div>
              ";}if($c >= 13){$html.="
              <div tabindex='14'
                                 data-cn='_c13'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p13'])>0?intval($value['_p13']):'-') ."</span></div>
              ";}if($c >= 14){$html.="
              <div tabindex='15'
                                 data-cn='_c14'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p14'])>0?intval($value['_p14']):'-') ."</span></div>
              ";}if($c >= 15){$html.="
              <div tabindex='16'
                                 data-cn='_c15'
                                 data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p15'])>0?intval($value['_p15']):'-') ."</span></div>";
              }if($c === 16){
                  $html.="<div tabindex='17'
                                     data-cn='_c16'
                                     data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea '><span class='fm tran-bez-5s border-interno'>". (intval($value['_p16'])>0?intval($value['_p16']):'-') ."</span></div>";
              }//end if
            $html.="</div>";
        }//end while
        sqlsrv_free_stmt( $stmt);
        $pages = 1;
				/*$query ="exec tra.proc_get_pages_lista_bydep @ope = ?, @pages = ?, @dep = ? ".(isset($_POST['per'])? ',@per = ?':'') ."";
				$params = array(array(&$ope, SQLSRV_PARAM_IN)
											, array(&$pages, SQLSRV_PARAM_OUT,SQLSRV_PHPTYPE_INT)
                      , array(&$dep, SQLSRV_PARAM_IN));
        if(isset($_POST['per'])) array_push($params, array(&$per, SQLSRV_PARAM_IN));
				$stmt_pages= sqlsrv_query($cnn, $query, $params);
				if ($stmt_pages === true) sqlsrv_next_result($stmt_pages);
				sqlsrv_free_stmt($stmt_pages);*/
        //----------------------------------------------------------------


        $resp['status'] = 'ok';
        $resp['html'] = $html;
        $resp['pages'] = $pages;
        $resp['columnas'] = $columnas;
        $resp['dias'] = $c;
      }else{
        if(sqlsrv_errors()){
          $resp['status'] = 'error';
          //$resp['query']= $query;
          $resp['msg'] = 'Error Ejecución.';
          $resp['error'] = sqlsrv_errors();
          $resp['post'] = $_POST;
        }else{
          $resp['status'] = 'error';
          $resp['msg'] = 'No rows...';
        }//end if
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
