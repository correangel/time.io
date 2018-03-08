
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
                      class='frm add-has-options frm-rep-generales fn boxshadow mar10 oculto'>";
			    $html.= "<div id='frm-cont-rep-generales' class='fn  bloque'>";
            $html.= "<div id='rep-select' class='fn bloque '>";

              $html.= "<div id='select-opt' class='fn enlinea floL boxshadow'>";
								$html.= "<div id='filters-collapse' class='fn bloque'>";
									$html.= "<div id='btn-collapse' class='fn opened'><i class='fa fa-1x fa-chevron-left'></i></div>";
								$html.= "</div>";
								$html.= "<div id='filters-cont' class='fn bloque'>";

								//---------------------------------------------
								$html.= "<div class='gpo fn bloque'>";
									$html.= "<div class='ico fn enlinea floL'>
														<i class='fa fa-1x fa-bar-chart'></i>
													</div>";
									$html.= "<div class='inp fn enlinea floL'>";
										$html.="<div id='cont-tipo' class='fn floL has-options contenedor enlinea closed'>";
											$html.="<div tabindex='0' id='cont-roles-title' class='fn title bloque' data-parent='#cont-tipo'>";
											 $html.="<div id='txt' class='fn floL enlinea' data-parent='#cont-tipo'>
																<input id='id-tipo' data-id=''  type='text' class='toupper fn integrado' data-parent='#cont-tipo' />
															</div>";
											 $html.="<div tabindex='0' id='ico' class='fn floL enlinea' data-parent='#cont-tipo'><i class='fa fa-1x fa-ellipsis-h' data-parent='#cont-tipo'></i></div>";
											$html.="</div>";
											$html.="<div id='options' class='fn select bloque thin-scroll oculto' data-parent='#cont-tipo'>";


													$html.= "<div data-id='rep-ausentismos'
																					class='fn option bloque'
																					data-label='Ausentismos'
																					data-parent='#cont-tipo'>REPORTE AUSENTISMOS</div>";
													$html.= "<div data-id='rep-horas-extras'
																					class='fn option bloque'
																					data-label='HorasExtras'
																					data-parent='#cont-tipo'>REPORTE HORAS EXTRAS</div>";
													$html.= "<div data-id='rep-jornadas'
																					class='fn option bloque'
																					data-label='Jornadas'
																					data-parent='#cont-tipo'>REPORTE JORNADAS</div>";

											$html.="</div>";
										$html.="</div>";

									$html.= "</div>";
							$html.= "</div>"; //gpo
										//---------------------------------------------
										$html.= "<div class='gpo fn bloque'>";
											$html.= "<div class='ico fn enlinea floL'>
																<i class='fa fa-1x fa-calendar'></i>
															</div>";
											$html.= "<div class='inp fn enlinea floL'>";
												$html.="<div id='cont-year' class='fn floL has-options contenedor-50per enlinea closed'>";
													$html.="<div tabindex='0' id='cont-roles-title' class='fn title bloque' data-parent='#cont-year'>";
													 $html.="<div id='txt' class='fn floL enlinea' data-parent='#cont-year'>
																		<input id='id-year' data-id='".date("Y")."' value='".date("Y")."' type='text' class='toupper fn integrado' data-parent='#cont-year' />
																	</div>";
													 $html.="<div tabindex='0' id='ico' class='fn floL enlinea' data-parent='#cont-year'><i class='fa fa-1x fa-ellipsis-h' data-parent='#cont-year'></i></div>";
													$html.="</div>";
													$html.="<div id='options' class='fn select bloque thin-scroll oculto' data-parent='#cont-year'>";

													$query = 'select _year from cat.years where active = 1 order by _year desc;';
													$params = array();
													 if($stmt = $com->_create_stmt($cnn, $query, $params)){
														 while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
															 $html.= "<div data-id='".$row['_year']."'
																							class='fn option bloque'
																							data-label='".$row['_year']."'
																							data-parent='#cont-year'>".$row['_year']."</div>";
														 }// end while
													 }//end if
													$html.="</div>";
												$html.="</div>";

											$html.= "</div>";
									$html.= "</div>"; //gpo
									//---------------------------------------------
									$html.= "<div class='gpo fn bloque'>";
										$html.= "<div class='ico fn enlinea floL'>
															<i class='fa fa-1x fa-calendar'></i>
														</div>";
										$html.= "<div class='inp fn enlinea floL'>";
											$html.="<div id='cont-periodo' class='fn floL has-options contenedor-50per enlinea closed'>";
												$html.="<div tabindex='0' id='cont-roles-title' class='fn title bloque' data-parent='#cont-periodo'>";
												 $html.="<div id='txt' class='fn floL enlinea' data-parent='#cont-periodo'>
																	<input id='id-per' data-id='' type='text' class='toupper fn integrado' data-parent='#cont-periodo' />
																</div>";
												 $html.="<div tabindex='0' id='ico' class='fn floL enlinea' data-parent='#cont-periodo'><i class='fa fa-1x fa-ellipsis-h' data-parent='#cont-periodo'></i></div>";
												$html.="</div>";
												$html.="<div id='options' class='fn select bloque thin-scroll oculto' data-parent='#cont-periodo'>";

												$year = date("Y");
									      $query = 'exec cat.proc_get_list_periodos_byyear @year = ?';
									      $params = array(&$year);
												if($stmt = $com->_create_stmt($cnn, $query, $params)){
													while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
														$html.= "<div data-id='".$row['id_periodo']."'
																					 class='fn option bloque'
																					 data-ini='".$row['_ini']."'
																					 data-fin='".$row['_fin']."'
																					 data-label='".$row['_label']."'
																					 data-parent='#cont-periodo'>".$row['_label']."</div>";
													}// end while
												}//end if


												$html.="</div>";
											$html.="</div>";

										$html.= "</div>";
									$html.= "</div>"; //gpo
								//---------------------------------------------
								// fechas
									$html.="<div id='combo-fechas'>";
										$html.="<div id='fec-ini' class='fn gpo-fec bloque' title='Fecha de Inicio'>
															<div class='cur-poi ico fn enlinea floL unactive'><i class='enlinea fa fa-square-o fa-1x'></i> </div>
															<div class='val fn enlinea floL'><input id='inp-ini' type='date' class='fn enlinea' disabled></div>
														</div>";
										$html.="<div id='fec-fin' class='fn gpo-fec bloque' title='Fecha Final '>
															<div class='cur-poi ico fn enlinea floL unactive'><i class='enlinea fa fa-square-o fa-1x'></i> </div>
															<div class='val fn enlinea floL'><input id='inp-fin' type='date' class='fn enlinea' disabled></div>
														</div>";
									$html.= "</div>";

								//---------------------------------------------
								//[cat].[proc_get_ausentismos_rep]
								$html.= "<div class='gpo fn bloque ausentismos oculto'>";
									$html.= "<div class='ico fn enlinea floL'>
														<i class='fa fa-1x fa-calendar-check-o'></i>
													</div>";
									$html.= "<div class='inp fn enlinea floL'>";
										$html.="<div id='cont-ausentismo' class='fn floL has-options contenedor enlinea closed'>";
											$html.="<div tabindex='0' id='cont-roles-title' class='fn title bloque' data-parent='#cont-ausentismo'>";
											 $html.="<div id='txt' class='fn floL enlinea' data-parent='#cont-ausentismo'>
																<input id='id-ausentismo' data-id='' type='text' class='toupper fs integrado' data-parent='#cont-ausentismo' />
															</div>";
											 $html.="<div tabindex='0' id='ico' class='fn floL enlinea' data-parent='#cont-ausentismo'><i class='fa fa-1x fa-ellipsis-h' data-parent='#cont-ausentismo'></i></div>";
											$html.="</div>";
											$html.="<div id='options' class='fn select bloque thin-scroll oculto' data-parent='#cont-ausentismo'>";

											//$year = date("Y");
											$query = 'exec [cat].[proc_get_ausentismos_rep]';
											$params = array();
											if($stmt = $com->_create_stmt($cnn, $query, $params)){
												$html.= "<div data-id='*'
																			 class='fs option bloque elli'
																			 data-parent='#cont-ausentismo'>TODOS</div>";
												while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
													$html.= "<div data-id='".$row['id_ausentismo']."'
																				 class='fs option bloque elli'
																				 data-code='".$row['_letra']."'
																				 data-name='".$row['_descripcion']."'

																				 data-parent='#cont-ausentismo'>".$row['_letra']." - ".$row['_descripcion']."</div>";
												}// end while
											}//end if


											$html.="</div>";
										$html.="</div>";

									$html.= "</div>";
								$html.= "</div>"; //gpo

								//---------------------------------------------

								$html.= "<div class='gpo fn bloque'>";
									$html.= "<div class='ico fn enlinea floL'>
														<i class='fa fa-1x fa-tags'></i>
													</div>";
									$html.= "<div class='inp fn enlinea floL'>";
										$html.="<div id='cont-deptos' class='fn floL has-options contenedor enlinea closed'>";
											$html.="<div tabindex='0' id='cont-roles-title' class='fn title bloque' data-parent='#cont-deptos'>";
											 $html.="<div id='txt' class='fn floL enlinea' data-parent='#cont-deptos'>
																<input id='id-depto' data-id='' type='text' class='toupper fs integrado' data-parent='#cont-deptos' />
															</div>";
											 $html.="<div tabindex='0' id='ico' class='fn floL enlinea' data-parent='#cont-deptos'><i class='fa fa-1x fa-ellipsis-h' data-parent='#cont-deptos'></i></div>";
											$html.="</div>";
											$html.="<div id='options' class='fn select bloque thin-scroll oculto' data-parent='#cont-deptos'>";

											//$year = date("Y");
											$query = 'exec [cat].[proc_get_departamentos_byope] @ope = ?';
											$params = array(&$id_operator);
											if($stmt = $com->_create_stmt($cnn, $query, $params)){
												$html.= "<div data-id='*'
																			 class='fs option bloque elli'
																			 data-parent='#cont-deptos'>TODOS</div>";
												while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
													$html.= "<div data-id='".$row['id_departamento']."'
																				 class='fs option bloque elli'
																				 data-code='".$row['_departamento_code']."'
																				 data-name='".$row['_departamento_name']."'

																				 data-parent='#cont-deptos'>".$row['_departamento_code']." - ".$row['_departamento_name']."</div>";
												}// end while
											}//end if


											$html.="</div>";
										$html.="</div>";

									$html.= "</div>";
								$html.= "</div>"; //gpo
								//-----------------------------------

								$html.= "<div class='gpo fn bloque'>";
                  $html.= "<div class='ico fn enlinea floL'>
                            <i class='fa fa-1x fa-hashtag'></i>
                          </div>";
                  $html.= "<div class='inp fn enlinea floL'>
                            <input id='_alter_id' data-emp='*' class='fs  enlinea floL'/>
														<button id='buscar-emp' class='fn btn-buscar enlinea floL waves-effect'> <i class='fa fa-1x fa-search' ></i></button>
														<div id='win-buscar-emp' class='window fn oculto boxshadow'>
															<div class='win-title fn bloque'>
																<div class='inp-cont fn enlinea floL'>
																	<input id='inp-filter' data-source='#win-rows-body' type='text' class='fn' disabled>
																</div>
																<div id='btn-close' data-target='#win-buscar-emp' class='ico-cont fn enlinea floL'>
																	<i class='fa fa-1x fa-times'></i>
																</div>
															</div>
															<div class='win-data fn bloque'>
																<div class='fn tar-cargando bloque visible' >
																	<i class='fa fa-2x fa-cog fa-spin'></i>
																</div>
																<div id='win-rows-body' class='fn bloque oculto thin-scroll' >
																</div>
															</div>
														</div>

													</div>";
                $html.= "</div>";
								//-----------------------------------------
								$html.= "</div>";//filters-cont



								$html.= "<div id='btn-cont' class='fn bloque'>";

									$html.= "<button id='btn-generar' class='fn bloque  waves-effect'> <i class='fa fa-1x fa-bolt'></i> Generar</button>";
								$html.= "</div>";//btn-cont

              $html.= "</div>";//select-opt

							$html.= "<div id='select-datos' class='fn enlinea floL boxshadow '>";
								$html.= "<div id='results-cont' class='fn bloque'>";


								$html.= "</div>";//btn-cont
								$html.= "<div id='btn-cont' class='fn bloque'>";
									$html.= "<div id='status-bar' class='fn enlinea floL'><span class='label fn'>Registros: </span><span class='count fn'></span> </div>";
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
