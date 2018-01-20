<?php
  header('Content-type: application/json');

  include_once "../includes/constantes.php";
  include_once "../includes/class.mssql.php";
  $com = new com_mssql();
  $cnn = $com->_conectar_win(HOST,DATA);
  $act = new _priv();

  if(isset($_POST['type'], $_POST['op'], $_POST['nav'])){

    $op = $_POST['op'];
    $id_navigator = $_POST['nav'];
    $tabla = $com->_get_val($cnn, '_table', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);
    //$query = $com->_get_val($cnn, '_insert_query', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);

    switch ($_POST['type']) {
      case 'add':
        if(isset($_POST['arry'])){
          $arry = $_REQUEST['arry'];
          $resp = $act->_exec_add($cnn, $com, $arry, $op , $tabla);
        }else{
          $resp = $act->_error('Falta posteo de datos...');
          $act->_end($cnn, $com, $resp);
        }//end if;

        break;
      case 'del':
        if(isset($_POST['col'], $_POST['id'])){
          $col = $_POST['col'];
          $id = $_POST['id'];
          $resp = $act->_exec_del($cnn, $com, $col, $id, $op,$tabla);
        }else{
          $resp = $act->_error('Falta posteo de datos...');
          $act->_end($cnn, $com, $resp);
        }//end if;
        break;
      case 'act':
        //$resp = $act->_exec_act();
        break;
      default:
        $resp = $act->_error('Valor de posteo invalido....');
        break;
    }//end switch
    $act->_end($cnn, $com, $resp);
  }else{
    $resp = $act->_error('Falta posteo de datos...');
    $act->_end($cnn, $com, $resp);
  }//end if

  //--------------------------------------------------
  class _priv{
    function _exec_del($cnn, $com, $col, $id, $op,$tabla){

      $query = "exec [adm].[proc_delete_row_from_table] @tabla=?,@columna=? ,@id=? ,@op=?";
      $params = array(&$tabla, &$col, &$id, &$op);
      $resp = $com->_exec_non_query($cnn, $query, $params);
      if($resp['status'] == 'ok'){
        $resp['tipo'] = 'notif-info';
        $resp['ico'] = 'fa-minus-circle';
        $resp['msg'] = 'Dato eliminado correctamente...';
      }else{
        $resp = $this->_error($resp);
      }//end if
      return $resp;
    }//end function
    function _exec_add($cnn, $com, $arry, $op , $tabla){
      $cols ='';
      $vals ='';
      $first = true;
      foreach ($arry as $key => $value) {
        if($first== true) {
          $cols.= $key;
          $vals.= "'$value'";
          $first = false;
        }else{
          $cols.= ",$key";
          $vals.= ",'$value'";
        }//end if
      }//endfor
      $cols.= ",insert_operator_id";
      $vals.= ",'$op'";
      $query= "insert into $tabla($cols) values ($vals);";
      if($com->_exec_simple_query($cnn, $query)){
        $resp['status'] = 'ok';
        $resp['tipo'] = 'notif-add';
        $resp['ico'] = 'fa-check';
        $resp['msg'] = 'Dato insertado correctamente...';
      }else{
        $resp = $this->_error($query);
      }//end if
      return $resp;
    }//end function

    function _error($resp){
      $resp['all'] = $resp;
      $resp['msg'] = 'No se pudo realizar el Movimiento...';
      $resp['ico'] = 'fa-warning';
      $resp['status'] = 'Error';
      return $resp;
    }//end function

    function _end($cnn, $com, $resp){
      $com->_desconectar($cnn);
      echo json_encode($resp);
    }//end function
  }//nd class
?>
