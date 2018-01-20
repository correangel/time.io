<?php
	include_once ('class.phpmailer.php');
	include_once ('class.smtp.php');
	class mailer{
		//-----------------------------------------------------------------------------------
		// Función que integra la clase PHPmailer y SMTP para envio de correos electrónicos.
		//-----------------------------------------------------------------------------------
		public function enviar_correo($direcciones = array(), $asunto ,$cuerpo,$smtphost, $smtpport, $from){
			$correo = new PHPMailer;
			$correo->SMTPDebug = 0; 		//Muestra resultados Para hacer debug.
			//--------------------------------------------------------------------------------
			$correo->isSMTP();					//Obligamos a usar SMTP.
			$correo->Host =$smtphost;		//140.50.12.103 //Seteo el servidor SMTP (Default = Relay HR)
			$correo->Port = $smtpport;					//Seteo el puerto SMTP (Default = 25)
			$correo->SMTPAuth = false;
			$correo->isHTML(true);
			$correo->FromName = $from;
			$correo->Subject = $asunto;
			$correo->Body = $cuerpo;
			foreach ($direcciones as $dir) {
				$correo->addAddress($dir);
			}//end foreach

			if(!$correo->send()) {
			    //echo 'Mailer Error: ' . $mail->ErrorInfo;
			    return false;
			} else {
			    return true;
			}//end if

		}//end function

 	}//end class


?>
