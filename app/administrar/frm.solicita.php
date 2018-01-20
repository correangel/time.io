<?php
	header('Content-type: application/json');
	//echo dirname(__FILE__);
	include_once $_SERVER['DOCUMENT_ROOT']."/login/class.login.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/constantes.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.mssql.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/includes/class.creates.php";
	//include_once '../includes/class.menu.php';


	//Conexion a Base de Datos
	$com = new com_mssql();
	$cnn = $com->_conectar_win(HOST,DATA);
	$lifetime = $com->_get_param($cnn,  'session_lifetime'); // in minutes

	//Login
	$clogin = new _login();
	$clogin->iniciar_sesion('TimeIO', $lifetime);
	//echo $_POST['id_nav'];
	if($clogin->_logeado()){
		if(!isset($_POST['id_nav']) && !isset($_POST['id_nav_proc'])  ){
			$resp['status'] = 'error';
			$resp['msg'] = '1.Error de Posteo...';
			$com->_desconectar($cnn);
			echo json_encode($resp);
			return;
		}//end if

		$create = new _creates();
		$id_navigator = (isset($_POST['id_nav'])?$_POST['id_nav']:$_POST['id_nav_proc']);
		$id_operator = $_SESSION['id_operator'];
		$_full = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_full'));
		$_read = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_read'));
		$_write = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_write'));
		$_special = intval($com->_get_permisos_nav($cnn, $id_navigator,$id_operator, '_special'));
		//echo $id_navigator;
		if(isset($_POST['id_nav'])) {

      $tabla = $com-> _get_val($cnn, '_table', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);
      $query = $com-> _get_val($cnn, '_insert_query', 'adm.navigator', 'id_navigator', $id_navigator, 'char(36)' ,1);
			//echo $_full;
			$permisos = 0;
			$permisos = $_full + $_read + $_write + $_special;
			//echo $permisos;
			$user_name = $_SESSION['user_name'];
			$user_sing = $_SESSION['user_sing'];
			$user_ldap = $_SESSION['user_ldap'];
			$ip = $_SERVER['REMOTE_ADDR'];


			if(intval($permisos)>0){
      #---------------------------------------------------------------------------------------------------------------
      # Inicia Contenido especifico del elemento.
      #---------------------------------------------------------------------------------------------------------------
      $html = "<div id='frm-".$id_navigator."' data-ope='".$id_operator."' data-nav='".$id_navigator."' data-table='".boolval($tabla)."' data-query='".boolval($query)."' class='frm fn boxshadow mar10 oculto'>";
				$html.="<div id='frm-wrapper' class='fs solicitud '>";
					$html.="<div id='frm-tabs-head' class='fs'>";
	        $html.="<div id='tab-soli' class='fs floL enlinea'>
	                  Solicitud de Usuario Nuevo</div>";
						$html.="<div id='tab-loca' class='fs floL enlinea'>
											<input data-source='#frm-locacion' id='inp-loca' type='text' class='fs enlinea filtra' placeholder='Locación'/></div>";

						$html.="<div id='tab-depa' class='fs floL enlinea'>
											<input data-source='#departamento-rows' id='inp-depa' type='text' class='fs enlinea filtra' placeholder='Departamento'/></div>";
					$html.="</div>"; // frm-tabs-head
					//---------------------------------------------------
	        $html.="<div id='frm-vert-tabs-sol' class='fs enlinea floL'>";
	          $html.="<div id='tabs-sol-data' class='fs'>";//start sol-data
	            $html.="<div class='fn gpo-captura code bloque'>";
								$html.="<div class='fn label-ico enlinea'><i class='fa fa-1x fa-hashtag'></i></div>";
	              $html.="<input id='_alter_id' class='fn enlinea' title='Inserta el C&oacute;digo y presiona \"Enter\"' data-ope='".$id_operator."'/>";
								$html.="<div class='fn msg enlinea'><i class='fa fa-1x fa-angle-left'></i><span class='fs le-msg elli'> </span></div>";
							$html.="</div>";
	            $html.="<div id='data-employee' class='tarjeta fs bloque '>"; // start data-employee
	                $html.="<div class='fn floL photo enlinea'>";
	                  $html.="<img id='employee-photo' src='/imagenes/no_image_profile.jpg' />";
	                $html.="</div>";
									$html.="<div class='fn floL data enlinea'>";
	                  $html.="<input id='_nombre' class='fs bloque elli' title='Nombre:'  disabled/>";
										$html.="<input id='_locacion' class='fs bloque elli' title='Locaci&oacute;n:'  disabled/>";
										$html.="<input id='_departamento' class='fs bloque elli' title='Departamento:'  disabled/>";
										$html.="<input id='_posicion' class='fs bloque elli' title='Posici&oacute;n:'  disabled/>";
	                $html.="</div>";
	            $html.="</div>"; //end data-employee

	            $html.="<div class='fs capturas-completa bloque'>"; // start capturas-completa
								//---------------------------------------------------
								// Roles
								//---------------------------------------------------
								$html.="<div class='fn gpo-captura'>";//start roles
									$html.="<div class='fn floL label-ico enlinea'><i class='fa fa-1x fa-shield'></i></div>";
									$html.="<div id='cont-roles' class='fs floL has-options contenedor enlinea closed'>";
										$html.="<div tabindex='0' id='cont-roles-title' class='fs title bloque' data-parent='#cont-roles'>";
										 $html.="<div id='txt' class='fs floL enlinea' data-parent='#cont-roles'>
															<input id='id_role' data-id='' type='text' class='toupper fs integrado' data-parent='#cont-roles' />
														</div>";
										 $html.="<div tabindex='0' id='ico' class='fn floL enlinea' data-parent='#cont-roles'><i class='fa fa-1x fa-ellipsis-h' data-parent='#cont-roles'></i></div>";
										$html.="</div>";
										$html.="<div id='options' class='fs select bloque thin-scroll oculto' data-parent='#cont-roles'>";

										$query = 'exec cat.proc_get_roles_available_by_ope ?';
										$params = array(&$id_operator);
										 if($stmt = $com->_create_stmt($cnn, $query, $params)){
											 while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
												 $html.= "<div data-id='".$row['id_role']."' class='fs option available-role bloque' data-parent='#cont-roles'>".$row['_role_name']."</div>";
											 }// end while
										 }//end if
										$html.="</div>";
									$html.="</div>";
								$html.="</div>";//end roles
								 //---------------------------------------------------
							 //$html.="</div>";//end roles
								/*$html.="<div class='fn gpo-captura'>";
									$html.="<div class='fn label-ico enlinea'><i class='fa fa-1x fa-shield'></i></div>";
									$query = 'exec cat.proc_get_roles_available_by_ope ?';
									$params = array(&$id_operator);
									if($stmt = $com->_create_stmt($cnn, $query, $params)){
										$html.="<select class='tran-bez-5s fn enlinea read' id='id_role'>";
											$html.="<option value='' selected=''>";
											while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
												$html.="</option><option value='".$row['id_role']."'>".$row['_role_name']."</option>";
											}// end while
										$html.="</select>";
									}//end if
		              //$html.=$create->_select($cnn, $com, 'cat.roles', 'id_role', '_role_name', 'id_role');
								$html.="</div>";*/
								//---------------------------------------------------
								// Correo
								//---------------------------------------------------
								$html.="<div class='fn gpo-captura'>"; //start correo
									$html.="<div class='fn floL label-ico enlinea'><i class='fa fa-1x fa-envelope'></i></div>";
									$html.="<input id='_correo' class='fs floL tolower border-effect' placeholder=''/>";
									$html.="<div id='cont-dominios' class='fs floL has-options contenedor-50per enlinea closed'>";
									 $html.="<div tabindex='0' id='cont-dominios-title' class='fs title bloque' data-parent='#cont-dominios'>";
										 $html.="<div id='txt' class='fs floL enlinea' data-parent='#cont-dominios'>
										 					<input id='id_email' id='id_role' data-id='' type='text' class='fs integrado' data-parent='#cont-dominios' />
										 				</div>";
										 $html.="<div tabindex='0' id='ico' class='fn floL enlinea' data-parent='#cont-dominios'><i class='fa fa-1x fa-ellipsis-h' data-parent='#cont-dominios'></i></div>";
									 $html.="</div>";
									 $html.="<div id='options' class='fs select bloque thin-scroll oculto' data-parent='#cont-dominios'>";

										 $query = 'exec cat.proc_get_dominios_correo';
										 $params = array();
										 if($stmt = $com->_create_stmt($cnn, $query, $params)){
											 while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
												 $html.= "<div data-id='".$row['id_email']."' class='fs option available-domain bloque' data-parent='#cont-dominios'>".$row['_domain']."</div>";
											 }// end while
										 }//end if
									 $html.="</div>";
									$html.="</div>";
								 $html.="</div>"; //end correo
								 //---------------------------------------------------
								 //departamentos
								 //---------------------------------------------------
								 $html.="<div class='fn gpo-captura'>";
 									$html.="<div class='fn floL label-ico enlinea'><i class='fa fa-1x fa-tags'></i></div>";
 									$html.="<div id='cont-deptos' class='fs contenedor has-options enlinea closed'>";
 										$html.="<div tabindex='0' id='cont-deptos-title' class='fs title bloque' data-parent='#cont-deptos'>";
 											$html.="<div id='txt' class='fs enlinea' data-parent='#cont-deptos' >0 Departamentos Seleccionados</div>";
 											$html.="<div tabindex='0' id='ico' class='fn enlinea' data-parent='#cont-deptos'><i class='fa fa-1x fa-ellipsis-h' data-parent='#cont-deptos'></i></div>";
 										$html.="</div>";
 										$html.="<div id='options' class='fs delete bloque thin-scroll oculto' data-parent='#cont-deptos'>";
 										$html.="</div>";
 									$html.="</div>";
 								$html.="</div>";// end departamentos

							$html.="</div>"; // end capturas completa

							/*$html.="<div id='cont-departamentos' class='fs seleccionados bloque closed'>";
								$html.="<div tabindex='0' id='cont-departamentos-title' class='fs bloque closed'>";
									$html.="<div id='txt' class='fs enlinea'>0 Departamentos Seleccionados</div>";
									$html.="<div tabindex='0' id='ico' class='fn enlinea'><i class='fa fa-1x fa-ellipsis-h'></i></div>";
								$html.="</div>";
								$html.="<div id='rows' class='fs bloque thin-scroll oculto'>";
								$html.="</div>";
							$html.="</div>";
						$html.="</div>"; */


							$html.="<div id='btns' class='fn bottom bloque'>"; //start btns
								$html.="<div  tabindex='0' id='btn-enviar' class='noerror fn enlinea waves-effect waves-light bg-verde-2'><i class='fa fa-1x fa-send-o'></i> <div class='fn msg enlinea'> Enviar</div></div>";
							$html.="</div>";// end btns
	          //$html.="</div>";

						$html.="</div>"; // end sol-data
					$html.="</div>"; // end frm-vert-tabs-dep
					//--------------------------------------------
					$html.="<div id='frm-locacion' class='fs enlinea floL'>";
	          $query = "exec cat.proc_get_list_elements
	                    	@table = ?,
	                    	@columns = ?,
	                    	@where = null,
	                    	@active= 1,
	                    	@distinct= 1,
	                      @verbose = 0,
		                    @orderby = '_locacion_name asc'";
	          $table = 'cat.locacion with(nolock)';

	          $columns = 'locacion_id, _locacion_code, _locacion_name';
	          $params=array(array(&$table, SQLSRV_PARAM_IN), array(&$columns, SQLSRV_PARAM_IN));
	          $stmt = $com->_create_stmt($cnn,$query, $params);
	          if ($stmt){
	            while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
	              $html.="<div class='fn locacion for-filtra bloque waves-effect' data-name='".$row['_locacion_name']."' data-code='".$row['_locacion_code']."' waves-effect' id='".$row['locacion_id']."'>
													<div class='fn check floL enlinea oculto'><i class='fa fa-1x fa-square-o'></i></div>
													<div class='fs txt floL enlinea'> ".$row['_locacion_code']."</div>
											  </div>";
	            }//end while
	          }else{
	            $error = sqlsrv_errors();
	          }//end if
	        $html.="</div>"; // frm-tabs

					$html.="<div id='frm-departamento' class='fs enlinea floL'>";
						$html.="<div class='fn cargando oculto'><i class='fa fa-2x fa-cog fa-spin'></i></div>";
						$html.="<div id='departamento-rows' class='fn'></div>";
					$html.="</div>"; // frm-vert-tabs-dep
				$html.="</div>";
				//-----------------------------------------------------------------------------------------
				$html.="<div id='frm-result' class='fs solicitud  oculto'>";
					$html.="<div id='result-wrapper' class='fn bloque '>";
						$html.="<div id='data' class='fn bloque'>";
							$html.="<div id='ico' class='fn bloque i-verde-2'><i class='fa fa-5x fa-check'></i></div>";
							$html.="<div id='msg' class='fn bloque '>ID: 1 </div>";
						$html.="</div>";
						$html.="<div id='btns' class='fn bloque'>";
							$html.="<div id='btn-salir' class='fn btn enlinea bg-rojo waves-effect waves-light'>Salir</div>";
							$html.="<div id='btn-nuevo' class='fn btn enlinea bg-azul waves-effect waves-light'>Nuevo</div>";
						$html.="</div>";
					$html.="</div>";
				$html.="</div>";
				//-----------------------------------------------------------------------------------------

      $html.="</div>";//frm-navigator


      $resp['html'] = $html;
      $resp['status'] = 'ok';
      $resp['msg'] = 'Usar html';

      #---------------------------------------------------------------------------------------------------------------
      }else{
				$html = "<div id=frm_permisos´ class='frm fm boxshadow mar10'>
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
  }else{
		$url = $com->_get_param($cnn, 'raiz');
		$resp['status'] = 'login';
		$resp['url'] = $url;
		$resp['msg'] = 'Sesion Caducada...';
	}//end if

	$com->_desconectar($cnn);
	echo json_encode($resp);
