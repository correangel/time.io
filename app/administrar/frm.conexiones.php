<?php 
	//echo dirname(__FILE__);
	include_once '../../login/class.login.php';
	include_once '../../includes/constantes.php';
	include_once '../../includes/class.mssql.php';
	//include_once '../includes/class.menu.php';
	
	//Obtener Valores de la Base de Datos

	$permisos = 0;
	//echo $_POST['id_nav'];
	if(isset($_POST['id_nav'])) {
		//Conexion a Base de Datos
		$com = new com_mssql();
		$cnn = $com->_conectar_win(HOST,DATA);
		$lifetime = $com->_get_param($cnn,  'session_lifetime'); // in minutes
		$clogin = new _login();
		$clogin->iniciar_sesion('TimeIO', $lifetime);
		$id_navigator = $_POST['id_nav'];
		$id_operator = $_SESSION['id_operator'];
		//echo " - ";
		//echo $id_operator;

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
		//$hostname = gethostbyaddr($ip);
	}//end if	
	

?>


<?php if(intval($permisos) > 0): ?>
	<div id='frm-conexiones' class='frm fn boxshadow mar10'>
		
	</div>
<?php else: ?>
	<div id="frm_permisos" class='frm fm boxshadow mar10'>
		<div id="permisos-wrapper">
			<div id='sinpermisos' class='fn bloque rojo'><i class="fa fa-4x  fa-exclamation-triangle "></i></div>
			<div id='message' class='fn bloque'> Permisos insuficientes...</div>
		</div>			
	</div>		
<?php endif; ?>

 