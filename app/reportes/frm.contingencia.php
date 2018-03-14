
<?php
	header('Content-type: application/json');
	header('Content-type: text/html; charset=UTF-8');
	//echo dirname(__FILE__);
  include_once $_SERVER['DOCUMENT_ROOT']."/login/class.login.php";
  include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
  include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";
  include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.creates.php";
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
                      data-ope='".$id_operator."'
                      data-nav='".$id_navigator."'
                      data-table='".boolval($tabla)."'
                      class='frm add-has-options frm-contingencia fn boxshadow mar10 oculto'>";
			    $html.= "<div id='frm-cont-contingencia' class='fn  bloque'>";
            $html.= "<div id='rep-select' class='fn bloque '>";



							$html.= "<div id='select-datos' class='fn enlinea floL boxshadow '>";
								$html.= "<div id='results-cont' class='fn bloque'>";

									$query = "exec [rep].[proc_contingencia_empleados_presentes] @ope = ?";
									$params = array(array(&$id_operator,SQLSRV_PARAM_IN));


									$c=0;

									if($stmt = $com->_create_stmt($cnn, $query, $params)){
										$html.= "<table id='rep-grid' class='fs table'>
																		<thead class='fs'><tr>
																			<th class='fs' >Causa</th>
																			<th class='fs' >Codigo</th>
																			<th class='fs' >Nombre</th>
																			<th class='fs' >ApellidoPaterno</th>
																			<th class='fs' >ApellidoMaterno</th>
																			<th class='fs' >Clase</th>
																			<th class='fs' >Locacion</th>
																			<th class='fs' >IDDepartamento</th>
																			<th class='fs' >Departamento</th>
																			<th class='fs' >IDPosicion</th>
																			<th class='fs' >Posicion</th>
																			<th class='fs' >FechaReporte</th>
																 </tr></thead>
															<tbody class='fs'>";

										while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
											$c++;
											$html.= "<tr class='fs' >
																<td class='fs'>".$row['Causa']."</td>
																<td class='fs'>".$row['Codigo']."</td>
																<td class='fs'>".$row['Nombre']."</td>
																<td class='fs'>".$row['ApellidoPaterno']."</td>
																<td class='fs'>".$row['ApellidoMaterno']."</td>
																<td class='fs'>".$row['Clase']."</td>
																<td class='fs'>".$row['Locacion']."</td>
																<td class='fs'>".$row['DeptoID']."</td>
																<td class='fs'>".$row['Depto']."</td>
																<td class='fs'>".$row['PosicionID']."</td>
																<td class='fs'>".$row['Posicion']."</td>
																<td class='fs'>".$row['FechaReporte']."</td>
														</tr>";
										}// end while
										$html.= "</tbody></table>";
										//$body.= "</div>";
										sqlsrv_free_stmt($stmt);
								}//end if

								$html.= "</div>";//btn-cont
								$html.= "<div id='btn-cont' class='fn bloque'>";
									$html.= "<div id='status-bar' class='fn enlinea floL'><span class='label fn'>Empleados: </span><span class='count fn'> $c</span> </div>";
									$html.= "<button id='btn-excel' data-filename='Reporte-General' data-target='rep-grid' class='fn btn enlinea floL waves-effect'> <i class='fa fa-1x fa-file-excel-o'></i> Exportar Excel</button>";
									$html.= "<button id='btn-pdf' class='fn btn enlinea floL waves-effect'> <i class='fa fa-1x fa-file-pdf-o'></i> Exportar PDF</button>";
								$html.= "</div>";//btn-cont
              $html.= "</div>";//select-datos
            $html.= "</div>";//tarjeta select

						$html.= "<div id='tablas-ocultas-for-export' class='oculto'>";

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
