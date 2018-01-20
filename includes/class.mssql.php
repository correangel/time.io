<?php
	/*--------------------------------------------------
		Autor: Ramón Sánchez;
		Fecha: 08/05/2014;
		RCD Resorts - Hard Rock Hotel Riviera Maya
	----------------------------------------------------
		Función:
		Clase con funciones para la comunicacion con
		la base de datos.
	--------------------------------------------------*/

	Class com_mssql {

		public function _conectar_win($host, $db){
			try {

				$connectionInfo = array( "Database"=>$db,'CharacterSet' => 'UTF-8');
				$cnn = sqlsrv_connect($host, $connectionInfo);
				//$cnn->set_charset('utf-8');
				if (!$cnn){
					throw new Exception("No se pudo realizar conexion con el servidor [$host]", 1);
				}else{
					return $cnn;
				}//end if
			} catch (Exception $e) {
				throw $e;
			}//end try
		}//end _conectar



		public function _conectar_uid($host, $user, $pass, $db){
			try {

				$connectionInfo = array( "Database"=>$db, "UID"=>$user, "PWD"=>$pass);
				$cnn = sqlsrv_connect($host, $connectionInfo);
				//$cnn->set_charset('utf-8');
				if (!$cnn){
					throw new Exception("No se pudo realizar conexion con el servidor [$host]", 1);
				}else{
					return $cnn;
				}//end if
			} catch (Exception $e) {
				throw $e;
			}//end try
		}//end _conectar

		public function _desconectar($cnn){
			sqlsrv_close($cnn);
		}//end _conectar

		public function _get_param($cnn, $parametro){
			$query = 'exec adm.proc_get_param @parametro = ?';
			$stmt = sqlsrv_prepare($cnn, $query, array(&$parametro));

			if ($stmt){
				$res = sqlsrv_execute($stmt);
				//var_dump($res);
				if(sqlsrv_has_rows( $stmt )){
					if( sqlsrv_fetch( $stmt )){
						return sqlsrv_get_field( $stmt, 0);
					}else{
						return false;//echo 'nofetch';
					}
				}else{
					return false;//echo 'norows';
				}//end if
			}else{
				return null;
			}//end if


		}// en function

		public function _exec_non_query($cnn, $query, $params){
			$stmt = sqlsrv_prepare($cnn, $query, $params);//array(&$parametro)
			if ($stmt){
				$stmt = sqlsrv_execute($stmt);
				//var_dump($stmt);
				if($stmt){
					$resp['status'] = 'ok';
					return $resp;
				}else{
					if( ($errors = sqlsrv_errors() ) != null) {
		        foreach( $errors as $error ) {
	            $resp['msg'] = $error['message'];
							$resp['error'] = $error;
		        }//end foreach
			    }//end if
					$resp['params'] = $params;
					$resp['status'] = 'error';

					return $resp;
				}//end if
			}else{
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
						$resp['msg'] = $error['message'];
						$resp['error'] = $error;
					}
				}
				$resp['params'] = $params;
				$resp['status'] = 'error';

				return $resp;
			}//end if
		}// en function
		public function _exec_simple_query($cnn, $query){
			$stmt = sqlsrv_query($cnn, $query);//array(&$parametro)
			if ($stmt){
				//var_dump($res);
				if(!sqlsrv_has_rows( $stmt )){
					return $stmt;
				}else{
					if( ($errors = sqlsrv_errors() ) != null) {
						foreach( $errors as $error ) {
							echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
							echo "code: ".$error[ 'code']."<br />";
							echo "message: ".$error[ 'message']."<br />";
						}
					}
					return false;
				}//end if
			}else{
				if( ($errors = sqlsrv_errors() ) != null) {
					foreach( $errors as $error ) {
						echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
						echo "code: ".$error[ 'code']."<br />";
						echo "message: ".$error[ 'message']."<br />";
					}
				}
				return false;
			}//end if
		}// en function
		public function _create_stmt($cnn, $query, $params){

			$stmt = sqlsrv_prepare($cnn, $query, $params);//array(&$parametro)
			if ($stmt){
				$res = sqlsrv_execute($stmt);
				//var_dump($res);
				if(sqlsrv_has_rows( $stmt ) && $res){
					return $stmt;
				}else{
					//die( print_r( sqlsrv_errors(), true));
					return false;
				}//end if
			}else{
				//die( print_r( sqlsrv_errors(), true));
				return false;
			}//end if
		}// en function
		//$name = $com->_get_val($cnn, '_name', 'cat.operator', '_username', true);

		public function _get_val($cnn, $field, $table, $where, $where_val, $type ,$active){
			$query = "exec adm.proc_get_val @field = ?, @table = ?, @where = ?, @where_val = ?, @type = ?, @active = ?";
			$params = array(&$field,&$table,&$where,&$where_val,&$type,&$active);
			$stmt = sqlsrv_prepare($cnn, $query, $params);//array(&$parametro)
			//var_dump($stmt);
			if ($stmt){
				$res = sqlsrv_execute($stmt);


				if(sqlsrv_has_rows( $stmt )){
					if( sqlsrv_fetch( $stmt )){
						return sqlsrv_get_field( $stmt, 0);
					}else{
						return false;
					}
				}else{
					if( ($errors = sqlsrv_errors() ) != null) {
						foreach( $errors as $error ) {
							echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
							echo "code: ".$error[ 'code']."<br />";
							echo "message: ".$error[ 'message']."<br />";
						}
					}
					return false;
				}//end if
			}else{
				return false;
			}//end if
		}// en function

		public function _get_permisos_nav($cnn, $id_nav, $id_op ,$field ){
			$query = "exec adm.proc_get_permiso_by_url_op @id_navigator= ?, @id_operator= ?, @field = ?;";
			$params = array(&$id_nav,&$id_op,&$field);
			$stmt = sqlsrv_prepare($cnn, $query, $params);//array(&$parametro)
			if ($stmt){
				$res = sqlsrv_execute($stmt);
				//var_dump($res);
				if(sqlsrv_has_rows( $stmt )){
					if( sqlsrv_fetch( $stmt )){
						return sqlsrv_get_field( $stmt, 0);
					}else{
						return false;
					}//end if
				}else{
					return false;
				}//end if
			}else{
				return false;
			}//end if
		}// en function

		public function _isvalid_email($email) {
		    if(is_array($email) || is_numeric($email) || is_bool($email) || is_float($email) || is_file($email) || is_dir($email) || is_int($email))
		        return false;
		    else {
		        $email=trim(strtolower($email));
		        if(filter_var($email, FILTER_VALIDATE_EMAIL)!==false) return $email;
		        else {
		            $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
		            return (preg_match($pattern, $email) === 1) ? $email : false;
		        }//end if
		    }//end if
		}//end function
		/*
		function _prepara($cnn,$query){
			try {
				return $cnn->prepare($query);
			} catch (mysqli_sql_exception $e) {
				throw $e;
			}//end try
		}//end _prepara

		function _obtener_val_txt($cnn,$tabla, $campo, $where_cl){
			try {
				$txt = "Null";
				$query = "SELECT $campo from $tabla where activo = 1 $where_cl limit 1";
				//
				//echo $query;
				$exe = $cnn->prepare($query);
				if($exe->execute()){
					$exe->store_result();
					$exe->bind_result($txt);
					$exe->fetch();
				}else{
					echo "Error al Ejecutar";
				}//end if
				return $txt;
			} catch (Exception $e) {
				return null;
			}//end try
		}//end function

		function _obtener_fecha($cnn){
			try {
				$fecha = "Null";
				$query = 'select convert(now(),date)';
				$exe = $cnn->prepare($query);
				if($exe->execute()){
					$exe->store_result();
					$exe->bind_result($fecha);
					$exe->fetch();
				}else{
					echo "Error al Ejecutar";
				}//end if
				return $fecha;
			} catch (Exception $e) {
				return null;
			}//end try
		}//end function

		function _obtener_valores_array($cnn, $tabla, $campo, $where_clause = null){
			$query = "select $campo from $tabla where 1 = 1 $where_clause";
			$result = $cnn->prepare($query);
			if ($result->execute()){
				$result->store_result();
				$result->bind_result($valor);
				$arr = array();
				while($result->fetch()){
					array_push($arr, $valor);
				}//end while
			}//end if
			return $arr;
		}//end function
		*/
	}//end Class
?>
