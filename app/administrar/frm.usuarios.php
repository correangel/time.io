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
		//echo $id_navigator ;
		if(isset($_POST['id_nav']) && $_read == 1) {

			$id_role = $com-> _get_val($cnn, 'id_role', 'cat.operator', 'operator_id', $id_operator, 'char(36)' ,1);
			$role = $com-> _get_val($cnn, '_role_name', 'cat.roles', 'id_role', $id_role, 'nvarchar(64)' ,1);
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


			if(intval($_read) === 1){

				$html = "<div id='frm-".$id_navigator."' data-ope='".$id_operator."' data-nav='".$id_navigator."' data-table='".boolval($tabla)."' data-query='".boolval($query)."' class='frm fn boxshadow mar10 oculto'>
								<div id='frm-tabs' class='fn floL enlinea '>
									<div data-tab='frm-table' class='fn tab isfrm bloque waves-effect active' data-title='Tabla de Usuarios'>
										<i class='fa fa-1x fa-table'></i>
									</div>
									<div data-tab='frm-add' class='fn tab isfrm bloque waves-effect' data-title='Agregar Usuario'>
										<i class='fa fa-1x fa-plus'></i>
									</div>";
					if($role === 'ADMINISTRATOR'){
					$html.= "<div data-tab='frm-menu' class='fn tab isfrm bloque waves-effect' data-title='Editar Permisos de Menu'>
										<i class='fa fa-1x fa-navicon'></i>
									</div>
									<div data-tab='frm-ause' class='fn tab isfrm bloque waves-effect' data-title='Editar Permisos de Ausentismos'>
										<i class='fa fa-1x fa-paperclip'></i>
									</div>";
					}//end if
					$html.= "<div data-tab='frm-dept' class='fn tab isfrm bloque waves-effect' data-title='Editar Permisos de departamentos'>
										<i class='fa fa-1x fa-tags'></i>
									</div>


								</div>
								<div id='frm-table' class='fs istab floL enlinea visible'>
									<div id='dialog-confirm' title='Eliminar registro?' class='fn oculto'>
											<div class='ico fn floL enlinea'><i class='fa fa-4x fa-warning'></i></div>
											<div class='txt fn floL enlinea'>El Registro se eliminará permanentemente, ¿Estás Seguro?</div>
									</div>";

					$html.= "<div id='rows-head' class='usuarios fs bloque'>
										<div id='col-user' class='floL fs colm enlinea ti input'><span class='fs ti'>Usuario:</span></div>
										<div id='col-dominio' class='floL fs colm enlinea ti input'><span class='fs ti'>Dominio:</span></div>
										<div id='col-nombre' class='floL fs colm enlinea ti input'><span class='fs ti'>Nombre:</span></div>
										<div id='col-apellido' class='floL fs colm enlinea ti input'><span class='fs ti'>Apellido:</span></div>
										<div id='col-code' class='floL fs colm enlinea ti input'><span class='fs ti'>Codigo:</span></div>
										<div id='col-rol' class='floL fs colm enlinea ti input'><span class='fs ti'>Rol:</span></div>
										<div id='col-departamento' class='floL fs colm enlinea ti input'><span class='fs ti'>Departamento:</span></div>
										<div id='col-tags' class='floL fs enlinea '><i class='fa fa-1x fa-tags'></i></div>
										<div class='tc floL fs colm enlinea ico' title='Eliminar'>R:</div>
									</div>";
									//$html .= $create->_columnas($cnn, $com, $id_navigator, $id_operator);
									//$html .= "</div>
					$html.= "<div style='width:8px;' class='tc floL fs enlinea scr'><i class='fa fa-1x fa-arrows-v'></i></div>
									<div id='rows-body' class='usuarios fm bloque'>";
											//$mn = new _menu();
											$query = 'exec cat.proc_get_operators @ope = ?';
											$params = array(array(&$id_operator, SQLSRV_PARAM_IN));
											if($stmt = sqlsrv_query($cnn,$query, $params)){
												while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
													$html.="<div id='".$row['operator_id']."' class='fm bloque row'>
																			<div data-col='_username' class='cell col-user floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_username']."</span></div>
																			<div data-col='_domain'  	class='cell col-dominio floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_domain']."</span></div>
																			<div data-col='_name'  		class='cell col-nombre floL ti fs  enlinea elli' ><span class='fm elli ti'>".$row['_name']."</span></div>
																			<div data-col='_lastname' class='cell col-apellido floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_lastname']."</span></div>
																			<div data-col='_alter_id' class='cell col-code floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_alter_id']."</span></div>
																			<div data-col='_role_name' data-id='".$row['id_role']."' class='cell col-rol floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_role_name']."</span></div>
																			<div data-col='_departamento_name' data-code='".$row['id_departamento']."' data-code='".$row['_departamento_name']."' data-code='".$row['_departamento_code']."' class='cell col-departamento floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_departamento_name']."</span></div>
																			<div data-action='tags' data-id='".$row['operator_id']."' class='action cell col-tags floL tc fm  enlinea ico'><i class='fa fa-1x fa-tags'></i></div>
																			<div data-action='remove' data-id='".$row['operator_id']."' class='action cell floL tc fm  enlinea ico'><i class='fa fa-1x fa-trash'></i></div>
																	</div>";
												}//end while
											}else{
												$html .="<div class='fn error red'>No Rows</div>";
											}//
											//$script="	";
											//$html .= $create->_alter_rows($cnn, $com, $id_navigator, $id_operator, $script);
									$html .="</div>
								</div>";
								$html.= "<div id='frm-edit-dialog' title='Editar Usuario' class='fs istab bloque oculto'>";
								//$html.="";
								$html .="</div>"; // frm-edit
								$html.= "<div id='frm-add' class='fs istab floL enlinea oculto'>";
									  $html.="<div id='gpo-wrapper' class='fn outer bloque'><div class='inner fn bloque'>";
										//-----------------------------------
										// Create Search Empleado
										//-----------------------------------
										$second = array(array('_name','Nombre'),array('_lastname', 'Ap. Paterno'), array('_maidename', 'Ap. Materno'));
										$qf = 'employee_by_code';
										$qs = 'employee_by_name';
										$cols= array('employee_id', '_alter_id','_name' ,'_lastname', '_maidenname');
										$html.=$create->_search('frm-add','Empleado','_alter_id', 'number', $second, $qf, $qs, $cols,'employee_id',0,0,'empleados','Codigo');
										//-----------------------------------
										// Create Campos
										//-----------------------------------
										$html.=$create->_alter_add($cnn,$com, $id_navigator, $id_operator);
										//-----------------------------------
										// Create Search Departamento
										//-----------------------------------
										/* ------ no necesario --------------
										$second = array(array('_departamento_name', 'Descripción'));
										$qf = 'depto_by_code';
										$qs = 'depto_by_name';
										$cols= array('id_departamento','_departamento_code' ,'_departamento_name');
										$html.=$create->_search('frm-add','Departamento','_departamento_code', 'number', $second, $qf, $qs, $cols,'id_departamento',0,0,'departamentos');
										*/
										//-----------------------------------
										// Create select roles
										//-----------------------------------
										$html.="<div id='select_id_role' class='fs gpo bloque'>
											<span style='width:calc(20% - 10px);'' class='enlinea fn td'>Rol:</span>";
											$html.=$create->_select($cnn, $com, 'cat.roles', 'id_role', '_role_name', 'id_role');
										$html.="</div>";


										//-----------------------------------
										// Botones personalizados
										//-----------------------------------
										$ubi = ($com->_get_param($cnn,  'btn_ubi') ?: 'right');
										$html.="<div id='frm-buttons' class='fn mt15 bloque'>
													<div style='width:calc(20% - 10px);'class='btn-dumm floL fn enlinea'></div>
													<div style='width:calc(80% - 35px);'class='btn-wrap ".$ubi." floL fn enlinea'>
														<div id='btn-can' class='btn fn can enlinea floL waves-effect '><i class='fa fa-1x fa-times'><span class='fn ml5 fnt'>Cancelar</span></i></div>
														<div id='btn-add-operator' class='btn fn add enlinea floL waves-effect waves-light'><i class='fa fa-1x fa-plus'><span class='fn ml5 fnt'>Agregar</span></i></div>
													</div>

												</div>";
										$html.="</div>

										</div>
										";
							$html.="</div>";
								$html.="<div id='frm-menu' class='fs istab floL enlinea oculto'>";
									$html.="<div class='frm-inner fs floL bloque' >";
								//<div id='gpo-wrapper' class='fn outer bloque'><div class='inner fn bloque'>";
								//-----------------------------------
								// Search Operator
								//-----------------------------------
										$second = array(array('_name','Nombre'),array('_lastname', 'Paterno'));
										$qf = 'operator_by_username';
										$qs = 'operator_by_name';
										$cols= array('operator_id', '_username','_name' ,'_lastname');
										$callback= 1;

										$html.=$create->_search('frm-menu','Usuario_Permisos','_username', 'text', $second, $qf, $qs, $cols,'operator_id',$callback,0,'navigator');
								//-----------------------------------

									$html.="</div>";
								$html.="</div>";//frm-menu
								//----------------------------------------------------------------------
								// Ausentismos
								$html.="<div id='frm-ause' class='fs istab floL enlinea oculto'>";
									$html.="<div class='frm-inner fs floL bloque' >";
									//$html.="<div id='gpo-wrapper' class='fn outer bloque'><div class='inner fn bloque'>";

										$second = array(array('_name','Nombre'),array('_lastname', 'Paterno'));
										$qf = 'operator_by_username';
										$qs = 'operator_by_name';
										$cols= array('operator_id', '_username','_name' ,'_lastname');
										$callback = 1;
										$html.=$create->_search('frm-ause','Usuario_Ausentismos','_username', 'text', $second, $qf, $qs, $cols,'operator_id',$callback,0, 'ausentismos');

									//$html.="</div></div>"; //frm-ause inner outer
									$html.="</div>";
								$html.="</div>";//frm-ause
								//----------------------------------------------------------------------
								// Departamentos
								$html.="<div id='frm-dept' class='fs istab floL enlinea oculto'>";
									$html.="<div class='frm-inner fs floL bloque' >";
										$second = array(array('_name','Nombre'),array('_lastname', 'Paterno'));
										$qf = 'operator_by_username';
										$qs = 'operator_by_name';
										$cols= array('operator_id', '_username','_name' ,'_lastname');
										$callback = 1;
										$html.=$create->_search('frm-dept','Usuario_Departamentos','_username', 'text', $second, $qf, $qs, $cols,'operator_id',$callback,0, 'departamentos');
									$html.="</div>";
								$html.="</div>";//frm-depto
								//----------------------------------------------------------------------
								// Passwords
								/*$html.="<div id='frm-pass' class='fs istab floL enlinea oculto'>";
										$html.="passwords";
								$html.="</div>";//frm-depto*/


						$html.="</div>";

					$html.="</div>";
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
		}elseif(isset($_POST['id_nav_proc'], $_POST['ope'], $_POST['action']) && ($_read== 1) && $_POST['action'] == 'navigator'){
			$action = $_POST['action'];
			$query = 'adm.proc_get_navigator_byoperator ?';
			$ope = $_POST['ope'];
			$params = array(array(&$ope, SQLSRV_PARAM_IN));
			$stmt = $com->_create_stmt($cnn, $query, $params);
			$heads = array('id_navigator','_assigned','_item_name','_full','_read','_write','_special');
			$cols = array('id_navigator','_assigned','_item_name','_full','_read','_write','_special');
			$class['tabla'] = 'anexa';$style['tabla'] = '';
			$class['heads'] = 'anexa_head';$style['heads'] = 'width:calc(100%);';
			$class['rows'] = 'anexa_rows';$style['rows'] = 'width:calc(100%);';
			$class['cell'] = array('oculto'
														,'oculto'
														,'cell der br visible'
														,'cur-poi cell mid br visible'
														,'cur-poi cell mid br visible'
														,'cur-poi cell mid br visible'
														,'cur-poi cell mid br visible');
			$class['btn'] = array('cell mid visible cur-poi');
			$style['cell'] = array('width:calc(0%);'
														,'width:calc(0%);'
														,'width:calc(50% - 30px);'
														,'width:calc(10%);'
														,'width:calc(10%);'
														,'width:calc(10%);'
														,'width:calc(10%);');
			$style['btn'] =	 array('width:calc(10%);');
			$btns = array(array('_set',"<i class='fa fa-1x fa-circle'></i>","<i class='fa fa-1x fa-circle-thin'></i>"));
			$script = "<script>

								</script>";
								$bool_btn['val']= 1;
								$bool_btn['btn']='_set';
								$bool_btn['col']= '_assigned';
			$html = $create->_create_tabla_interact($stmt,'tabla_navigator', $heads, $class, $style , $cols ,$btns , $script, $bool_btn, $action);
			$resp['html'] = $html;
			$resp['ope'] = $ope;
			$resp['status'] = 'ok';
			$resp['msg'] = 'Usar html';
		}elseif(isset($_POST['id_nav_proc'], $_POST['ope'], $_POST['action']) && ($_read== 1) && $_POST['action'] == 'ausentismos'){
			$action = $_POST['action'];
			$query = 'cat.proc_get_asuentismos_byope ?';
			$ope = $_POST['ope'];
			$nav = $_POST['id_nav_proc'];
			$params = array(array(&$ope, SQLSRV_PARAM_IN));
			$stmt = $com->_create_stmt($cnn, $query, $params);

			//$html = $create->_create_tabla_interact($stmt,'tabla_ausentismos', $heads, $class, $style , $cols ,$btns , $script, $bool_btn, $action);
			$html = "<div id='tabla-ausentismos' data-nav='".$nav."' data-ope='".$ope."' class='fn result-options enlinea '>";
			while($row = sqlsrv_fetch_array($stmt , SQLSRV_FETCH_ASSOC)){
				$html.= "<div data-val='".$row['_assigned']."' id='".$row['id_ausentismo']."' class='fn enlinea result-options-item tran-eas-5s '>";
					$html.= "<div class='fn floL element enlinea'>";
						$html.= "<div class='fg letter bloque'>".$row['_letra']."</div>";//letter
						$html.= "<div class='fm descr bloque'>".$row['_descripcion']."</div>";//descr
					$html.= "</div>";//element
					$html.= "<div id='btn-set' data-action='ausentismos' data-val='".$row['_assigned'] ."' class='fn floL assigned enlinea waves-effect'>";
						$html.= "<i class='fa fa-2x ".($row['_assigned']==1?'fa-check-square-o':'fa-square-o') ."'></i>";
					$html.= "</div>";//assigned
				$html.= "</div>";//item
			}//end while


			$html .= "</div>"; //tabla ausentismos
			$resp['html'] = $html;
			$resp['ope'] = $ope;
			$resp['status'] = 'ok';
			$resp['msg'] = 'Usar html';
		}elseif(isset($_POST['id_nav_proc'], $_POST['do_ope'], $_POST['dep'], $_POST['ope'], $_POST['action']) && ($_read== 1) && $_POST['action'] == 'departamento_favorito'){
			$query = 'exec [cat].[proc_set_favorite_departamento_from_ope] @ope = ?, @dep = ? , @log = 1, @do_ope = ?';
			$ope = $_POST['ope'];
			$dep = $_POST['dep'];
			$do_ope = $_POST['do_ope'];
			$params = array(array(&$ope, SQLSRV_PARAM_IN)
											,array(&$dep, SQLSRV_PARAM_IN)
											,array(&$do_ope, SQLSRV_PARAM_IN));
			$resp = $com->_exec_non_query($cnn, $query, $params);
		}elseif(isset($_POST['id_nav_proc'], $_POST['ope'], $_POST['action']) && ($_read== 1) && $_POST['action'] == 'departamentos'){
			$action = $_POST['action'];
			$query = 'cat.proc_get_departamentos_byope ?';
			$ope = $_POST['ope'];
			$nav = $_POST['id_nav_proc'];
			$params = array(array(&$ope, SQLSRV_PARAM_IN));
			$stmt = $com->_create_stmt($cnn, $query, $params);

			$html = "<div id='tabla-departamentos' data-nav='".$nav."' data-ope='".$ope."' class='fn result-options enlinea'>";

				$html.="<div class='fn bloque result-options-add'>";
				//if($stmt){
					$html.="<div class='fn floL inp enlinea'>
											<i class='floL fa fa-1x fa-search'></i>
											<input class='fn inp-box enlinea floL' type='text' placeholder='Codigo/Nombre de Departamento' required/>
											<div class='fn inp-box-options oculto'></div>
									</div>";
					//$html.="<div data-id='' class='fn floL dsc enlinea'><div class='fn lab enlinea floL'>Descripción:</div> <div class='fn val enlinea floL'></div> </div>";
					//$html.="<div id='btn-add-departamento' class='fn floL ico enlinea'><i class='enlinea fa fa-1x fa-check'> <span class='fn enlinea'>	Agregar</span></i></div>";
				$html.="</div>";
				$html.="<div id='act-departamentos' class='fn bloque'>";
					$html.="<div id='column-headers' class='act-departamentos-head fn'>";
					$html.="<div id='act-departamentos-item-favo' class='cur-def fn enlinea floL act-departamentos-item-cell bl w30px'><i class='fa fa-1x fa-star'></i></div>";
						$html.="<div id='act-departamentos-item-code' class='cur-def fn enlinea floL act-departamentos-item-cell bl w100px'>Codigo</div>";
						$html.="<div id='act-departamentos-item-name' class='cur-def fn enlinea floL act-departamentos-item-cell bl w100po'>Departamento</div>";
						$html.="<div id='act-departamentos-item-assi' class='cur-def fn enlinea floL act-departamentos-item-cell bl w100px'>Empleados</div>";
						//$html.="<div id='act-departamentos-item-cali' class='cur-def fn enlinea floL act-departamentos-item-cell bl w100px'>Califica</div>";
						$html.="<div id='act-departamentos-item-drop' class='cur-def fn enlinea floL act-departamentos-item-cell w100px'>Eliminar</div>";
						$html.="<div id='dummy-scrollbar' class='cur-def fn enlinea floL scr'><i class='fa fa-1x fa-arrows-v'></i></div>";
					$html.="</div>";
					$html.="<div class='act-departamentos-cont-item fn '>";
					if($stmt){
						while($row = sqlsrv_fetch_array($stmt , SQLSRV_FETCH_ASSOC)){
							$html.="<div id='".$row['id_departamento']."' class='act-departamentos-item fn '>";
								$html.="<div id='act-departamentos-item-favo' class='cur-poi act-departamentos-item-cell bl  w30px fn enlinea floL'><i class='fa fa-1x ".($row['_favorite']==1? 'fa-star gold': 'fa-star-o')."'></i></div>";
								$html.="<div id='act-departamentos-item-code' class='cur-def act-departamentos-item-cell bl  w100px fn enlinea floL'>".$row['_departamento_code']."</div>";
								$html.="<div id='act-departamentos-item-name' class='cur-def act-departamentos-item-cell bl w100po fn enlinea floL'>".$row['_departamento_name']."</div>";
								$html.="<div id='act-departamentos-item-empl' data-empleados='".$row['_empleados_directos']."' class='cur-poi act-departamentos-item-cell bl w100px fn enlinea floL'>".$row['_empleados_directos']."</div>";
								//$html.="<div id='act-departamentos-item-cali' data-califica='".$row['_califica']."' class='cur-poi act-departamentos-item-cell bl w100px fn enlinea floL'>".($row['_califica']==1?  "<i class='fa fa-1x fa-check-square-o'></i>": "<i class='fa fa-1x fa-square-o'></i>")."</div>";
								$html.="<div id='act-departamentos-item-drop' class='cur-poi act-departamentos-item-cell w100px fn enlinea floL'><i class='fa fa-1x fa-trash'></i></div>";
							$html.="</div>";
						}//end while
					}//end if
					$html.="</div>";

				$html.="</div>";
			$html.= "</div>";//tabla-departamentos
			$resp['html'] = $html;
			$resp['ope'] = $ope;
			$resp['status'] = 'ok';
			$resp['msg'] = 'Usar html';
		}elseif(isset($_POST['id_nav_proc'],$_POST['nav'], $_POST['ope'], $_POST['ins'], $_POST['do_ope']) && ($_full === 1 || $_write=== 1)){

			$nav = $_POST['nav'];
			$ope = $_POST['ope'];
			$ins = $_POST['ins'];
			$do_ope = $_POST['do_ope'];
			$query = "adm.proc_set_nav_to_ope_by_bit ?,?,?,?";
			$params = array(&$ope, &$nav, &$ins,&$do_ope);
			$resp = $com->_exec_non_query($cnn, $query, $params);
			//$resp['status'] = 'ok';
		}elseif(isset($_POST['id_nav_proc'],$_POST['aus'], $_POST['ope'], $_POST['ins'], $_POST['do_ope']) && ($_full === 1 || $_write=== 1)){

			$aus = $_POST['aus'];
			$ope = $_POST['ope'];
			$ins = $_POST['ins'];
			$do_ope = $_POST['do_ope'];
			$query = "exec cat.proc_set_aus_to_ope_by_bit ?,?,?,?";
			$params = array(&$ope, &$aus, &$ins,&$do_ope);
			$resp = $com->_exec_non_query($cnn, $query, $params);
			$resp['post'] = $_POST;
		}elseif(isset($_POST['id_nav_proc'],$_POST['dep'], $_POST['ope'], $_POST['do_ope'], $_POST['action'])&& $_POST['action'] == 'delete' && ($_full === 1 || $_write=== 1)){

			//$aus = $_POST['aus'];
			$ope = $_POST['ope'];
			$dep = $_POST['dep'];
			$do_ope = $_POST['do_ope'];
			$query = "exec [cat].[proc_delete_departamento_from_ope] ?,?,1,?";
			$params = array( &$ope,&$dep, &$do_ope);
			$resp = $com->_exec_non_query($cnn, $query, $params);
			$resp['post'] = $_POST;
		}elseif(isset($_POST['id_nav_proc'],$_POST['dep'], $_POST['ope'], $_POST['do_ope'], $_POST['action']) && $_POST['action'] == 'insert' && ($_full === 1 || $_write=== 1)){

			//$aus = $_POST['aus'];
			$ope = $_POST['ope'];
			$dep = $_POST['dep'];
			$do_ope = $_POST['do_ope'];
			$query = "exec [cat].[proc_add_departamento_to_ope] ?,?,?";
			$params = array(&$do_ope, &$ope,&$dep);
			$resp = $com->_exec_non_query($cnn, $query, $params);
			$resp['post'] = $_POST;


		}elseif(isset($_POST['id_nav_proc'],$_POST['nav'], $_POST['ope'], $_POST['upd'], $_POST['for'], $_POST['do_ope']) && ($_full === 1 || $_write=== 1)){
			$nav = $_POST['nav'];
			$ope = $_POST['ope'];
			$upd = $_POST['upd'];
			$for = $_POST['for'];
			$do_ope = $_POST['do_ope'];
			$query = "exec adm.proc_set_per_to_nav_by_bit ?,?,?,?,?";
			$params = array(&$ope, &$nav, &$for, &$upd, &$do_ope);
			$resp = $com->_exec_non_query($cnn, $query, $params);
		}elseif(isset($_POST['id_nav_proc']
								, $_POST['add']
								, $_POST['do_ope']) && ($_full === 1 || $_write=== 1)){

			$add = $_POST['add'];
			$do_ope = $_POST['do_ope'];
			/*@employee_id char(36)
			,@username nvarchar(64)
			,@domain nvarchar(64)
			,@name nvarchar(64)
			,@lastname nvarchar(64)
			,@email nvarchar(128)
			,@is_admin bit
			,@id_role char(36)
			,@id_depto char(36)
			,@do_ope char(36)*/
			$query = "exec adm.proc_add_operator_domain ?,?,?,?,?,?,?,?,?";
			$result = 0;
			$msg = '';
			$params = array(array(&$add['employee_id'],SQLSRV_PARAM_IN)
											,array(&$add['_username'],SQLSRV_PARAM_IN)
											,array(&$add['_domain'],SQLSRV_PARAM_IN)
											,array(&$add['_name'],SQLSRV_PARAM_IN)
											,array(&$add['_lastname'],SQLSRV_PARAM_IN)
											,array(&$add['id_role'],SQLSRV_PARAM_IN)
											,array(&$do_ope,SQLSRV_PARAM_IN)
											,array(&$result,SQLSRV_PARAM_OUT)
											,array(&$msg , SQLSRV_PARAM_OUT)
										);
			$stmt = sqlsrv_query($cnn, $query, $params);
			sqlsrv_next_result($stmt);
			sqlsrv_free_stmt($stmt);
			//$mn = new _menu();
			if($result == 1) {
				$html='';
				$query = 'exec cat.proc_get_operators @ope = ?';
				$params = array(array(&$id_operator, SQLSRV_PARAM_IN));
				if($stmt = sqlsrv_query($cnn,$query, $params)){
					while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
						$html.="<div id='".$row['operator_id']."' class='fm bloque row'>
												<div data-col='_username' class='cell col-user floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_username']."</span></div>
												<div data-col='_domain'  	class='cell col-dominio floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_domain']."</span></div>
												<div data-col='_name'  		class='cell col-nombre floL ti fs  enlinea elli' ><span class='fm elli ti'>".$row['_name']."</span></div>
												<div data-col='_lastname' class='cell col-apellido floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_lastname']."</span></div>
												<div data-col='_alter_id' class='cell col-code floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_alter_id']."</span></div>
												<div data-col='_role_name' data-id='".$row['id_role']."' class='cell col-rol floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_role_name']."</span></div>
												<div data-col='_departamento_name' data-code='".$row['id_departamento']."' data-code='".$row['_departamento_name']."' data-code='".$row['_departamento_code']."' class='cell col-departamento floL ti fs  enlinea elli'><span class='fm elli ti'>".$row['_departamento_name']."</span></div>
												<div data-action='tags' data-id='".$row['operator_id']."' class='action cell col-tags floL tc fm  enlinea ico'><i class='fa fa-1x fa-tags'></i></div>
												<div data-action='remove' data-id='".$row['operator_id']."' class='action cell floL tc fm  enlinea ico'><i class='fa fa-1x fa-trash'></i></div>
										</div>";
					}//end while
					sqlsrv_free_stmt($stmt);
				}else{
					$html .="<div class='fn error red'>No Rows</div>";
				}//
				$resp['status'] = 'ok';
				$resp['inserted'] = $html;
				$resp['msg'] = $msg;
			}else{
				$resp['status'] = 'error';
				$resp['post'] = $_POST;
				$resp['error'] = sqlsrv_errors();
				$resp['msg'] = $msg;
			}//end if
		}elseif(isset($_POST['id_nav_proc']
								, $_POST['id']
								, $_POST['action']) && ($_full === 1 || $_write=== 1) && $_POST['action']=='delete'){


			$query = "adm.proc_delete_row_from_table ?,?,?";
			$tabla = "cat.operator";
			$columna = "operator_id";
			$id = $_POST['id'];
			$params = array(&$tabla,&$columna,&$id);
			$resp = $com->_exec_non_query($cnn, $query, $params);
			if($resp['status'] == 'ok'){
				$resp['msg'] = 'Usuario Eliminado...';
				$resp['ico'] = 'fa-warning';
			}//end if
		}elseif(isset($_POST['id_nav_proc']
								, $_POST['id']
								, $_POST['action']) && ($_full === 1 || $_write=== 1) && $_POST['action']=='edit'){

			$query = 'exec adm.create_html_edit_from_row ?,?,?,?,?';
			$tabla = 'adm.vw_html_operator';
			$id = $_POST['id'];
			$id_field = 'operator_id';
			$field_values = '[operator_id]
				,convert(nvarchar(max),[_alter_id])[_alter_id]
				,convert(nvarchar(max),[employee_id])[employee_id]
				,convert(nvarchar(max),[_username])[_username]
				,convert(nvarchar(max),[_password])[_password]
				,convert(nvarchar(max),[_ad_user])[_ad_user]
				,convert(nvarchar(max),[_domain])[_domain]
				,convert(nvarchar(max),[_name])[_name]
				,convert(nvarchar(max),[_lastname])[_lastname]
				,convert(nvarchar(max),[_email])[_email]
				,convert(nvarchar(max),[_is_admin])[_is_admin]
				,convert(nvarchar(max),[active])[active]
				,convert(nvarchar(max),[_role_name])[_role_name]
				,convert(nvarchar(max),[id_role])[id_role]
				,convert(nvarchar(max),[_departamento_code])[_departamento_code]
				,convert(nvarchar(max),[id_departamento])[id_departamento]';
			 $field_names = '[_alter_id],[employee_id] ,[_username],[_password],[_ad_user],[_domain],[_name],[_lastname],[_email],[_is_admin],[active],[_role_name],[id_role],[_departamento_code],[id_departamento]';
			$params = array(&$tabla, &$id_field, &$id, &$field_values, &$field_names);
			$stmt = $com->_create_stmt($cnn, $query, $params);
			$html = '';
			if($stmt){
				while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
					$html.= $row['value'];
				}//end while
				$resp['status'] = 'ok';
				$resp['html'] = $html;
			}else{
				$resp['status'] = 'error';
				$resp['msg'] = 'Error stmt...';
			}//end if
		}else{
			$resp['status'] = 'error';
			$resp['msg'] = '2.Error de Posteo...';
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
