<?php
	header('Content-type: application/json');
	//echo dirname(__FILE__);
  include_once $_SERVER['DOCUMENT_ROOT']."/login/class.login.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.creates.php";

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
				$html = "<div id='frm-".$id_navigator."' data-op='".$id_operator."' data-nav='".$id_navigator."' data-table='".boolval($tabla)."' data-query='".boolval($query)."' class='frm fn boxshadow mar10 oculto'>";
				$html.= " <div id='frm-tabs' class='fn floL enlinea '>
                    <div data-tab='frm-add' class='fn tab isfrm bloque waves-effect active' data-title='Agregar Columnas'>
                      <i class='fa fa-1x fa-plus'></i>
                    </div>
				          </div>";


        $html.="  <div id='frm-add' class='fs istab floL enlinea visible'>";
        $html.="    <div id='gpo-wrapper' class='fn outer bloque'><div class='inner fn bloque'>";
        //-----------------------------------
        // Search Operator
        //-----------------------------------
        $second = array(array('_schema_name','Schema'),array('_object_name', 'Objeto'));
        $qf = 'objects_by_item_name';
        $qs = 'objects_by_name';
        $cols= array('object_id', '_item_name','_schema_name','_object_name');
        $callback = 0;

        $html.=$create->_search('frm-add','Columnas','_item_name', 'text', $second, $qf, $qs, $cols,'object_id',$callback,0, 'Columnas');
        //-----------------------------------
        $html.= "   </div></div>"; //frm-ause outer inner
        $html.= " </div>";//frm-ause
        $html.= "</div>"; //frm-".$id_navigator."
				$resp['html'] = $html;
				$resp['status'] = 'ok';
				$resp['msg'] = 'Usar html';
			}else{
				$hmtl = "<div id='frm_permisos' class='frm fm boxshadow mar10'>
    							<div id='permisos-wrapper'>
    								<div id='sinpermisos' class='fn bloque rojo'><i class='fa fa-4x fa-exclamation-triangle'></i></div>
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
