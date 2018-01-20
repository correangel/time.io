<?php
	header('Content-type: application/json');
	//echo dirname(__FILE__);
	include_once $_SERVER['DOCUMENT_ROOT']."/login/class.login.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.creates.php";
	//include_once '../includes/class.menu.php';


	//Conexion a Base de Datos
	$com = new com_mssql();
	$cnn = $com->_conectar_win(HOST,DATA);
	$lifetime = $com->_get_param($cnn,  'session_lifetime'); // in minutes

	//Login
	$clogin = new _login();
	$clogin->iniciar_sesion('TimeIO', $lifetime);
	//echo $_POST['id_nav'];
	if($clogin->_logeado()){
		if(!isset($_POST['id_nav']) && !isset($_POST['id_nav_proc'])  ){
			$resp['status'] = 'error';
			$resp['msg'] = '1.Error de Posteo...';
			$com->_desconectar($cnn);
			echo json_encode($resp);
			return;
		}//end if

		$create = new _creates();
		$id_navigator = (isset($_POST['id_nav'])?$_POST['id_nav']:$_POST['id_nav_proc']);
		$id_operator = $_SESSION['id_operator'];
		$_full = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_full'));
		$_read = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_read'));
		$_write = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_write'));
		$_special = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_special'));
		//echo $id_navigator;
		if(isset($_POST['id_nav'])) {

      $tabla = $com-> _get_val($cnn, '_table', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);
      $query = $com-> _get_val($cnn, '_insert_query', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);
			//echo $_full;
			$permisos = 0;
			$permisos = $_full + $_read + $_write + $_special;
			//echo $permisos;
			$user_name = $_SESSION['user_name'];
			$user_sing = $_SESSION['user_sing'];
			$user_ldap = $_SESSION['user_ldap'];
			$ip = $_SERVER['REMOTE_ADDR'];


			if(intval($permisos)>0){
      #---------------------------------------------------------------------------------------------------------------
      # Inicia Contenido especifico del elemento.
      #---------------------------------------------------------------------------------------------------------------
      $html = "<div id='frm-".$id_navigator."' data-ope='".$id_operator."' data-nav='".$id_navigator."' data-table='".boolval($tabla)."' data-query='".boolval($query)."' class='frm fn boxshadow mar10 oculto'>";
				$html.="<div id='frm-tabs-head' class='fs'>";
					$html.="<div id='tab-loca' class='fs floL enlinea'>
										<input data-source='#frm-vert-tabs' id='inp-loca' type='text' class='fs enlinea filtra' placeholder='Locación'/></div>";
					$html.="<div id='tab-depa' class='fs floL enlinea'>
										<input data-source='#tabs-dep-rows' id='inp-depa' type='text' class='fs enlinea filtra' placeholder='Departamento'/></div>";
					$html.="<div id='tab-posi' class=' floL enlinea'>
										<input data-source='#tabs-pos-rows' id='inp-posi' type='text' class='fs enlinea filtra' placeholder='Posición'/>
										<div id='lab-fe' class='lab fs enlinea ' title='Festivos'>FE</div>
										<div id='lab-pd' class='lab fs enlinea ' title='Prima Dominical'>PD</div>
										<div id='lab-he' class='lab fs enlinea ' title='Horas Extra'>HE</div>
										<div id='lab-hn' class='lab fs enlinea ' title='Horas Nocturnas'>HN</div>
										<div id='lab-em' class='lab fs enlinea ' title='Empleados Activos'>EM</div>
									</div>";
				$html.="</div>"; // frm-tabs-head
				$html.="<div id='frm-vert-tabs' class='fs enlinea floL'>";
          $query = "exec cat.proc_get_list_elements
                    	@table = ?,
                    	@columns = ?,
                    	@where = null,
                    	@active= 1,
                    	@distinct= 1,
                      @verbose = 0,
	                    @orderby = '_locacion_name asc'";
          $table = 'cat.locacion with(nolock)';
          $error = array();
          $columns = 'locacion_id, _locacion_code, _locacion_name';
          $params=array(array(&$table, SQLSRV_PARAM_IN), array(&$columns, SQLSRV_PARAM_IN));
          $stmt = $com->_create_stmt($cnn,$query, $params);
          if ($stmt){
            while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
              $html.="<div class='fs vtab for-filtra' data-name='".$row['_locacion_name']."' data-code='".$row['_locacion_code']."' waves-effect' id='".$row['locacion_id']."'>".$row['_locacion_code']."</div>";
            }//end while
          }else{
            $error = sqlsrv_errors();
          }//end if
        $html.="</div>"; // frm-tabs
				$html.="<div id='frm-vert-tabs-dep' class='fs enlinea floL'>";
					$html.="<div class='fn cargando oculto'><i class='fa fa-2x fa-cog fa-spin'></i></div>";
					$html.="<div id='tabs-dep-rows' class='fn'></div>";
				$html.="</div>"; // frm-vert-tabs-dep
				$html.="<div id='frm-vert-tabs-pos' class='fs enlinea floL'>";
					$html.="<div class='fn cargando oculto'><i class='fa fa-2x fa-cog fa-spin'></i></div>";
					$html.="<div id='tabs-pos-rows' class='fn'></div>";
				$html.="</div>"; // frm-vert-tabs-dep
      $html.="</div>";//frm-navigator

      if(!$error){
        $resp['html'] = $html;
        $resp['status'] = 'ok';
        $resp['msg'] = 'Usar html';
      }else{
        $resp['status'] = 'error';
        $resp['error'] = $error;
      }//end if
      #---------------------------------------------------------------------------------------------------------------
      }else{
				$html = "<div id=frm_permisos´ class='frm fm boxshadow mar10'>
							<div id='permisos-wrapper'>
								<div id='sinpermisos' class='fn bloque rojo'><i class='fa fa-4x  fa-exclamation-triangle'></i></div>
								<div id='message' class='fn bloque'> Permisos insuficientes...</div>
							</div>
						</div>";
				$resp['status'] = 'ok';
				$resp['msg'] = 'Permisos Insuficientes';
				$resp['html'] = $html;
			}//endif
		}else{
			$resp['status'] = 'error';
			$resp['msg'] = 'Error de Programación contacte al administrador del Sistema';
		}//end if
  }else{
		$url = $com->_get_param($cnn, 'raiz');
		$resp['status'] = 'login';
		$resp['url'] = $url;
		$resp['msg'] = 'Sesion Caducada...';
	}//end if

	$com->_desconectar($cnn);
	echo json_encode($resp);
