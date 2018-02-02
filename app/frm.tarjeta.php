
<?php
	header('Content-type: application/json');
	header('Content-type: text/html; charset=UTF-8');
	//echo dirname(__FILE__);
	include_once '../login/class.login.php';
	include_once '../includes/constantes.php';
	include_once '../includes/class.mssql.php';
	include_once '../includes/class.creates.php';
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
			//$tabla = $com-> _get_val($cnn, '_table', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);
			//$query = $com-> _get_val($cnn, '_insert_query', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);
			$_full = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_full'));
			$_read = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_read'));
			$_write = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_write'));
			$_special = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_special'));

			$tabla = 'tra.tarjeta';
			//echo $_full;
			$permisos = $_full + $_read + $_write + $_special;
			//echo $permisos;
			$user_name = $_SESSION['user_name'];
			$user_sing = $_SESSION['user_sing'];
			$user_ldap = $_SESSION['user_ldap'];
			$ip = $_SERVER['REMOTE_ADDR'];

			$query ='exec cat.proc_get_periodo_actual ?';
			//$query ='{call cat.proc_get_periodo_bydate( ?, ? )}';
			//$date = (new \DateTime())->format('Ymd');
			//echo $date;
			$id_per = '';
			$params = array(array(&$id_per, SQLSRV_PARAM_OUT,SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR), SQLSRV_SQLTYPE_UNIQUEIDENTIFIER));
			$stmt= sqlsrv_query($cnn, $query, $params);
			if( $stmt === false ) {
				//echo "Error in executing statement 3.\n";
				$resp['status'] = 'error';
				$resp['errores'] = sqlsrv_errors();
				$resp['msg'] = 'Error de Programación contacte al administrador del Sistema';
				$com->_desconectar($cnn);
				echo json_encode($resp);
			}else{
				sqlsrv_next_result($stmt);
				sqlsrv_free_stmt($stmt);
				$query = 'exec cat.proc_get_datos_periodo ?';
				$params = array(&$id_per);
				$stmt = $com->_create_stmt($cnn, $query, $params);
				if($stmt === false){
					$resp['status'] = 'error';
					$resp['errores'] = sqlsrv_errors();
					$resp['msg'] = 'Error de Programación contacte al administrador del Sistema';
					$com->_desconectar($cnn);
					echo json_encode($resp);
				}else{
					while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
						$per = $row['_per'];
						$days = $row['_days'];
						$year = $row['_year'];
						$year_per = $row['_year_per'];
					}//end while
				}//end if
			}//end if

      $html='';
			if(intval($permisos) > 0){
        //------------------------------------------------------------------
        $html.= "<div id='frm-$id_navigator'
                      data-op='".$id_operator."'
                      data-nav='".$id_navigator."'
                      data-table='".boolval($tabla)."'
                      class='frm add-has-options frm-tarjeta fn boxshadow mar10 oculto'>";
			    $html.= "<div id='frm-cont-tarjeta' class='fn  bloque'>";
            $html.= "<div id='tarjeta-select' class='fn  bloque boxshadow'>";
              $html.= "<div id='select-pic' class='fn enlinea floL'>";
                $html.= "<img id='pic-col' src='/imagenes/no_image_profile.jpg' />";
              $html.= "</div>";
              $html.= "<div id='select-opt' class='fn enlinea floL'>";
                $html.= "<div class='gpo fn bloque'>";
                  $html.= "<div class='ico fn enlinea floL'>
                            <i class='fa fa-1x fa-hashtag'></i>
                          </div>";
                  $html.= "<div class='inp fn enlinea floL'>
                            <input id='_alter_id' data-emp='' class='fn enlinea floL'/>
														<button id='buscar-emp' class='fn tn-buscar enlinea floL waves-effect'> <i class='fa fa-1x fa-search' ></i></button>
                          </div>";
                $html.= "</div>";
                $html.= "<div class='gpo fn bloque'>";
                  $html.= "<div class='ico fn enlinea floL'>
                            <i class='fa fa-1x fa-calendar'></i>
                          </div>";
                  $html.= "<div class='inp fn enlinea floL'>";
										$html.="<div id='cont-periodo' class='fn floL has-options contenedor enlinea closed'>";
											$html.="<div tabindex='0' id='cont-roles-title' class='fn title bloque' data-parent='#cont-periodo'>";
											 $html.="<div id='txt' class='fn floL enlinea' data-parent='#cont-periodo'>
																<input id='id-per' data-id='' type='text' class='toupper fn integrado' data-parent='#cont-periodo' />
															</div>";
											 $html.="<div tabindex='0' id='ico' class='fn floL enlinea' data-parent='#cont-periodo'><i class='fa fa-1x fa-ellipsis-h' data-parent='#cont-periodo'></i></div>";
											$html.="</div>";
											$html.="<div id='options' class='fn select bloque thin-scroll oculto' data-parent='#cont-periodo'>";

											$query = 'exec cat.proc_list_periodos';
											$params = array();
											 if($stmt = $com->_create_stmt($cnn, $query, $params)){
												 while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
													 $html.= "<div data-id='".$row['id_periodo']."'
													 								class='fn option bloque'
																					data-ini='".$row['_ini']."'
																					data-fin='".$row['_fin']."'
																					data-label='".$row['_label']."'
																					data-parent='#cont-periodo'>".$row['_label']." - ".$row['_ini']." - ".$row['_fin']."</div>";
												 }// end while
											 }//end if
											$html.="</div>";
										$html.="</div>";

                  $html.= "</div>";
                $html.= "</div>";
              $html.= "</div>";
            $html.= "</div>";
            $html.= "<div id='tarjeta-data' class='fn bloque'>";
							$html.= "<div id='tarjeta-data-tabs' class='fn enlinea'>";

							$html.= "</div>";
							$html.= "<div id='tarjeta-data-rows' class='fn enlinea'>";
								$html.= "<div id='data-rows-head' class='fn bloque'>";

								$html.= "</div>";
								$html.= "<div id='data-rows-body' class='fn bloque boxshadow'>";

								$html.= "</div>";
							$html.= "</div>";
            $html.= "</div>";
          $html.= "</div>";
        $html.= "</div>";
        //------------------------------------------------------------------
				$resp['html'] = $html;
				$resp['status'] = 'ok';
				$resp['msg'] = 'Usar html';
			}else{
				$html = "<div id='frm_permisos' class='frm fm boxshadow mar10'>
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
