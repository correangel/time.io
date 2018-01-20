<?php 
	header('Content-type: application/json');
	if(isset($_POST['action'],$_POST['tb_for'],$_POST['tb_parametro'],$_POST['tb_valor'],$_POST['type'], $_POST['user']) && $_POST['action'] == 'save'){
		include_once '../includes/mysql.configuracion.php';
		include_once '../includes/mysql.funciones.php';
		

		$com = new com_mysql();
		$cnn = $com->_conectar(HOST,USER,PASS,DATA);

		$tb_for = $_POST['tb_for'];
		$tb_parametro = $_POST['tb_parametro'];
		$tb_valor = $_POST['tb_valor'];
		$type = $_POST['type'];
		$user = $_POST['user'];

		$query = 'call proc_insert_param(?,?,?,?,?);';
		if($res = $cnn->prepare($query)){
			$res->bind_param('sssss', $tb_for, $tb_parametro, $tb_valor, $type, $user); 
			if($res->execute()){
				$res->bind_result($status,$_id);
				$res->fetch();					 
				$res->close();
				$com->_desconectar($cnn);
				if($status === 'ok'){
					$div = "	<div id='$_id' class='param old fn enlinea boxshadow-low floL'>
										<div data-id='$_id' class='icono fn enlinea floL o3'>
											<i class='fa fa-2x fa-cog'></i>
										</div>
										<div class='campos fn enlinea'>
											<div data-id='$_id' data-field='_for' class='bloque'>
												<input value='$tb_for' title='For:' placeholder='For:' type='text'  id='tb_for' name='tb_for' required />
											</div>
											<div data-id='$_id' data-field='parametro' class='bloque'>
												<input value='$tb_parametro' title='Parameter:' placeholder='Parameter:' type='text' id='tb_parametro' name='tb_parametro' required />
											</div>
											<div data-id='$_id' data-field='valor' class='bloque'>
												<input value='$tb_valor' data-type='$type' title='Value:' placeholder='Value:' type='text' id='tb_valor' name='tb_valor' required />
											</div>
										</div>								
									</div>";
					$new =  "<div id='new' class='param new inactive fn enlinea boxshadow-low floL '>								
								<div class='ico fn bloque o2'>
									<i class='fa fa-2x fa-plus'></i>
								</div>
								<div class='fields bloque oculto'>
									<div data-action='none' class='icono fn enlinea floL o3 trans_a3s '>
										<i class='fa fa-2x fa-plus'></i>
									</div>
									<div class='campos fn enlinea'>
										<div data-id='new' data-field='_for' class='bloque'>
											<input value='' title='For:' placeholder='For:' type='text'  id='tb_for' name='tb_for' required />
										</div>
										<div data-id='new' data-field='parametro' class='bloque'>
											<input value='' title='Parameter:' placeholder='Parameter:' type='text' name='tb_parametro' id='tb_parametro' required />
										</div>
										<div data-id='new' data-field='valor' class='bloque'>
											<input value='' title='Value:' placeholder='Value:' type='text' id='tb_valor' name='tb_valor' required />
										</div>
									</div>											
								</div>									
							</div>";
					$response_array['status'] = $status;
					$response_array['div'] = $div;
					$response_array['new'] = $new;					
					$response_array['id'] = $_id;				
					$response_array['msg'] = 'Parameter inserted...';  
					echo json_encode($response_array);
				}else{
					$response_array['status'] = $status;									
					$response_array['id'] = $_id;				
					$response_array['msg'] = $_id;
					echo json_encode($response_array);
				}//end if
				
			}else{
				
				$response_array['status'] = 'error';
				$response_array['where'] = 'execute';
				$response_array['error'] = $res->error;
				$response_array['query'] = $query;
				$response_array['msg'] = 'Parametro No Insertado.';  
				echo json_encode($response_array);
			}//end if
		}else{
			
			$response_array['status'] = 'error';
			$response_array['where'] = 'prepare';
			$response_array['error'] = $res->error;
			$response_array['query'] = $query;
			$response_array['msg'] = 'Parametro No Insertado.';  
			echo json_encode($response_array);
		}//end if		


	}else{

		if(isset($_POST['action'],$_POST['id'])&& $_POST['action'] == 'delete'){
			include_once '../includes/mysql.configuracion.php';
			include_once '../includes/mysql.funciones.php';
			

			$com = new com_mysql();
			$cnn = $com->_conectar(HOST,USER,PASS,DATA);
			$id = $_POST['id'];
			$query = 'call proc_delete_param(?);';
			if($res = $cnn->prepare($query)){
				$res->bind_param('s', $id); 
				if($res->execute()){
					$res->close();
					$com->_desconectar($cnn);
					$response_array['status'] = 'ok';
					$response_array['id'] = $id;
					$response_array['msg'] = 'Parameter removed...';  
					echo json_encode($response_array);

				}else{
					$response_array['status'] = 'error';
					$response_array['where'] = 'execute';
					$response_array['error'] = $res->error;
					$response_array['query'] = $query;
					$response_array['msg'] = 'Parameter not removed...';  
					echo json_encode($response_array);
				}//end if
			}else{
				$response_array['status'] = 'error';
				$response_array['where'] = 'prepare';
				$response_array['error'] = $res->error;
				$response_array['query'] = $query;
				$response_array['msg'] = 'Parameter not removed...';  
				echo json_encode($response_array);
			}//end if	
			
		}else{
			$resp['msg'] = 'Error de Posteo';
			$resp['status'] = 'Error';
			echo json_encode($resp);
		}//end if
		
	}//end if
?>