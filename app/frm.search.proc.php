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
		$permisos = 0;
    if(isset($_POST['search'],$_POST['first'],$_POST['query_first'], $_POST['cols'])){
      $first = $_POST['first'];
      $query_name = $_POST['query_first'];
			$query = $com->_get_val($cnn, '_query_script', 'adm.queries', '_query_name', $query_name, 'nvarchar(256)' ,1);//'exec cat.proc_employees_for_operator_search_bynum ?';
			//echo $query;
			$cols = $_POST['cols'];
      $params = array(array(&$first , SQLSRV_PARAM_IN));
      $stmt = $com->_create_stmt($cnn,$query,$params);
      if($stmt){
        $html='';
				$shadow = false;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
					//$html.= json_encode($cols);
					$html.= "<div data-diag='".$_POST['diag']."'
												data-action='".$_POST['action']."'
												data-callback='".$_POST['callback']."'
												data-source='".$_POST['source']."'
												data-destino='".$_POST['destino']."'
												id='".$row[$cols[0]]."'
												class='fs cur-poi row ".($shadow?'shadow':'')."'>";
					$i=0;
					$x = 60/(count($cols) - 2);
					foreach ($cols as $key => $value) {
						if ($i==0) {
							$html.="<div id='$value'  class='fs cell send enlinea oculto' data-for='$value'>".$row[$value]."</div>";
						}else{
							$html.="<div id='$value'  style='width:calc(".($i==1?"25":$x)."% - 5px);' class='fs cell send enlinea elli' data-for='$value'>".trim($row[$value])."</div>";
						}//end if
						$i++;

					}//end if

            $html.="<div style='width:calc(15% - 5px);' class='fs for-click cell mid enlinea'><i class='fa fa-1x fa-circle-thin'></i></div>
         </div>";
				 if($shadow) $shadow=false; else $shadow = true;
        }//end while
        sqlsrv_free_stmt($stmt);
        $resp['html'] = $html;
        $resp['status']='ok';
      }else{
        $resp['status']='error';
        $resp['msg']='Error en query';
      }//end if
    }elseif(isset($_POST['search'],$_POST['vals'],$_POST['query_second'], $_POST['cols'])){
			$vals = $_POST['vals'];

      //$name = isset($_POST['name'])?$_POST['name']:'%';
      //$lastname = isset($_POST['lastname'])?$_POST['lastname']:'%';
      //$maidename = isset($_POST['maidename'])?$_POST['maidename']:'%';
      //$query = $_POST['query_second'];//'exec cat.proc_employees_for_operator_search_byname ?,?,?';
			$query_name = $_POST['query_second'];
			$query = $com->_get_val($cnn, '_query_script', 'adm.queries', '_query_name', $query_name, 'nvarchar(256)' ,1);
			$cols = $_POST['cols'];
			$params = array();
			foreach ($vals as $key => $value) {
				array_push($params, array(&$value[1],SQLSRV_PARAM_IN));
			}//end if

      $stmt = $com->_create_stmt($cnn,$query,$params);
      if($stmt){
        $html='';
				$shadow =false;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
					$html.= "<div data-diag='".$_POST['diag']."'
												data-action='".$_POST['action']."'
												data-callback='".$_POST['callback']."'
												data-source='".$_POST['source']."'
												data-destino='".$_POST['destino']."'
												id='".$row[$cols[0]]."'
												class='fs cur-poi row ".($shadow?'shadow':'')."'>";
					$i=0;
					$x = 60/(count($cols) - 2);
					foreach ($cols as $key => $value) {
						if ($i==0) {
							$html.="<div id='$value' class='fs cell send enlinea oculto' data-for='$value'>".$row[$value]."</div>";
						}else{
							$html.="<div id='$value' style='width:calc(".($i==1?"25":$x)."% - 5px);' class='fs cell send enlinea elli' data-for='$value'>".trim($row[$value])."</div>";
						}//end if
						$i++;

					}//end if

						$html.="<div style='width:calc(15% - 5px);' class='fs for-click cell mid enlinea'><i class='fa fa-1x fa-circle-thin'></i></div>
				 </div>";
				 if($shadow) $shadow=false; else $shadow = true;
        }//end while
        sqlsrv_free_stmt($stmt);
        $resp['html'] = $html;
        $resp['status']='ok';
      }else{
        $resp['status']='error';
        $resp['msg']='Error en query';
      }//end if
		}elseif(isset($_POST['search'], $_POST['action'], $_POST['dep']) && $_POST['action']=='employees_bydep'){
			$query = 'exec [cat].[proc_get_employees_from_dep] @dep = ?';
			$dep = $_POST['dep'];
			$params = array(array(&$dep , SQLSRV_PARAM_IN));
			$stmt = $com->_create_stmt($cnn,$query,$params);
			$html = '';
			if($stmt){
				$html.="<div id='tabla-employees' class='fn noselect' title='Departamento: ".$dep."'>";
					$html.="<div id='tabla-employees-head' class='fn'>";
						$html.="<div id='emple-iden' class='fs enlinea cur-def item-cell oculto'>id</div>
										<div id='emple-code' class='fs enlinea cur-def item-cell'>Codigo</div>
										<div id='emple-name' class='fs enlinea cur-def item-cell'>Nombre</div>
										<div id='emple-posi' class='fs enlinea cur-def item-cell'>Posición</div>
										<div id='emple-clas' class='fs enlinea cur-def item-cell'>Clase</div>
										<div id='emple-chec' class='fs enlinea cur-def item-cell'>Checa</div>";
					$html.='</div>';
					$html.="<div id='tabla-employees-rows' class='fn'>";
					while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
						$html.="<div id='".$row['employee_id']."' class='fs item bloque'>
											<div id='emple-iden' class='fs enlinea  item-cell oculto'>".$row['employee_id']."</div>
											<div id='emple-code' class='fs enlinea item-cell '>".$row['_alter_id']."</div>
											<div id='emple-name' class='fs enlinea item-cell '>".$row['_name']."</div>
											<div id='emple-posi' class='fs enlinea item-cell '>".$row['_position']."</div>
											<div id='emple-clas' class='fs enlinea item-cell '>".$row['_clase']."</div>
											<div id='emple-chec' class='fs enlinea item-cell '><i class='fa fa-1x ".($row['_checa']==1 ?'fa-check-square-o' :' fa-square-o' )."'></i></div>
										</div>";
					}//end while
					$html.='</div>';
				$html.='</div>';
				$resp['post'] = $_POST;
				$resp['html'] = $html;
				$resp['status']= 'ok';
				sqlsrv_free_stmt($stmt);
			}else{
				$resp['data'] = $_POST;
				$resp['status']= 'error';
			}//end if
		}elseif(isset($_POST['search'], $_POST['action'], $_POST['limit'], $_POST['txt'], $_POST['ope']) && $_POST['action']=='departamentos'){
			$query = 'exec [cat].[proc_deptos_for_operator_search_bycode_or_byname] ?, ?,?';
			$txt = $_POST['txt'];
			$ope = $_POST['ope'];
			$limit = $_POST['limit'];
			$params = array(array(&$txt , SQLSRV_PARAM_IN)
											,array(&$limit , SQLSRV_PARAM_IN)
											,array(&$ope , SQLSRV_PARAM_IN));
			$stmt = $com->_create_stmt($cnn,$query,$params);
			$html='';
			if($stmt){
				$c = 0;
				while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
					$c++;
					$html.="<div class='fs bloque inp-box-options-item' id='".$row['id_departamento']."'>
										<div id='depto-id' class='fs enlinea oculto'>".$row['id_departamento']."</div>
										<div id='depto-empl' class='fs enlinea oculto'>".$row['_employees']."</div>
										<div id='depto-code' class='fs enlinea'>".$row['_departamento_code']."</div>
										<div id='depto-name' class='fs enlinea' title='Empleados: ".$row['_employees']."'>".$row['_departamento_name']."</div>
									</div>";
				}//end while

				if($limit == 1){
					if ($c == 10) $en = 1; else $en = 0;
					$html.="<div data-enabled='".$en."' class='fs bloque inp-box-options-more ".($en== 1 ? 'enabled' : 'disabled')."'>
										<div class='fs enlinea'><i class='fa fa-1x fa-ellipsis-h'></i></div>
									</div>";
				}//end if
				$resp['post'] = $_POST;
				$resp['html'] = $html;
				$resp['status']= 'ok';
				sqlsrv_free_stmt($stmt);
			}else{
				$resp['data'] = $_POST;
				$resp['status']= 'error';
			}//end if


		}else{
			$resp['status'] = 'error';
			$resp['msg'] = 'Error de Posteo...';
		}//end if
  }else{
    $url = $com->_get_param($cnn, 'raiz');
    $resp['status'] = 'login';
    $resp['url'] = $url;
    $resp['msg'] = 'Sesion Caducada...';
  }//end if


  $com->_desconectar($cnn);
  echo json_encode($resp);
?>
