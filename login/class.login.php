<?php    

	
	
	class _login{
		public function iniciar_sesion($app, $min) {
		    $session_name = $app;   // Set a custom session name 
		    $secure = false; // falla en true, falta revisar a fondo por que...

		    // This stops JavaScript being able to access the session id.
		    $httponly = true;

		    // Forces sessions to only use cookies.
		    if (ini_set('session.use_only_cookies', 1) === FALSE) {
		       	echo "[error] - No se pudo inicializar las cookies";
		        exit();
		    }//end if

		    // Gets current cookies params.
		    $cookieParams = session_get_cookie_params();
		    $cookieParams["lifetime"] = 60 * ((int)preg_replace("/[^\d]+/","",$min)); 
		    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);

		    // Sets the session name to the one set above.
		    session_name($session_name);

		    // Start the PHP session 
		    session_start();
		    // regenerated the session, delete the old one. 			
		    session_regenerate_id();
		}// end function


		public function _logeado() {
			//return true;
			if(isset($_SESSION['user_ldap'],$_SESSION['user_sing'])) {
				return true;
			} else {
				//No conectado
				return false;
			}//end if
		}//end function

	}//end class

?>