<?php

	include_once $_SERVER['DOCUMENT_ROOT']."/login/class.login.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";
	$clogin = new _login();
	$clogin->iniciar_sesion('TimeIO', 1000);
	//Desconfigura todos los valores de sesión
	$_SESSION = array();
	//Obtén parámetros de sesión
	$params = session_get_cookie_params();
	//Borra la cookie actual
	setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	//Destruye sesión
	session_destroy();

	$com = new com_mssql();
	$cnn = $com->_conectar_win(HOST,DATA);

	//Obtener Valores de la Base de Datos
	$raiz = $com->_get_param($cnn,  'raiz');
	header("Location: $raiz");
?>
