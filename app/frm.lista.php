
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

			$tabla = 'tra.lista';
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


			if(intval($permisos) > 0){
				//------------------------------------------------------------------------
				$pages = 0;
				$query ='exec tra.proc_get_pages_lista_bydep @ope = ?, @pages = ? ';
				$params = array(array(&$id_operator, SQLSRV_PARAM_IN)
											, array(&$pages, SQLSRV_PARAM_OUT,SQLSRV_PHPTYPE_INT));
				$stmt_pages= sqlsrv_query($cnn, $query, $params);
				if ($stmt_pages === true) sqlsrv_next_result($stmt_pages);
				sqlsrv_free_stmt($stmt_pages);
				//------------------------------------------------------------------------
				$dep = '';
				$query ='exec tra.proc_get_departamento_byope @ope = ?, @dep = ? ';
				$params = array(array(&$id_operator, SQLSRV_PARAM_IN)
											, array(&$dep, SQLSRV_PARAM_OUT,SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR)));
				$stmt_dep= sqlsrv_query($cnn, $query, $params);
				if ($stmt_dep === true) sqlsrv_next_result($stmt_dep);
				sqlsrv_free_stmt($stmt_dep);
				//------------------------------------------------------------------------
				$html = "<div id='frm-$id_navigator' data-op='".$id_operator."' data-nav='".$id_navigator."' data-table='".boolval($tabla)."'  class='frm frm-employees frm-lista fn boxshadow mar10 oculto'>
								<div id='frm-tabs' class='fn floL enlinea '>

									<div id='set-periodo' data-actual='$id_per' data-title='Seleccionar periodo, Actual: $per, Año: $year, Dias: $days' data-periodo='$id_per' data-year='$year' data-panel='#frm-set-periodo' class='fs tab panel bloque waves-effect unactive '>$year_per</div>";

					$html.="<div id='set-departamento'
												data-departamento='$dep'
												data-ope='$id_operator'
												data-title='Seleccionar departamentos'
												data-panel='#frm-set-departamento'
												class='fn tab panel bloque waves-effect unactive '>
										<i class='fa fa-1x fa-tags'></i>
									</div>
									<div id='btn-refresh-lista' data-title='Actualizar lista de Asistencia' data-tab='frm-refresh' class='fn tab  bloque waves-effect  '>
										<i class='fa fa-1x fa-refresh'></i>
									</div>
									<div id='btn-horas-extra' data-title='Horas Extras' data-tab='frm-horas-extra' class='fn tab  bloque waves-effect  unactive'>
										<i class='fa fa-1x fa-clock-o'></i>
									</div>
									<div id='btn-buscar' data-title='Buscar Colaborador' data-panel='frm-buscar' class='fn tab bloque waves-effect unactive'>
										<i class='fa fa-1x fa-search'></i>
									</div>
									<div 	id='btn-info' data-title='Info Ausentismos'
												data-title='Info Ausentismos'
												data-panel='#frm-info'
												class='fn tab panel bloque waves-effect unactive '>
										<i class='fa fa-1x fa-info'></i>
									</div>


									<div id='frm-pagination' data-actual='1' data-pages='$pages' class='fn tab panel bottom bloque waves-effect ".($pages> 1? 'pages':'')." '>
										<div id='frm-pagination-lab' class='fs bloque waves-effect'>1/$pages</div>
									</div>
								</div>
								<div id='frm-pagination-controls'  data-ope='$id_operator' data-actual='1' data-pages='$pages' class='fs flol enlinea oculto'>
									<div id='first-page' data-action='first' class='fn page-ctrl enlinea floL waves-effect'><i class='fa fa-1x fa-angle-double-left'></i></div>
									<div id='past-page' data-action='past' class='fn page-ctrl enlinea floL waves-effect'><i class='fa fa-1x fa-angle-left'></i></div>
									<div id='next-page' data-action='next' class='fn page-ctrl enlinea floL waves-effect'><i class='fa fa-1x fa-angle-right'></i></div>
									<div id='last-page' data-action='last' class='fn page-ctrl enlinea floL waves-effect'><i class='fa fa-1x fa-angle-double-right'></i></div>
								</div>";


										$query = 'exec cat.proc_create_etiquetas_lista @per = ?';
										$params = array(&$id_per);
										$stmt = $com->_create_stmt($cnn, $query, $params);
										$c=0;
										if($stmt){

											$rows = array();
											while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
												array_push($rows, $row);
												$c++;
											}//end while
							$html.="<div id='frm-table' class='fs istab floL enlinea visible'>
												<div id='rows-head' data-dias-periodo='$c' class='fs bloque'>";
											$html.= "<div id='list-filter-cont' class='floL fs hide closed cur-poi'>
																	<div id='filter-ico-cont' class='fs floL enlinea waves-effect'><i class='fa fa-1x fa-filter' ></i></div>
																	<div id='filter-inp-cont' class='fs floL enlinea'>
																		<input id='filter-input' type='text' class='fs'
																		 			 data-source='#rows-body'	/>
																	</div>
															</div>";
											$html.= "<div id='code' class='floL fs colm enlinea id'
																		data-orden-dir='desc' data-orden-tip='numerico'  data-orden-sou='alter'>
																<span class='fs eti'>C&oacute;digo</span><i class='fa removable fa-1x fa-angle-down'></i>
															</div>
															<div id='name' class='floL fs colm enlinea name'
																	 data-orden-dir='asc' data-orden-tip='alfabetico' data-orden-sou='nombre'>
																<span class='fs eti'>Nombre</span>
															</div>
															<div id='posi' data-count='$c'
																	 data-orden-dir='asc' data-orden-tip='alfabetico' data-orden-sou='posicion'
																	 class='floL fs colm enlinea elli ".($c > 15 ? 'last16': ($c === 13? 'last13': 'last'))."'>
																<span class='fs eti'>Posici&oacute;n </span></div>
															<div id='work' class='floL fs colm enlinea work'><span class='fs eti'>W: </span></div>";
											$c=0;

											foreach ($rows as $row => $value) {
												$c++;
												$html.= "<div id='dias' data-dia='$c' class='floL fs colm dia enlinea' title='".$value['_title']."'><span class='fs eti'>".$value['_etiqueta']." </span><!--<i class=' fa fa-1x fa-filter'></i>!--></div>";
											}//end while
											sqlsrv_free_stmt( $stmt);
										}//end if

									$html .= "</div>
														<div style='width:8px;' class='tc floL fs enlinea scr'><i class='fa fa-1x fa-arrows-v'></i></div>";
														$html.="<div id='frm-set-periodo' data-actual='$id_per' class='fs frm-set bloque borde-negro boxshadow oculto'>
																			<div class='fn sel-box-title'>
																				<div class='fm enlinea floL text'> Selecciona el Periodo</div>
																				<div data-btn='#set-periodo' data-who='#frm-set-periodo' class='fm enlinea floL close cur-poi'><i class='fa fa-1x fa-times'></i></div>
																			</div>";
																		$query = 'exec cat.proc_list_periodos';
																		if ($stmt_per = sqlsrv_query($cnn, $query, array())){
																			$html.="<div class='fs set-options thin-scroll' data-btn='#set-periodo'>";
																			while($row = sqlsrv_fetch_array($stmt_per, SQLSRV_FETCH_ASSOC)){
																				$html.="<div id='".$row['id_periodo']."' data-actual='".$row['_actual']."' class='fs periodo-option ".($row['_actual'] === 1 ? 'selected': '')."' data-value='".$row['_label']."' >";
																					$html.="<div class=' fs option-label enlinea'>".$row['_label']."</div>";
																					$html.="<div class=' fs option-ini enlinea'>".$row['_ini']."<i class='fa fa-1x fa-long-arrow-right'></i></div>";
																					$html.="<div class=' fs option-end enlinea'>".$row['_fin']."</div>";
																				$html.="</div>";
																			}//end while
																			$html.="</div>";
																			sqlsrv_free_stmt($stmt_per);
																		}else{
																			$html.="<div class='fs option' data-value='$id_per' selected> $year_per </div>";
																		}//end if
														$html.="</div>";
									$html .= "<div id='frm-set-departamento' data-ope='$id_operator' class='fn frm-set boxshadow borde-negro oculto'>
															<div class='fn sel-box-title'>
																<div class='fm enlinea floL text'> Selecciona el Departamento</div>
																<div data-btn='#set-departamento' data-who='#frm-set-departamento' class='fm enlinea floL close cur-poi'><i class='fa fa-1x fa-times'></i></div>
															</div>
															<div class='fn set-departamento-arbol'>
																<div class='fn cargando visible'><i class='fa fa-2x fa-spin fa-cog'></i></div>
															</div>
														</div>
														<div id='filter' data-to='undefined' class='fn oculto boxshadow borde-negro'>
															<div class='fs label enlinea'>
																<select id='filter-colm' class='fs bloque'>
																	<option value='code'>Codigo</option>
																	<option value='name'>Nombre</option>
																	<option value='posi'>Posicion</option>
																	<option value='work'>W</option>";
																	foreach ($rows as $row => $value) {
																		$html.= "<option value='".trim($value['_etiqueta'])."'>".trim($value['_etiqueta'])."</option>";
																	}//end while
														$html .="</select>
															</div>
															<div class='fs input enlinea'><input id='filter-input' name='filter-input' placeholder='Buscar:' type='text' class='fs bloque' /></div>
														</div>";
									$html .="<div id='frm-buscar' class='fn frm-set boxshadow borde-negro oculto'>";
										//aqui
										$html .="
															<div class='fm enlinea floL text'><input id='inp-buscar' class='fn'/></div>
															<div id='btn-exec' data-btn='#btn-exec'  class='fn btn bg-buscar-btn enlinea floL cur-poi'><i class='fa fa-1x fa-bolt'></i></div>
															<div data-btn='#btn-buscar' data-who='#frm-buscar' class='fn btn bg-buscar-btn enlinea floL close cur-poi'><i class='fa fa-1x fa-times'></i></div>
														";

									$html .="</div>";
									$html .="<div id='frm-info' class='fn frm-set boxshadow borde-negro oculto'>";
										$html .="<div class='fn sel-box-title'>
															<div class='fm enlinea floL text'> Info Ausentismos</div>
															<div data-btn='#btn-info' data-who='#frm-info' class='fm enlinea floL close cur-poi'><i class='fa fa-1x fa-times'></i></div>
														</div>";
										$query="select
														_letra
														,_descripcion
													from cat.ausentismos
													order by _letra asc;";
										$params=array();
										if($stmt = sqlsrv_query($cnn, $query, $params)){
											while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
												$html.="<div class='fn info-item'>
														<div class='_letra fn enlinea'> ".$row['_letra']."</div>
														<div class='_desc fn enlinea'>".$row['_descripcion']."</div>
													</div>";
											}//end while
										}//end if;

									$html .="</div>
									<div id='rows-body-cargando' class='fn oculto'>
										<i class='fa fa-2x fa-cog fa-spin'></i>
									</div>
									<div id='rows-body-error' class='fn oculto'>
										<div class='fn ico rojo'><i class='fa fa-2x fa-exclamation-triangle'></i></div>
										<div class='fn txt'></div>
									</div>
									<div id='rows-body' class='modo-checadas fm bloque paginar'>";

											$query = 'exec tra.proc_create_lista_by_ope @ope = ?';
											$params = array(&$id_operator);
											$stmt = $com->_create_stmt($cnn, $query, $params);
											if($stmt){
												$i=0;
												$create = new _creates();
												$first = true;
												while( $value = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ) ) {
													if($first){
														$first = false;
														$title = $value['_alter_id']." - ".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno']." - ".$value['_posicion_name'];
														$first_empl = $value['id_employee'];
														$url = "https://asistenciarcd.aicollection.local:444/app/proc.photo?id=".$first_empl;
													}
													$i++;
													$html.="	<div id='".$value['id_lista']."' data-id='$i'
																data-periodo='".$value['id_periodo']."'
																data-employee='".$value['id_employee']."'
																data-_alter_id='".$value['_alter_id']."'
																data-code='".$value['_posicion_name']."'
																data-name='".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."'
																class='fm bloque empl row'>
															<div class='floL fm cell alter enlinea elli id'>".$value['_alter_id']."</div>
															<div class='floL fm cell nombre enlinea elli name'>".$value['_nombres']." ". $value['_apellido_paterno']." ". $value['_apellido_materno'] ."</div>
															<div class='floL fm cell posicion enlinea ".($c > 15 ? 'last16': ($c === 13? 'last13': 'last'))."'>".$value['_posicion_name']."</div>
															<div tabindex='1' class='der floL fm cell dia enlinea work waves-effect'><span class='fm tran-bez-5s border-interno'>". $value['_days'] ."</span></div>
															<div tabindex='2'
															 									data-color='".($create->_color_from_letra($cnn, $com, $value['_c01']))."' data-cn='_c01'
																								data-employee='".$value['id_employee']."' class='tooltip der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c01']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c01'] ."</span></div>
															<div tabindex='3'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c02']))."' data-cn='_c02'
																								data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c02']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c02'] ."</span></div>
															<div tabindex='4'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c03']))."' data-cn='_c03'
																								data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c03']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c03'] ."</span></div>
															<div tabindex='5'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c04']))."' data-cn='_c04'
																								data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c04']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c04'] ."</span></div>
															<div tabindex='6'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c05']))."' data-cn='_c05'
																								data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c05']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c05'] ."</span></div>
															<div tabindex='7'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c06']))."' data-cn='_c06'
																								data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c06']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c06'] ."</span></div>
															<div tabindex='8'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c07']))."' data-cn='_c07'
																								data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c07']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c07'] ."</span></div>
															<div tabindex='9'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c08']))."' data-cn='_c08'
																								data-employee='".$value['id_employee']."' class='der floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c08']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c08'] ."</span></div>
															<div tabindex='10'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c09']))."' data-cn='_c09'
																								data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c09']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c09'] ."</span></div>
															<div tabindex='11'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c10']))."' data-cn='_c10'
																								data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c10']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c10'] ."</span></div>
															<div tabindex='12'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c11']))."' data-cn='_c11'
																								data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c11']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c11'] ."</span></div>

															";if($c >= 12){$html.="
															<div tabindex='13'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c12']))."' data-cn='_c12'
																								data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c12']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c12'] ."</span></div>
															";}if($c >= 13){$html.="
															<div tabindex='14'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c13']))."' data-cn='_c13'
																								data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c13']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c13'] ."</span></div>
															";}if($c >= 14){$html.="
															<div tabindex='15'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c14']))."' data-cn='_c14'
																								data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c14']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c14'] ."</span></div>
															";}if($c >= 15){$html.="
															<div tabindex='16'
																								data-color='".($create->_color_from_letra($cnn, $com, $value['_c15']))."' data-cn='_c15'
																								data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c15']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c15'] ."</span></div>";
															}if($c == 16){
								                  $html.="<div tabindex='17'
																							 data-color='".($create->_color_from_letra($cnn, $com, $value['_c16']))."' data-cn='_c16' data-employee='".$value['id_employee']."' class='izq floL fm cell dia waves-effect enlinea ".($create->_color_from_letra($cnn, $com, $value['_c16']))."'><span class='fm tran-bez-5s border-interno'>". $value['_c16'] ."</span></div>";
								              }//end if
								            $html.="</div>";
												}//end while
												sqlsrv_free_stmt( $stmt);
											}//end if
									$html .="</div>
									<div id='dia-cont' data-employee='$first_empl' class='fm'>
										<div id='dia-bar' class='fm'>
											<div id='dia-info' class='fm enlinea floL'>
												<div class='fn floL dia-bar-photo enlinea'>
													<img id='bar-photo-item' src='$url' />
												</div>
												<div class=' dia-bar-data enlinea'>
													<div id='label' class='fs enlinea'>Fecha:</div>
													<div id='data-fecha' class='fs ledata enlinea'><span class='fs val'></span></div>

													<div id='label' class='fs enlinea'>Entrada:</div>
													<div id='data-entrada' class='fs ledata enlinea'><span class='fs val'></span></div>

													<div id='label' class='fs enlinea'>Salida:</div>
													<div id='data-salida' class='fs ledata enlinea'><span class='fs val'></span></div>

													<div id='label' class='fs enlinea'>Jornada:</div>
													<div id='data-jornada' class='fs ledata enlinea'><span class='fs val'></span></div>

													<div id='label' class='fs enlinea'>Ausentismo:</div>
													<div id='data-ausentismo' class='fs ledata-large enlinea elli' title=''><span class='fs val'></span></div>
												</div>
											</div>
											<div id='btn-dia' class='fm waves-effect enlinea floL' data-active='0'>
												<div class='nwrap'>
													<span class='nav-line'></span>
													<span class='nav-line'></span>
												</div>
											</div>
										</div>

										<div id='dia-wrapper' class='fm bloque oculto' data-diahide='0'>
											<div id='dia-wrapper-head' class='fn bloque'>
												<div id='dia-wrapper-title' class='fn floL title enlinea'>$title</div>
												<div id='dia-wrapper-close' class='fn floL btn close enlinea waves-effect'><i class='fa fa-1x fa-angle-down'></i></div>
											</div>
											<div id='dia-wrapper-content' class='fn bloque'>
												<!--<div class='cargando fn' data-diahide='0'><i class='fa fa-3x fa-cog fa-spin' data-diahide='0'></i></div>!-->
												<div class='fn w25p floL photo enlinea'>
													<img id='photo_item' src='$url' />
												</div>
												<div class='fn floL data enlinea'>
													<div class='fn data-item info floL enlinea'>
														<div class='fn box bder'>

															<div class='fn content thin-scroll'>
																<div class='cargando fn visible' '><i class='fa fa-3x fa-cog fa-spin' data-diahide='0'></i></div>
																<div class='datos fs oculto'></div>
															</div>
															<div class='fn head'>
																	<div data-action='informacion' class='fm btn-more floL enlinea'><i class='fa fa-1x fa-plus'></i></div>
																	<div class='fm floL enlinea'>Información</div>
															</div>
														</div>
													</div>
													<div class='fn data-item checadas floL enlinea'>
														<div class='fn box bder'>

															<div class='fn content thin-scroll'>
																<div class='cargando fn visible' ><i class='fa fa-3x fa-cog fa-spin' data-diahide='0'></i></div>
																<div id='datos-checadas' class='datos-checadas tab-cont fs oculto'></div>
																<div id='datos-jornadas' class='datos-jornadas tab-cont fs oculto'></div>
															</div>
															<div class='fn head'>
																	<div class='fm floL enlinea tab active waves-effect' data-tab='#datos-checadas'>Checadas</div>
																	<div class='fm floL enlinea tab inactive waves-effect' data-tab='#datos-jornadas'>Jornadas</div>
															</div>
														</div>
													</div>
													<div class='fn data-item ausentismos floL enlinea'>
														<div class='fn box '>
															<div class='fn content thin-scroll'>
																<div class='cargando fn visible'><i class='fa fa-3x fa-cog fa-spin' data-diahide='0'></i></div>
																<div class='datos fs oculto'></div>
															</div>
															<div class='fn head'>
																	<div class='fm floL enlinea tab active'>Ausentismos</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<script>
									$(document).ready(function() {

										var _row = $('#rows-body').children('.empl.row').first();

										_set_focus(_row, 1);
									});
								</script>";
								//$html.=$create->_add($cnn,$com, $id_navigator, $id_operator);

								$html.="</div>
							</div>";

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
