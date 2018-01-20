<?php
  header('Content-type: application/json');
  //$resp['data'] = 'hola mundo';
  //echo json_encode($resp);
  if(isset($_POST['source'],$_POST['destino'],$_POST['rows'])){
    //---------------------------------------------------------------------------------------------------
    // Recibo Variables de Post
    $source = $_POST['source'];
    $destino = $_POST['destino'];
    $rows =  $_POST['rows'];
    //---------------------------------------------------------------------------------------------------
    // Includes de clases y constantes - Conexion básica
    include_once "../../includes/constantes.php";
    include_once "../../includes/class.mssql.php";
    //---------------------------------------------------------------------------------------------------
    // Objeto de mssql
    $com = new com_mssql();
    $cnn = $com->_conectar_win(HOST,DATA);

    //---------------------------------------------------------------------------------------------------
    // Obtengo datos de conexion a db source
    //$host_s = $com->_get_val($cnn, '_server', 'adm.conexiones', '_name', $source, 'nvarchar(32)' ,1);
    //$user_s = $com->_get_val($cnn, '_user', 'adm.conexiones', '_name', $source, 'nvarchar(32)' ,1);
    //$pass_s = $com->_get_val($cnn, '_pass', 'adm.conexiones', '_name', $source, 'nvarchar(32)' ,1);
    //$data_s = $com->_get_val($cnn, '_data', 'adm.conexiones', '_name', $source, 'nvarchar(32)' ,1);

    //---------------------------------------------------------------------------------------------------
    // Obtengo datos de conexion a db destino
    $host_d = $com->_get_val($cnn, '_server', 'adm.conexiones', '_name', $destino, 'nvarchar(32)' ,1);
    $user_d = $com->_get_val($cnn, '_user', 'adm.conexiones', '_name', $destino, 'nvarchar(32)' ,1);
    $pass_d = $com->_get_val($cnn, '_pass', 'adm.conexiones', '_name', $destino, 'nvarchar(32)' ,1);
    $data_d = $com->_get_val($cnn, '_data', 'adm.conexiones', '_name', $destino, 'nvarchar(32)' ,1);
    //---------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------------------------------------------
    // Conexion destino
    $cnn_d = $com->_conectar_uid($host_d,$user_d,$pass_d,$data_d);
    $ids = [];
    // Consulta de ids pendientes
    $query = 'exec ifc.proc_ids_pendientes;';
		$stmt = $com->_create_stmt($cnn_d, $query, array());
    //$stmt_dump = var_dump($stmt);
		if($stmt){
      // Recorro ids y se agregan al arreglo
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        array_push($ids,$row['id']);
      }// end while
    }// end if
    $len = count($ids);
    $c = 0;
    // Stored Procedure responsable del insert
    $query = 'exec ifc.proc_valida_handpunch @id = ?, @verbose = 0;';
    foreach ($ids as $key => $value) {
      $com->_exec_non_query($cnn_d, $query, array(&$value));
      $c++;
    }//end foreach
    $resp['len'] = $len;
    $resp['count'] = $c;
    //$resp['dump'] = $stmt_dump;
    $resp['$host_d']=$host_d;
    $resp['$user_d']=$user_d;
    //$resp['$pass_d']=$pass_d;
    $resp['$data_d']=$data_d;
    $resp['status'] = 'ok';

    $resp['source'] = $source;
    $resp['destino'] = $destino;

    // Cerrar Conexiones
    $com->_desconectar($cnn);
    $com->_desconectar($cnn_d);

    echo json_encode($resp);
  }else{
    $resp['status'] = 'error';
    $resp['msg'] = 'Error de Posteo...';
    echo json_encode($resp);
  }

  /*include_once "../includes/constantes.php";
  include_once "../includes/class.mssql.php";
  $com_morpho = new com_mssql();
  $cnn_morpho = $com->_conectar_uid($host,$user,$pass,$db);*/

?>
