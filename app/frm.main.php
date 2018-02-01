<?php

	//echo dirname(__FILE__);
	include_once $_SERVER['DOCUMENT_ROOT']."/login/class.login.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.creates.php";
	//Conexion a Base de Datos
	$com = new com_mssql();
	$cnn = $com->_conectar_win(HOST,DATA);

	//Obtener Valores de la Base de Datos
	$lifetime = $com->_get_param($cnn,  'session_lifetime'); // in minutes
	$raiz = $com->_get_param($cnn,  'raiz');

	$clogin = new _login();
	$clogin->iniciar_sesion('TimeIO', $lifetime);
	$logeado = 0;

	if(isset($_GET['solicitud'])) $solicitud = $_GET['solicitud'];

	if($clogin->_logeado()){
		$logeado = 1;
		$id_operator = $_SESSION['id_operator'];
		$user_name = $_SESSION['user_name'];
		$user_sing = $_SESSION['user_sing'];
		$user_ldap = $_SESSION['user_ldap'];
		$ip = $_SERVER['REMOTE_ADDR'];
		//$hostname = gethostbyaddr($ip);
	}else{
		//$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
		//$root2 = 'http://srrmpspr.aicollection.local/';
		if(isset($_GET['solicitud'])) $raiz.= '?solicitud='.$_GET['solicitud'];
		header("Location: $raiz");
		//exit();
	}//end if

?>

<?php if ($logeado === 1): ?>

<!DOCTYPE html>
	<html>
		<head>
			<title>Time.io - Main</title>
			<link rel="shortcut icon" href="/favicon.ico?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/favicon.ico');?>" />
			<link rel="icon" href="/favicon.ico?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/favicon.ico');?>" />
			<link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css"/>
			<link rel="stylesheet" type="text/css" media="screen" href="/css/animaciones.css" />
			<link rel="stylesheet" type="text/css" media="screen" href="/css/jquery-ui.css">
			<link rel="stylesheet" type="text/css" media="screen" href="/css/particles.css?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/css/particles.css');?>"/>
			<link rel="stylesheet" type="text/css" media="screen" href="/css/main.css?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/css/main.css');?>"/>
			<link rel="stylesheet" type="text/css" media="screen" href="/css/style.colores.css">
			<link rel="stylesheet" type="text/css" media="screen" href="/css/style.buttons.css">
			<link rel="stylesheet" type="text/css" media="screen" href="/css/waves.css">
			<link rel="stylesheet" type="text/css" media="screen" href="/css/jstree/style.min.css">


			<script src="/scripts/jquery.min.js"></script>
			<script src="/scripts/particles.min.js"></script>
			<script src="/scripts/jquery-ui.js"></script>
			<script src="/scripts/jstree.min.js"></script>
			<script src="/scripts/materialize.min.js"></script>

			<script src="/scripts/jquery.main.js?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.main.js');?>"></script>
			<script src="/scripts/jquery.list.js?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.list.js');?>"></script>
			<script src="/scripts/jquery.usuarios.js?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.usuarios.js');?>"></script>
			<script src="/scripts/jquery.interfaces.js?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.interfaces.js');?>"></script>
			<script src="/scripts/jquery.posiciones.js?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.posiciones.js');?>"></script>
			<script src="/scripts/jquery.solicitud.js?v=<?php echo md5_file($_SERVER['DOCUMENT_ROOT'].'/scripts/jquery.solicitud.js');?>"></script>


		</head>
		<body class='main oculto' data-lifetime='<?php echo $lifetime * 60000; ?>'>
			<div id="wrapper" class="completo">
				<div id='sidebar' class="left25 completo tema-darkblue-part noselect" >

					<div id="brand" class='fh45 bloque completo-h45 tema-darkblue-brand'>
						<div id="brand-title" class='fh45 fg enlinea hide'>
							<!--<span class=' fg azul-f1'>io</span>1-->
							<img id='time-io-logo-main' src="..\imagenes\time.io.logo.main.png" alt="">
						</div>
						<div id="brand-collp" class='fh45 fg enlinea' data-collp='0'>
							<div class="nwrap">
								<span class="nav-line"></span>
								<span class="nav-line"></span>
								<span class="nav-line"></span>
								<span class="nav-line-mid"></span>
							</div>

						</div>
					</div>

					<div id="menu" class='particulas fn bloque ini-hide'>

							<?php
								//echo 'Error';
								//echo $user_sing;
								$create = new _creates();
								$create->_menu($cnn, $com, strtolower($user_sing));
							?>

						<div class="menu-footer bloque ">

						</div>
					</div>

					<div id="client" class='bloque hide'>
						<img id='img-client' src="/imagenes/client.png" >
					</div>
				</div>
				<div id="board">
					<div id="head" class="tema-darkblue-head bloque">
						<div id="frm_wrapper" class="enlinea">
							<div id="titulo" class="enlinea fm noselect pad-l20">Dashboard</div>
							<div id="notify" class="item enlinea fm noselect icon-head waves-effect" > <i class="fa fa-1x fa-bell-o"></i></div>
							<div id="sesion" class="item enlinea fm noselect sesi-head waves-effect" data-id='<?php //echo $id_operator;?>'>
								<div class="sesi-txt fm enlinea elli floL pad-l10 tooltip" title='<?php echo $user_ldap; ?>'><?php echo $user_name;?></div>
								<div class="sesi-ico fm enlinea floL pad-l10"><i class="fa fa-1x fa-user-o"></i></div>
							</div>
							<div id='sesion-panel' class="fn bloque oculto">
								<div class="item fn bloque">
									<div class='fn txt enlinea'>Perfil</div>
									<div class='fn enlinea foto'></div>
								</div>
								<span class='fs separa bloque'></span>
								<div id='logout' class="item fn bloque">
									<a id='a-logout' class='fn txt enlinea' href="/logout.php">Logout </a>
									<i class="fa enlinea fa-1x fa-sign-out"></i>
								</div>
							</div>
							<div id="extras" class="item enlinea fm noselect icon-head waves-effect oculto" ><i class="fa fa-1x fa-ellipsis-v"></i></div>
						</div>

					</div>
					<div id="content" class="bloque  noselect">
						<div id="frm_wrapper" class="enlinea tema-darkblue-forms">
							<!--Ajax!-->
							<?php
								if(isset($_GET['solicitud'])) {
									echo $solicitud;
								}

							?>


							<!--/jax!-->
						</div>

						<div id="tools_wrapper" class="enlinea fm no-visible ">
							<div id='' class="completo"></div>
								<div id='particulas' class="fm admin-wrapper-bg"></div>
								<div class="fm admin-wrapper">
									<div id='cnn-container' class='tool-box fm bloque'>
										<div class="fm title"> Conexiones
										</div>
										<div class="fm data">
											<div class="fm cargando">
												<div class="fm gris"><i class='fa fa-4x fa-cog fa-spin'></i>	</div>

											</div>

										</div>
									</div>
									<div id='ifc-container' class='tool-box fm bloque'>
										<div class="fm title bloque"> Interfaces</div>
										<div class="fm data bloque">
											<div class="fm cargando">
												<div class="fm gris"><i class='fa fa-4x fa-cog fa-spin'></i>	</div>

											</div>

										</div>
									</div>
								</div>

						</div>

						<div id="notificaciones" class='fn transparent'>


						</div>
					</div>

				</div>
			</div>
		</body>
	</html>
<?php else: ?>

<?php endif; ?>
