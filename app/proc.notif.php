<?php 
	header('Content-type: application/json');
	if (isset($_POST['proc'],$_POST['msg'],$_POST['ico'], $_POST['tipo']) && $_POST['proc'] = 'show') {
		include_once '../includes/constantes.php';
		include_once '../includes/class.mssql.php';
		//include_once '../includes/class.menu.php';
			
		//Conexion a Base de Datos
		$com = new com_mssql();
		$cnn = $com->_conectar_win(HOST,DATA);

		$ubi = ($com->_get_param($cnn,  'notif_ubi') ?: 'bottom'); 
		$delay = ($com->_get_param($cnn,  'notif_delay') ?: 5000); 
		$com->_desconectar($cnn);
		$id = uniqid('notif_');
		$div = "<div id='".$id."' class=' oculto fn notif-item notif-".$_POST['tipo']."'>
					<div class='fn mid ico cua floL enlinea'>
						<i class='fa fa-1x ".$_POST['ico']."'></i>
					</div>
					<div class='fn mid msg elli floL enlinea' title='".$_POST['msg']."'>
						".$_POST['msg']."
					</div>
					<div class='fn mid btn cua floL enlinea waves-effect'>
						<i class='fa fa-1x fa-times'></i>
					</div>
				</div>";
		$resp['id'] = $id;		
		$resp['div'] = $div;
		$resp['ubi'] = $ubi;
		$resp['delay'] = $delay;
		echo json_encode($resp);
	}//end if
?>