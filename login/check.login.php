<?php
	//JSON header
	header('Content-type: application/json');
	//Cross Domain headers
	//header('Access-Control-Allow-Origin: *');
	//header('Access-Control-Allow-Origin: https://loginrcd.aicollection.local');
	//header('Access-Control-Allow-Methods: GET, POST');
	//header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

	if (isset( $_POST['pass'],$_POST['user'])){

		include '../includes/constantes.php';
		include '../includes/class.mssql.php';
		include '../includes/class.ldap.php';

		//Manejador de Errores
		set_time_limit(30);
		//error_reporting(E_ALL);
		//ini_set('error_reporting', E_ALL);
		//ini_set('display_errors',1);
		set_error_handler('error_hand', E_ALL);

		//Variables de posteo

		$user = $_POST['user'];
		if ($user == '') {
			$response_array['login'] = 'false';
			$response_array['msg'] = 'Ingresa un Usuario';
			echo json_encode($response_array);
			return;
		}//end if
		$pass = $_POST['pass'];
		if ($pass == '') {
			$response_array['login'] = 'false';
			$response_array['msg'] = 'Ingresa un Password';
			echo json_encode($response_array);
			return;
		}//end if

		$com = new com_mssql();
		$cnn = $com->_conectar_win(HOST,DATA);

		$is_ad_user = $com->_get_val($cnn, '_ad_user', 'cat.operator', '_username', $user,'nvarchar', '1');
		if($is_ad_user == 0){
			$salt = $com->_get_val($cnn, '_salt', 'cat.operator', '_username', $user,'char(36)', '1');
			$pass_enc = base64_encode(hash('sha512',$pass.$salt));
			$id_operator = $com->_get_val($cnn, 'operator_id', 'cat.operator', '_password', $pass_enc,'varchar(max)', '1');
			if($id_operator){
				$lifetime = $com->_get_param($cnn, 'session_lifetime');
				$name = $com->_get_val($cnn, '_name', 'cat.operator', '_username', $user,'nvarchar', '1');
				$name .= " " . $com->_get_val($cnn, '_lastname', 'cat.operator', '_username', $user,'nvarchar','1');
				$com->_desconectar($cnn);
				$response_array['login'] = 'true';
				$response_array['name'] = $name;
				$response_array['lifetime'] = $lifetime;
				$response_array['id_operator'] = $id_operator;
				$response_array['msg'] = 'Autenticado en DB';
				$response_array['user'] = $user;
				echo json_encode($response_array);
				return;
			}else{
				$com->_desconectar($cnn);
				//$response_array['salt'] = $salt;
				//$response_array['pass'] = $pass_enc;
				//$response_array['id_operator'] = $id_operator;
				$response_array['login'] = 'false';
				$response_array['msg'] = 'Credenciales Inv&aacute;lidas';
				echo json_encode($response_array);
				return;
			}//end if
		}//end if

		$id_operator = $com->_get_val($cnn, 'operator_id', 'cat.operator', '_username', $user,'varchar(max)', '1') ?: '';
		if ($id_operator == '') {
			$response_array['login'] = 'false';
			$response_array['msg'] = 'Ingresa un Usuario valido';
			echo json_encode($response_array);
			return;
		}//end if
		//Conexion a Base de Datos





		//Obtener Valores de la Base de Datos
		//$salt = $com->_call_proc($cnn, 'get_param', 'salt' );

		//$prev = $com->_call_proc($cnn, 'get_param', 'prev');
		//$llave =  $com->_call_proc($cnn, 'get_key', $member256);
		$dominio = $com->_get_val($cnn, '_domain', 'cat.operator', '_username', $user,'nvarchar','1');// ?: $com->_get_param($cnn, 'dominio_cliente');
		$host = $com->_get_val($cnn
													, '_host'
													, 'adm.dominios'
													, 'id_dominio'
													, $dominio
													, 'nvarchar'
													, '1');

		//echo $dominio;
		//echo $host;
		//$reprev = $com->previa($prev,'sha256', $member256 ,-12);

		//ldap class
		$ldap = new _ldap();
		$ad_user= $user.'@'.$dominio;

		//echo $user;
		//Validaciones
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
			$grupo = $com->_get_val($cnn
														, '_grupo'
														, 'adm.dominios'
														, 'id_dominio'
														, $dominio
														, 'nvarchar'
														, '1');
			$lifetime = $com->_get_param($cnn, 'session_lifetime');
			$name = $com->_get_val($cnn, '_name', 'cat.operator', '_username', $user,'nvarchar', '1');
			$name .= " " . $com->_get_val($cnn, '_lastname', 'cat.operator', '_username', $user,'nvarchar','1');


			if ( $grupo <> '' && $grupo <> 'undefined' ) {
				//$bdn= $com->_get_param($cnn, 'base_dn');
				$bdn = $com->_get_val($cnn
															, '_base_dn'
															, 'adm.dominios'
															, 'id_dominio'
															, $dominio
															, 'nvarchar'
															, '1');
				//$ous= $com->_get_param($cnn, 'OU_grupos');
				$ous = $com->_get_val($cnn
															, '_ou_grupo'
															, 'adm.dominios'
															, 'id_dominio'
															, $dominio
															, 'nvarchar'
															, '1');
				$output = $ldap->check_grupo($ad, $grupo,$bdn, $ous ,$user);
				//$response_array['output'] = $output;
				//$output =true;
				if($output){
					if (_insert_log($cnn, $com, $id_operator)){
						$com->_desconectar($cnn);
						$response_array['login'] = 'true';
						$response_array['name'] = $name;
						$response_array['lifetime'] = $lifetime;
						$response_array['id_operator'] = $id_operator;
						$response_array['msg'] = 'Autenticado en el Grupo de Seguridad';
						$response_array['user'] = $ad_user;
						$user = str_replace("@AICOLLECTION.LOCAL","",strtoupper($ad_user));
						$user = str_replace("@UNICOHOTEL.LOCAL","",strtoupper($user));
						$response_array['userSindominio'] =$user;

						$response_array['grupo'] = $grupo;
						echo json_encode($response_array);
					}else{
						$response_array['login'] = 'false';
						$response_array['msg'] = 'Error insert log';
						echo json_encode($response_array);
					}//end if
				}else{
					$response_array['login'] = 'false';
					$response_array['msg'] = 'Usuario no pertenece al grupo de Seguridad';
					echo json_encode($response_array);
				}//end if
			}else{
				if (_insert_log($cnn, $com, $id_operator)){
					$com->_desconectar($cnn);
					$response_array['login'] = 'true';
					$response_array['name'] = $name;
					$response_array['lifetime'] = $lifetime;
					$response_array['id_operator'] = $id_operator;
					$response_array['msg'] = 'Autenticado en el AD';
					$response_array['user'] = $ad_user;
					echo json_encode($response_array);
				}else{
					$response_array['login'] = 'false';
					$response_array['msg'] = 'Error insert log';
					echo json_encode($response_array);
				}//end if
			}//end if
		}//end if




	}else{
		$response_array['login'] = 'false';
		$response_array['msg'] = 'Sin posteo';
		echo json_encode($response_array);
	}//end if isset

	function error_hand($errno){
		if  ($errno == 2){
			$response_array['login'] = 'false';
			$response_array['msg'] = 'Credenciales inv&aacute;lidas';
			echo json_encode($response_array);
		}else{
			echo 'error';//$errmsg;
		}//end if
	}//end function

	function grupo_posteado(){
		if(isset($_POST['grupo'])){
			return $_POST['grupo'];
		}//end if
		return 0;
	}//end function

	function _insert_log($cnn, $com, $id_operator){
		try {
			$ip = $_SERVER['REMOTE_ADDR'];
			$hostname = gethostbyaddr($ip);
			$query = "[log].proc_insert_log_access @id_operator = ?, @from_ip = ?, @from_hostname = ?";
			$params = array(&$id_operator,&$ip,&$hostname);
			if($com->_exec_non_query($cnn, $query, $params)){
				return true;
			}else{
				return false;
			}//end if;
		} catch (Exception $e) {
			echo $e;
			return false;
		}//endif
	}//end function
?>
