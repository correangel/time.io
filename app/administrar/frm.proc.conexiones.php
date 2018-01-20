<?php 
	header('Content-type: text/html');
	$act = new _priv();	
	if(isset($_POST['type'])){
		//$_POST['name'] ,$_POST['engine'] ,$_POST['server'] ,$_POST['port'] ,$_POST['user'] ,$_POST['pass'] ,$_POST['data'] ,$_POST['id_operator']
			
		switch ($_POST['type']) {
			case 'add':
				$resp = $act->_exec_add();
				break;
			case 'del':
				$resp = $act->_exec_del();
				break;
			default:
				$resp = $act->_error('Valor de posteo invalido....');				
				break;
		}//end switch
		
	}else{
		$resp = $act->_error('Falta posteo de datos...');	
	}//end if
	echo json_encode($resp);

	//--------------------------------------------------
	class _priv{
		function _exec_add(){
			include_once "../../includes/constantes.php";
			include_once "../../includes/class.mssql.php";
			if(isset($_POST['name'] ,$_POST['engine'] ,$_POST['server'] ,$_POST['port'] ,$_POST['user'] ,$_POST['pass'] ,$_POST['data'] ,$_POST['id_operator'])){
				$name = $_POST['name'];
				$engine = $_POST['engine'];
				$server = $_POST['server'];
				$port = $_POST['port'];
				$user = $_POST['user'];
				$pass = $_POST['pass'];
				$data = $_POST['data'];
				$id_operator = $_POST['id_operator'];

				$com = new com_mssql();
				$cnn = $com->_conectar_win(HOST,DATA);
				$query = "exec adm.proc_add_conexion @name = ?, @engine = ?, @server = ?, @port = ?, @user = ?, @pass = ?, @data = ?, @id_operator = ?";
				$params = array(&$name , &$engine , &$server , &$port , &$user , &$pass , &$data , &$id_operator);

				if(_exec_non_query($cnn, $query, $params)){
					$resp['status'] = 'ok';
					$resp['msg'] = 'ok';
				}else{
					$resp = $this->_error('Error query...');
				}//end if
				$com->_desconectar($cnn);
			}else{
				$resp = $this->_error('[add] - Error Post');
			}//end if
			return $resp;
		}//end function

		function _error($msg){
			$resp['msg'] = $msg;
			$resp['status'] = 'Error';
			return $resp;
		}//end function
	}//nd class
	/*----------------------
	
	$name = $_POST['name'];
	$engine = $_POST['engine'];
	$server = $_POST['server'];
	$port = $_POST['port'];
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$data = $_POST['data'];
	$id_operator = $_POST['id_operator'];
	----------------------*/
?>
