<?php if(isset($_POST['user'])): ?>
	<?php
		try {
			ob_clean(); //limpia espacios para el callback de ajax
			header('Content-type: application/json');
			include_once '/class.login.php';
			$clogin = new _login();
			$clogin->iniciar_sesion('MonitoreoRCD');
			$_SESSION['user'] = $_POST['user'];
			$_SESSION['userSindominio'] = str_replace("@AICOLLECTION.LOCAL","",strtoupper($_POST['user']));
			$response_array['cook'] = true;
			$response_array['msg'] = 'Cookie creada satisfactoriamente';
			echo json_encode($response_array);
		} catch (Exception $e) {
			echo $e;
		}//end try
	?>
<?php else: ?>
	<div id="frm_login"
		data-grupo="MonitoreoRCD"
		data-llave="rsanchez!883bf9095b07"
		data-url="https://loginrcd.aicollection.local:4443/check.login.php">
		
		<label for="tb_user">User:</label>
		<input type="text" id="tb_user"  required>

		<label for="tb_pass">Password:</label>
		<input type="password" id="tb_pass" required>

		<button id="btn_login"><i class="fa fa-1x fa-user"></i> Login</button>

		<div id="results" class='error oculto'></div>
	</div>
<?php endif;?>