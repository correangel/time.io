<?php

//------------------------------------------------------------
class _creates{
	public function _menu($cnn, $com, $op){
		$query = 'exec adm.proc_get_nav @username =  ? ';
		$stmt = $com->_create_stmt($cnn, $query, array(&$op));
		$open_dad = false;
		$first_dad = true;
		$last_dad = '';
		$last_child = true;
		if($stmt){
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {

				if($row['_is_single'] === 1){
			      echo "<div id='".$row['id_navigator']."'
		      				class='nav-item waves-effect nav-single fn bloque'
		      				data-url='".$row['_url']."'
		      				title='".$row['_title']."'
		      				data-open='0'>
							<div class='sin-icon fm enlinea'><i class='fa fa-1x ".$row['_item_icon']."'></i></div>
							<div class='sin-title fm enlinea hide'>".$row['_item_name']."</div>
							<div class='icon-col fm enlinea hide'></div>
						</div>";
					$last_child = false;
				}//end if
				if($row['_is_father'] === 1){
					if($first_dad === false && $open_dad === true){
						echo "</div></div>";
					}//end if
					echo "<div  id='".$row['id_navigator']."'
								data-icon-selector='#sidebar #menu #".$row['id_navigator']." .icon-col'
								class='nav-item  nav-father fn bloque'
								title='".$row['_title']."'>
							<div class='dad-content waves-effect fn bloque'>
								<div class='dad-icon fm enlinea'><i class='fa fa-1x ".$row['_item_icon']."'></i></div>
								<div class='dad-title fm enlinea hide'>".$row['_item_name']."</div>
								<div class='icon-col fm enlinea hide'><i class='fa fa-1x fa-angle-right'></i></div>
							</div>
							<div class='nav-children bloque  oculto'>";
					$first_dad = false;
					$open_dad = true;
					$last_child = false;
				}//end if

				if($row['_is_child'] === 1){
					echo "<div  id='".$row['id_navigator']."'
								class='child-item fm bloque'
								data-url='".$row['_url']."'
		      					title='".$row['_title']."'
		      					data-open='0'>".$row['_item_name']."</div>";
		      		$last_child = true;
				}//end if
			}//end while
			if($last_child === true){
				echo "</div></div>";
			}
			sqlsrv_free_stmt( $stmt);
		}else{
			echo 'Error al crear Menu!';
		}//end if
	}//end class
	//-------------------------------------------------------------------------------


	public function _columnas($cnn, $com, $nav ,$op){
		$query = 'exec adm.proc_get_columnas @id_navigator =  ? ';
		$stmt = $com->_create_stmt($cnn, $query, array(&$nav));

		if($stmt){
			$resp ='';
			$resp.="<div style='width:61px;' data-ajustar='0' data-width='1' id='indentity' class=' tc floL fs colm enlinea'>ID:</div>";
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {

				if($row['_width'] != '' && $row['_width'] != '0'){
					switch ($row['_align']){
						case 'left':
							$align = 'ti';

							break;
						case 'center':
							$align = 'tc';

							break;
						case 'right':
							$align = 'td';
							break;
						default:
							break;
					}//end switch

					if ($row['_label']== 'active' || $row['_special_class'] == 'checkbox'){
						$resp.= "<div style='width:61px;' data-ajustar='0' data-width='1' id='".$row['id_columna']."' class='floL fs colm enlinea ".$align." ".$row['_special_class'] ."'><span class='fs ".$align."'>".ucfirst($row['_label']).":</span></div>";
					}else{
						$resp.= "<div data-ajustar='1' data-width='".$row['_width']."' id='".$row['id_columna']."' class='floL fs colm enlinea ".$align." ".$row['_special_class'] ."'><span class='fs ".$align."'>".ucfirst($row['_label']).":</span></div>";
					}//end if


				}//end if


			}//end while
			$write = intval($com->_get_permisos_nav($cnn, $nav,$op, '_write'));
			if($write){
				$resp.= "<div style='width:61px;' data-ajustar='0' data-width='1' class='tc floL fs colm enlinea ico' title='Eliminar'>R:</div>";
			}//end if
			return $resp;
			sqlsrv_free_stmt( $stmt);
		}else{
			echo "Error al crear columnas! - [$nav]";
		}//end if
	}//end function


	public function _rows($cnn, $com, $nav ,$op){
		$query = 'exec adm.proc_get_columnas @id_navigator =  ? ';
		$stmt = $com->_create_stmt($cnn, $query, array(&$nav));

		if($stmt){
			$cols='';
			$first = true;
			$i = 0;
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {

				switch ($row['_align']){
					case 'left':
						$align = 'ti';

						break;
					case 'center':
						$align = 'tc';

						break;
					case 'right':
						$align = 'td';

						break;
					default:
						break;
				}//end switch
				$ali[$row['_columna']] = $align;
				if (!$first) $cols.=', ';
				$cols .= $row['_columna'];
				$col[$i] = $row['_columna'];
				$cls[$i] = $row['_special_class'];
				$wid[$row['_columna']] = $row['_width'];
				$tabla = $row['_tabla'];
				$first = false;
				$i++;
			}//end while
			//echo json_encode($data);
			//echo $tabla;
			//echo $cols;
			sqlsrv_free_stmt( $stmt);
		}else{
			echo "Error al crear columnas! - [$nav]";
		}//end if

		$query = 'exec adm.proc_get_table_by_cols @columnas =  ?, @tabla = ? ';
		$stmt = $com->_create_stmt($cnn, $query, array(&$cols,&$tabla));

		if($stmt){
			$first = true;
			$le_col ='';
			$le_id ='';
			$c = 0;
			//$params = '';
			$resp ='';
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
				//if (!$first) $cols.=', ';
				//$params .= $row['_parametro'];
				$c++;

				$resp.= "<div id='".$row[$col[0]]."' data-id='".$c."' class='fm bloque row'>";
						$resp.= "<div style='width:61px;' data-ajustar='0' data-width='1' class='floL tc fm cell enlinea elli'>".$c."</div>";
				foreach ($col as $key=>$val) {
					if ($first == true){
						$le_col =$val;
						$first = false;
					}//end if
					$le_id = $row[$le_col];
					if($wid[$val] != '' && $wid[$val] != '0'){
						if ($col[$key] === 'active' || $cls[$key] == 'checkbox') {
							$ico_act = 'fa-check-square-o';
							if ($row[$val] == 0){
								$ico_act = 'fa-square-o';
							}// end if
							//echo "<div style='width:62px' class='floL tc fm cell enlinea elli'><i class='fa fa-1x fa-trash'></i></div>";
							//echo $col[$key];
							$resp.= "<div data-action='active' data-col='$val' style='width:61px;' data-ajustar='0' data-width='1' data-id-col='$le_col' data-id='$le_id' class='action floL ".$ali[$val]." fm cell enlinea ico'><i class='fm fa fa-1x ".$ico_act."'></i></div>";
						}else{
							$resp.= "<div data-ajustar='1' data-col='$val' data-width='".$wid[$val]."' data-id-col='$le_col' data-id='$le_id' class='floL ".$ali[$val]." fm cell enlinea elli'><span class='fm elli ".$ali[$val]."'>".$row[$val]."</span></div>";
						}//end if
					}//end if
				}//endfor
				$write = intval($com->_get_permisos_nav($cnn, $nav,$op, '_write'));
				if($write){
					$resp.= "<div data-action='remove' style='width:61px;' data-ajustar='0' data-width='1' data-id-col='$le_col' data-id='$le_id' class='action floL tc fm cell enlinea ico'><i class='fa fa-1x fa-trash'></i></div>";
				}//end if
				$resp.= "</div>";

			}//end while
			//echo json_encode($data);
			//echo $tabla;
			//echo $params;
			return $resp;
			sqlsrv_free_stmt( $stmt);
		}else{
			//$resp.='';
		}//end if
	}//end function
	public function _alter_rows($cnn, $com, $nav ,$op, $script){
		$query = 'exec adm.proc_get_columnas @id_navigator =  ? ';
		$stmt = $com->_create_stmt($cnn, $query, array(&$nav));

		if($stmt){
			$cols='';
			$first = true;
			$i = 0;
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {

				switch ($row['_align']){
					case 'left':
						$align = 'ti';

						break;
					case 'center':
						$align = 'tc';

						break;
					case 'right':
						$align = 'td';

						break;
					default:
						break;
				}//end switch
				$ali[$row['_columna']] = $align;
				if (!$first) $cols.=', ';
				$cols .= $row['_columna'];
				$col[$i] = $row['_columna'];
				$cls[$i] = $row['_special_class'];
				$wid[$row['_columna']] = $row['_width'];
				$tabla = $row['_tabla'];
				$first = false;
				$i++;
			}//end while
			//echo json_encode($data);
			//echo $tabla;
			//echo $cols;
			sqlsrv_free_stmt( $stmt);
		}else{
			echo "Error al crear columnas! - [$nav]";
		}//end if

		$query = 'exec adm.proc_get_table_by_cols @columnas =  ?, @tabla = ? ';
		$stmt = $com->_create_stmt($cnn, $query, array(&$cols,&$tabla));

		if($stmt){
			$first = true;
			$le_col ='';
			$le_id ='';
			$c = 0;
			//$params = '';
			$resp ='';
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
				//if (!$first) $cols.=', ';
				//$params .= $row['_parametro'];
				$c++;

				$resp.= "<div id='".$row[$col[0]]."' data-id='".$c."' class='fm bloque row'>";
						$resp.= "<div style='width:61px;' data-ajustar='0' data-width='1' class='floL tc fm cell enlinea elli'>".$c."</div>";
				foreach ($col as $key=>$val) {
					if ($first == true){
						$le_col =$val;
						$first = false;
					}//end if
					$le_id = $row[$le_col];
					if($wid[$val] != '' && $wid[$val] != '0'){
						if ($col[$key] === 'active' || $cls[$key] == 'checkbox') {
							$ico_act = 'fa-check-square-o';
							if ($row[$val] == 0){
								$ico_act = 'fa-square-o';
							}// end if
							//echo "<div style='width:62px' class='floL tc fm cell enlinea elli'><i class='fa fa-1x fa-trash'></i></div>";
							//echo $col[$key];
							$resp.= "<div data-action='active' data-col='$val' style='width:61px;' data-ajustar='0' data-width='1' data-id-col='$le_col' data-id='$le_id' class='action_$nav floL ".$ali[$val]." fm cell enlinea ico'><i class='fm fa fa-1x ".$ico_act."'></i></div>";
						}else{
							$resp.= "<div data-ajustar='1' data-col='$val' data-width='".$wid[$val]."' data-id-col='$le_col' data-id='$le_id' class='floL ".$ali[$val]." fm cell enlinea elli'><span class='fm elli ".$ali[$val]."'>".$row[$val]."</span></div>";
						}//end if
					}//end if
				}//endfor
				$write = intval($com->_get_permisos_nav($cnn, $nav,$op, '_write'));
				if($write){
					$resp.= "<div data-action='remove' style='width:61px;' data-ajustar='0' data-width='1' data-id-col='$le_col' data-id='$le_id' class='action_$nav floL tc fm cell enlinea ico'><i class='fa fa-1x fa-trash'></i></div>";
				}//end if
				$resp.= "</div>
				";

			}//end while
			//echo json_encode($data);
			//echo $tabla;
			//echo $params;
			$resp.=$script;
			return $resp;
			sqlsrv_free_stmt( $stmt);
		}else{
			//$resp.='';
		}//end if
	}//end function

	function _add($cnn,$com,$nav ,$op){
		$write = intval($com->_get_permisos_nav($cnn, $nav,$op, '_write'));
		if($write){
			$query = 'exec adm.proc_get_columnas @id_navigator =  ?';
			$stmt = $com->_create_stmt($cnn, $query, array(&$nav));
			if($stmt){
				$resp ='';
				$resp.="<div id='gpo-wrapper' class='fn outer bloque'><div class='inner fn bloque'>";
				while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
					if($row['_width'] != '' && $row['_width'] != '0' && $row['_needed'] == 1){
						//<div class='tran-bez-3s fn enlinea' id='".$row['_prefix'].$row['_label']."' style='width:35px;'><i class='fa fa-1x fa-check-square-o'></i></div>
						if ($row['_label']== 'active' || $row['_special_class'] == 'checkbox'){
							$resp.= "<div id='".$row['id_columna']."' class='fs gpo bloque'>
										<span style='width:calc(20% - 10px);' class='enlinea fn td'>".ucfirst($row['_columna']).":</span>
										<input class='read tran-bez-5s fn enlinea' id='".$row['_columna']."' type='checkbox'  name='".$row['_prefix'].$row['_label']."' checked>
									</div>";
						}else{
							if($row['_has_options'] == 1){
								$tabla = $row['_options_source'];
								$ids = $row['_options_id'];
								$sho = $row['_options_show'];
								//<input class='tran-bez-5s fn enlinea' id='".$row['_columna']."' style='width:calc(80% - 48px);' type='".$row['_input_type']."' name='".$row['_prefix'].$row['_label']."' placeholder='' required='".boolval($row['_required'])."'>
								$resp.= "<div id='".$row['id_columna']."' class='fs gpo bloque'>
											<span style='width:calc(20% - 10px);' class='enlinea fn td'>".ucfirst($row['_columna']).":</span>";
											$resp.=$this->_select($cnn,$com, $tabla, $ids, $sho, $row['_columna']);
									$resp.="</div>";
							}else{
								$resp.= "<div id='".$row['id_columna']."' class='fs gpo bloque'>
											<span style='width:calc(20% - 10px);' class='enlinea fn td'>".ucfirst($row['_columna']).":</span>
											<input class='read tran-bez-5s fn enlinea' id='".$row['_columna']."' type='".$row['_input_type']."' name='".$row['_prefix'].$row['_label']."' placeholder='' required='".boolval($row['_required'])."'>
										</div>";
								}//end if
						}//end if
					}//end if

				}//end while
				$ubi = ($com->_get_param($cnn,  'btn_ubi') ?: 'right');
				$resp.="<div id='frm-buttons' class='fn mt15 bloque'>
							<div style='width:calc(20% - 10px);'class='btn-dumm floL fn enlinea'></div>
							<div style='width:calc(80% - 35px);'class='btn-wrap ".$ubi." floL fn enlinea'>
								<div id='btn-can' class='btn fn can enlinea floL waves-effect '><i class='fa fa-1x fa-times'><span class='fn ml5 fnt'>Cancelar</span></i></div>
								<div id='btn-add' class='btn fn add enlinea floL waves-effect waves-light'><i class='fa fa-1x fa-plus'><span class='fn ml5 fnt'>Agregar</span></i></div>
							</div>
						</div>";
				$resp.="</div></div>";
			}else{
				echo "Error al crear _add! - [$nav]";
			}//end if
		}else{
			$resp = "<div id=frm_permisos´ class='frm fm boxshadow mar10'>
						<div id='permisos-wrapper'>
							<div id='sinpermisos' class='fn bloque rojo'><i class='fa fa-4x  fa-exclamation-triangle'></i></div>
							<div id='message' class='fn bloque'> Permisos insuficientes...</div>
						</div>
					</div>";

		}//end if
		return $resp;
		sqlsrv_free_stmt( $stmt);
	}//end function
	function _alter_add($cnn,$com,$nav ,$op){
		$write = intval($com->_get_permisos_nav($cnn, $nav,$op, '_write'));
		if($write){
			$query = 'exec adm.proc_get_columnas @id_navigator =  ?';
			$stmt = $com->_create_stmt($cnn, $query, array(&$nav));
			if($stmt){
				$resp ='';

				while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
					if($row['_width'] != '' && $row['_width'] != '0' && $row['_needed'] == 1){
						//<div class='tran-bez-3s fn enlinea' id='".$row['_prefix'].$row['_label']."' style='width:35px;'><i class='fa fa-1x fa-check-square-o'></i></div>
						if ($row['_label']== 'active' || $row['_special_class'] == 'checkbox'){
							$resp.= "<div id='".$row['id_columna']."' class='fs gpo bloque'>
										<span style='width:calc(20% - 10px);' class='enlinea fn td'>".ucfirst($row['_columna']).":</span>
										<input data-val='1' class='read tran-bez-5s fn enlinea' id='".$row['_columna']."' type='checkbox'  name='".$row['_prefix'].$row['_label']."' checked='true'>
									</div>";
						}else{
							if($row['_has_options'] == 1){
								$tabla = $row['_options_source'];
								$ids = $row['_options_id'];
								$sho = $row['_options_show'];
								//<input class='tran-bez-5s fn enlinea' id='".$row['_columna']."' style='width:calc(80% - 48px);' type='".$row['_input_type']."' name='".$row['_prefix'].$row['_label']."' placeholder='' required='".boolval($row['_required'])."'>
								$resp.= "<div id='".$row['id_columna']."' class='fs gpo bloque'>
											<span style='width:calc(20% - 10px);' class='enlinea fn td'>".ucfirst($row['_columna']).":</span>";
											$resp.=$this->_select($cnn,$com, $tabla, $ids, $sho, $row['_columna']);
									$resp.="</div>";
							}else{
								$resp.= "<div id='".$row['id_columna']."' class='fs gpo bloque'>
											<span style='width:calc(20% - 10px);' class='enlinea fn td'>".ucfirst($row['_columna']).":</span>
											<input class='read tran-bez-5s fn enlinea' id='".$row['_columna']."' type='".$row['_input_type']."' name='".$row['_prefix'].$row['_label']."' placeholder='' required='".boolval($row['_required'])."'>
										</div>";
								}//end if
						}//end if
					}//end if

				}//end while


			}else{
				echo "Error al crear _add! - [$nav]";
			}//end if
		}else{
			$resp = "<div id=frm_permisos´ class='frm fm boxshadow mar10'>
						<div id='permisos-wrapper'>
							<div id='sinpermisos' class='fn bloque rojo'><i class='fa fa-4x  fa-exclamation-triangle'></i></div>
							<div id='message' class='fn bloque'> Permisos insuficientes...</div>
						</div>
					</div>";

		}//end if
		return $resp;
		sqlsrv_free_stmt( $stmt);
	}//end function


	function _select($cnn, $com, $tabla, $ids, $shows, $col){
		$query = "select $ids [ids], $shows [shows] from $tabla where active = 1 order by $shows asc;";
		$params = array();//array(&$ids,&$shows,&$tabla);
		$stmt = $com->_create_stmt($cnn,$query,$params);
		$resp = '';
		if($stmt){
			$resp.="<select class='tran-bez-5s fn enlinea read' id='".$col."'  >";
				$resp.="<option value='' selected></option>";
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
				$resp.="<option value='".$row['ids']."'>".strtoupper($row['shows'])."</option>";
			}//end while
			$resp.="</select>";
		}else{
			if( ($errors = sqlsrv_errors() ) != null) {
				foreach( $errors as $error)  	{
					 //echo "SQLSTATE: ".$error[ 'SQLSTATE']."\n";
					 //echo "code: ".$error[ 'code']."\n";
					  $msg = $error[ 'message'];
				} //end foreach
		 	}//end if
			return $resp = "Error [$msg]";
		}//end if
		return $resp;
	}//end function


	function _color_from_letra($cnn,$com,$letra){
		$query = 'exec cat.proc_get_color_from_letra ?, ?';
		$color = '';
		$params = array(array(&$letra, SQLSRV_PARAM_IN)
									, array(&$color, SQLSRV_PARAM_OUT));
		$stmt= sqlsrv_query($cnn, $query, $params);
		if($stmt === false ) {
			//echo sqlsrv_errors();
			return 'error';
		}else{
			sqlsrv_next_result($stmt);
			sqlsrv_free_stmt($stmt);
			return $color;
		}//end if
	}//end function
	function _get_title($cnn,$com,$per, $emp ,$cn){
		$query = 'exec tra.proc_get_title_dia @per = ?, @emp = ?, @cn = ?, @title = ?';
		$title = '';
		$params = array(array(&$per, SQLSRV_PARAM_IN)
									, array(&$emp, SQLSRV_PARAM_IN)
									, array(&$cn, SQLSRV_PARAM_IN)
									, array(&$title, SQLSRV_PARAM_OUT));
		$stmt= sqlsrv_query($cnn, $query, $params);
		if($stmt === false ) {
			//echo sqlsrv_errors();
			return 'error';
		}else{
			sqlsrv_next_result($stmt);
			sqlsrv_free_stmt($stmt);
			return $title;
		}//end if
	}//end function

	function _search($tab,$title,$first, $ftype, $second, $qf, $qs, $cols, $destino, $callback ,$verbose, $action, $label='ID'){

		//$title // titulo del buscador, usado en los ids del DOM
		//$first // input primario
		//$ftype // tipo de input primario
		//$second // inputs secundarios, siempre textos
		//$qf // query principal
		//$qs // query secundario
		//$cols // columnas a consultar
		//$destino // input al que se escribiré el ident
		//$callback['name']
		//$callback['script']


		$html= "<div id='generic_source' data-query-first='$qf' data-cols='".json_encode($cols)."'class='".strtolower($title)." fs gpo bloque'>
							<span style='width:calc(20% - 10px);' class='enlinea fn td'>$label:</span>
							<input data-parent='#$tab'
										 data-action='$action'
										 data-callback='$callback'
										 data-source='#$tab #generic_source.".strtolower($title)."'
										 data-col='".$first."'
										 style='width:calc(30% - 48px);' class='lower tran-bez-5s fn enlinea' id='$first' type='$ftype' name='tb$first' ".($ftype==='number'?"min='1' max='999999'":"")." required/>

							<div
								data-action='".strtolower($title)."'
								data-source='.diag-source-".strtolower($title)."'
								data-parent='#$tab'
							 	id='btn_search_$title' class='btn fn search enlinea waves-effect waves-light ml5 '>
								<i class='fa fa-1x fa-search'></i>
							</div>
							<div id='options_$first' class='options fn oculto bloque box-shadow'>	</div>

							<div id='sg-$title' data-query-first='$qf' data-query-second='$qs' data-cols='".json_encode($cols)."' class='diag-source-".strtolower($title)." fn search_dialog oculto noselect'	title='Buscar $title'>
								<div class='fn search_wrapper'>
									<div class='fs params'>
											<div class='fs bloque'>
												<label for='tb$first' class='fs enlinea' >$label:</label>
												<input data-parent='#$tab'
												 			 data-action='$action'
															 data-callback='$callback'
															 data-source='.diag-source-".strtolower($title)."'
															 data-col='".$first."' class='first tran-bez-5s fs enlinea' id='tb$first' type='$ftype' name='tb$first' ".($ftype==='number'?"min='1' max='999999'":"")." required/>
											</div>";
											if($second){
												foreach ($second as $key => $value) {
													$html.= "<div class='fs bloque'>
																		<label for='tb".$value[0]."' class='fs enlinea' >".$value[1].":</label>
																		<input data-parent='#$tab'
																		 			 data-action='$action'
																					 data-callback='$callback'
																					 data-source='.diag-source-".strtolower($title)."'
																					 data-col='".$value[0]."' class='second tran-bez-5s fs enlinea' id='tb".$value[0]."' type='text' name='tb".$value[0]."' required/>
																	</div>";
													# code...
												}//end foreach
											}//end if

					$html.="</div>
									<div class='fn results'>
										<div class='fs heads'>
											<div style='width:calc(25% - 5px);' class='fs cell enlinea'>$first:</div>";
											$x = count($second);
											$x = 60 / $x;
											if($second) foreach ($second as $key => $value) $html.= "<div style='width:calc($x% - 5px);' class='fs cell enlinea'>".$value[1].":</div>";

							$html.="<div style='width:calc(15% - 5px);' class='fs cell mid enlinea'>_set:</div>
										</div>
										<div class='fn rows scrollable'>
											<!-- Ajax !-->
										</div>
									</div>
								</div>
							</div>
							";

								$html.="
						</div>

						<div id='generic_destino' class='fs gpo bloque'>
							<span style='width:calc(20% - 10px);' class='enlinea fn td oculto'>_ident:</span>
							<input class='tran-bez-5s fn enlinea read oculto' id='$destino' type='text' name='tb_$destino' data-value='' disabled/>
						</div>
						".($callback ? "<div id='callback-results' class='fn'></div>":'') ."
						";
				return $html;
	}//end function

	function _create_tabla_interact($stmt,$id, $heads, $class, $style , $cols ,$btns , $script, $bool_btn, $action){
		//$class['tabla']
		//$class['heads']
		//$class['rows']
		//$class['cell']
		//$bool_btn['val']
		//$bool_btn['btn']
		//$bool_btn['col']

		if($stmt){
			$html="<div id='$id' style='".$style['tabla']."' class='fn bloque ".$class['tabla']."' >";
				$html.="<div id='tabla-heads' style='".$style['heads']."'  class='fn bloque ".$class['heads']."'>";
					foreach ($heads as $key => $value) $html.="<div id='col$value' style='".$style['cell'][$key]."'  class='fn enlinea ".$class['cell'][$key]."'>$value:</div>";
					foreach ($btns as $key => $value) $html.="<div id='col".$value[0]."' style='".$style['btn'][$key]."' class='fn enlinea ".$class['btn'][$key]."'>".$value[0].":</div>";
				$html.="</div>";
				$html.="<div id='tabla-rows' style='".$style['rows']."' class='fn bloque ".$class['rows']."'>";
			$shadow = false;
			while($row = sqlsrv_fetch_array($stmt , SQLSRV_FETCH_ASSOC)){

					$html.="<div id='".$row[$cols[0]]."' ".($bool_btn['val']==1 ? ($row[$bool_btn['col']]==1 ?" data-val='1' ": " data-val='0'") : '' )." class='fn ".( $shadow === true ? 'shadow' :'')." row bloque'>";
						foreach ($cols as $key => $value) $html.="<div id='cell$value' style='".$style['cell'][$key]."' class='fn enlinea ".$class['cell'][$key]."'>".$row[$value]."</div>";
						foreach ($btns as $key => $value) $html.="<div data-action='$action' id='btn".$value[0]."'  style='".$style['btn'][$key]."' class='fn enlinea ".$class['btn'][$key]."'>".($bool_btn['val']==1 ? ($row[$bool_btn['col']]==1 ? $value[1] : $value[2]) : $value[1] )."</div>";
					$html.="</div>";
					if($shadow === false) $shadow = true; else $shadow = false;
			}//end while
				$html.="</div>";
				$html.=$script;
			$html.="</div>";
			sqlsrv_next_result($stmt);
			return $html;
		}//end if
	}//end function
}//end class


?>
