<?php
	header('Content-type: application/json');
	//echo dirname(__FILE__);
	include_once '../login/class.login.php';
	include_once '../includes/constantes.php';
	include_once '../includes/class.mssql.php';
	include_once '../includes/class.creates.php';
	//Test
	
	//Conexion a Base de Datos
	$com = new com_mssql();
	$cnn = $com->_conectar_win(HOST,DATA);
	$lifetime = $com->_get_param($cnn,  'session_lifetime'); // in minutes

	//Login
	$clogin = new _login();
	$clogin->iniciar_sesion('TimeIO', $lifetime);
	//echo $_POST['id_nav'];
	if($clogin->_logeado()){
		$permisos = 0;

		if(isset($_POST['id_nav'])) {
			$id_navigator = $_POST['id_nav'];
			$id_operator = $_SESSION['id_operator'];
      $tabla = $com-> _get_val($cnn, '_table', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);
      $query = $com-> _get_val($cnn, '_insert_query', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);
			$_full = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_full'));
			$_read = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_read'));
			$_write = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_write'));
			$_special = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_special'));

			//echo $_full;
			$permisos = $_full + $_read + $_write + $_special;
			//echo $permisos;
			$user_name = $_SESSION['user_name'];
			$user_sing = $_SESSION['user_sing'];
			$user_ldap = $_SESSION['user_ldap'];
			$ip = $_SERVER['REMOTE_ADDR'];


			if(intval($permisos) > 0){
				$create = new _creates();
				$html = "<div id='frm-".$id_navigator."' data-op='".$id_operator."' data-nav='".$id_navigator."' data-table='".boolval($tabla)."' data-query='".boolval($query)."' class='frm fn boxshadow mar10 oculto'>
								<div id='frm-tabs' class='fn floL enlinea '>
									<div data-tab='frm-table' class='fn tab isfrm bloque waves-effect active'>
										<i class='fa fa-1x fa-table'></i>
									</div>
									<div data-tab='frm-add' class='fn tab isfrm bloque waves-effect'>
										<i class='fa fa-1x fa-plus'></i>
									</div>
								</div>
								<div id='frm-table' class='fs istab floL enlinea visible'>
									<div id='rows-head' class='fs bloque'>";
									$html .= $create->_columnas($cnn, $com, $id_navigator, $id_operator);
									$html .= "</div><div style='width:8px;' class='tc floL fs enlinea scr'><i class='fa fa-1x fa-arrows-v'></i></div>
									<div id='rows-body' class='fm bloque'>";
											//$mn = new _menu();
											$html .= $create->_rows($cnn, $com, $id_navigator, $id_operator);
									$html .="</div>
								</div>
								<div id='frm-add' class='fS istab floL enlinea oculto'>";
								$html.=$create->_add($cnn,$com, $id_navigator, $id_operator);

								$html.="</div>
							</div>
							<div id='dialog-confirm' title='Eliminar registro?' class='fn oculto'>
									<div class='ico fn floL enlinea'><i class='fa fa-4x fa-warning'></i></div>
									<div class='txt fn floL enlinea'>El Registro se eliminará permanentemente, ¿Estás Seguro?</div>
							</div>";
				$resp['html'] = $html;
				$resp['status'] = 'ok';
				$resp['msg'] = 'Usar html';
			}else{
				$hmtl = "<div id='frm_permisos' class='frm fm boxshadow mar10'>
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
		//$hostname = gethostbyaddr($ip);
	}else{
		$url = $com->_get_param($cnn, 'raiz');
		$resp['status'] = 'login';
		$resp['url'] = $url;
		$resp['msg'] = 'Sesion Caducada...';
	}//end if


	$com->_desconectar($cnn);
	echo json_encode($resp);

?>
