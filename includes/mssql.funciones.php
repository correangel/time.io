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

		function _conectar_win($host, $db){
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

		

		function _conectar_uid($host, $user, $pass, $db){

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

		function _desconectar($cnn){
			sqlsrv_close($cnn);
		}//end _conectar
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