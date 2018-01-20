<?php
  header('Content-type: application/json');
  if (isset($_POST['ifc']) && $_POST['ifc'] === 'by_hits') {
    include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
  	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";

    $com = new com_mssql();
  	$cnn = $com->_conectar_win(HOST,DATA);

    $resp['count'] = 0;
    $div = "<div class='fm element'>
             <div class=\"fm header bloque\">
               <div class=\"fm enlinea status verde\"><i class='fa fa-1x fa-check-circle'></i></div>
               <div class=\"fm enlinea title\">Previamente Sincronizado...</div>
             </div>
            </div>";
    $resp['div'] =  $div;
    while($rows = _while_hits($cnn, $com)){
      $row = 0;
      foreach ($rows as $key => $row) {
        $query_name = $row['_query_name'];
        $ifc = $row['id_interface'];
        $ifc_name = $row['_interface_name'];
        $need_op = $row['_need_op'];
        $query = $com->_get_val($cnn, '_query_script', 'adm.queries', '_query_name', $query_name , 'nvarchar(256)' ,1);
        $result = 0;
        //echo $need_op;
        if ($need_op === 1){
          $ope = $com->_get_val($cnn, 'operator_id', 'cat.operator', '_username','administrator', 'char(36)' ,1);
          $params = array(array(&$ope, SQLSRV_PARAM_IN)
    										, array(&$ifc, SQLSRV_PARAM_IN)
                        , array(&$result, SQLSRV_PARAM_OUT));
        }else{
          $params = array(array(&$ifc, SQLSRV_PARAM_IN)
                        , array(&$result, SQLSRV_PARAM_OUT ));
        }//end if
        //$stmt = $com->_create_stmt($cnn, $query, $params);

        $stmt2 = sqlsrv_query($cnn, $query, $params);

        if($stmt2 == true) {
          //sqlsrv_next_result($stmt);
          //$arow['result'] = $result;
          $resp['count']++;
          $arow['ifc'] = $ifc;
          $resp['interfaces'][$ifc_name] = $arow;
          sqlsrv_free_stmt($stmt2);
          $query = "exec ifc.proc_get_last_event_by_ifc ?";
          $params = array(array(&$ifc, SQLSRV_PARAM_IN));
          $stmt3 = $com->_create_stmt($cnn,$query, $params);
          if($stmt3 == true){
            while($row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)){
              $resp['interfaces'][$ifc_name]['data'] = $row;
              $div = "<div class='fm element'>
                <div class=\"fm header bloque\">
                  <div class=\"fm enlinea status ".($row['type'] == 1? "verde":"rojo")."\"><i class='fa fa-1x ".($row['type'] == 1? "fa-check-circle":"fa-times-circle")."'></i></div>
                  <div class=\"fm enlinea title\">$ifc_name</div>
                </div>
                <div class=\"fm data bloque\">
                  <div class=\"fm item bloque\">
                      <div class=\"fs item-eti enlinea\">Inserted:</div>
                      <div class=\"fs item-val enlinea\">".$row['inserted']."</div>
                  </div>
                  <div class=\"fm item bloque\">
                      <div class=\"fs item-eti enlinea\">Updated:</div>
                      <div class=\"fs item-val enlinea\">".$row['updated']."</div>
                  </div>
                  <div class=\"fm item bloque\">
                      <div class=\"fs item-eti enlinea\">Secs:</div>
                      <div class=\"fs item-val enlinea\">".$row['dif']."</div>
                  </div>
                  <div class=\"fm item bloque\">
                      <div class=\"fs item-eti enlinea\">Inicio:</div>
                      <div class=\"fs item-val enlinea\">".$row['ini']."</div>
                  </div>
                  <div class=\"fm item bloque\">
                      <div class=\"fs item-eti enlinea\">Final:</div>
                      <div class=\"fs item-val enlinea\">".$row['fin']."</div>
                  </div>
                </div>
              </div>";
              $resp['interfaces'][$ifc_name]['div'] = $div;
            }//end while
          }else{
            $resp['interfaces'][$ifc_name]['data'] = sqlsrv_errors();
          }//if
          //echo $data;
          //$arow['data'] = trim($data);
          //$resp['stmt'] = print_r($stmt);
          //$arow['query'] = $query;
        }else{
          $arow['status'] = 'error';
          $arow['errores'] = sqlsrv_errors();
          $arow['params'] = $params;
          $arow['post'] = $_POST;
          $arow['msg'] = '1. Error de Programación contacte al administrador del Sistema';
          $arow['ifc'] = $ifc;
          $resp['interfaces'][$ifc_name] = $arow;
        }//end if
      }// for each
      //$resp['status'] = 'ok';
    }//end while rows
    $com->_desconectar($cnn);
    echo json_encode($resp);
  }else{
    $resp['status'] = 'error';
    $resp['msg'] = '1. error de posteo';
    echo json_encode($resp);
  }//end if isset

  function _while_hits($cnn, $com){
    $query = $com->_get_val($cnn, '_query_script', 'adm.queries', '_query_name','ifc_by_hits', 'nvarchar(256)' ,1);

    //echo $query;
    $param = array();
    $stmt = $com->_create_stmt($cnn,$query,$param);
    if($stmt){
      $rows = array();
      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        array_push($rows, $row);
      }//end while
      sqlsrv_free_stmt($stmt);
      return $rows;
    }else{
      return false;
    }//end if
  }//end function
?>
