<?php if(isset($_POST['user'], $_POST['lifetime'], $_POST['name'],$_POST['id_operator']  )): ?>
	<?php
		try {
			ob_clean(); //limpia espacios para el callback de ajax
			header('Content-type: application/json');
			include_once $_SERVER["DOCUMENT_ROOT"] ."/login/class.login.php";
			include_once $_SERVER["DOCUMENT_ROOT"] ."/includes/constantes.php";
			$user = $_POST['user'];
			$user = str_replace("@AICOLLECTION.LOCAL","",strtoupper($user));
			$user = str_replace("@UNICOHOTEL.LOCAL","",strtoupper($user));
			$lifetime = $_POST['lifetime'];
			$clogin = new _login();
			$clogin->iniciar_sesion('TimeIO', $lifetime );
			$_SESSION['id_operator'] = $_POST['id_operator'];
			$_SESSION['user_ldap'] = $_POST['user'];
			$_SESSION['user_name'] = $_POST['name'];
			$_SESSION['user_sing'] = $user;

			$response_array['cook'] = true;
			//$response_array['user_ldap'] =  $_SESSION['user_ldap'];
			//$response_array['user_sing'] = $_SESSION['user_sing'];
			$response_array['msg'] = 'Cookie creada satisfactoriamente';
			if(isset($_GET['solicitud'])) $response_array['solicitud'] = $_GET['solicitud'];
			echo json_encode($response_array);
		} catch (Exception $e) {
			echo $e;
		}//end try
	?>
<?php else: ?>

	<!DOCTYPE html>
	<html>
		<head>
			<title>Time.io - login</title>
			<link rel="shortcut icon" href="/favicon.ico?v=<?php echo md5_file('favicon.ico');?>" />
			<link rel="icon" href="/favicon.ico?v=<?php echo md5_file('favicon.ico');?>" />
			<link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css"/>
			<link rel="stylesheet" type="text/css" href="/css/particles.css"/>
			<link rel="stylesheet" type="text/css" href="/css/main.css"/>
			<link rel="stylesheet" type="text/css" href="/login/sty.login.css"/>
			<link rel="stylesheet" type="text/css" media="screen" href="/css/style.colores.css">
			<script src="/scripts/jquery.min.js"></script>
			<script src="/scripts/particles.min.js"></script>
			<script src="/login/jqy.login.js"></script>
		</head>
		<body>
			<div id="wrapper" class="completo">

				<div id="frm_login" class='fn frm_login boxshadow–big' data-url='<?php echo '/index.php'.(isset($_GET['solicitud'])? '?solicitud='.$_GET['solicitud']:''); ?>'>
					<div id="frm–logo" class='logo'>
						<img id='time-io-logo' src="imagenes\time–io–login.jpg" alt="">
					</div>
					<div id="frm–data" class='fn data'>
						<!--<label for="tb_user">User:</label>!-->
						<div id='inputs' class="fn bloque ">

							<div class="fn bloque gpo">
								<div class="fn ico enlinea floL"><i class='fa fa-1x fa-user '></i></div>
								<input class="enlinea floL" type="text" id="tb_user" required>
							</div>
							<div class="fn bloque gpo">
								<div class="fn ico enlinea floL"><i class='fa fa-1x fa-key '></i></div>
								<input class="enlinea floL" type="password" id="tb_pass" required>
							</div>
						</div>

						<button id="btn_login" class='fn bg-azul-login noerror'><i class="fa fa-1x fa-lock"></i> <span class='fn msg'>Login</span></button>

						<div id="results" class='error oculto'></div>
					</div>
				</div>
				<div id="particulas" class="particulas completo tema-gradblue-part">

				</div>

			</div>
		</body>
	</html>
<?php endif;?>
