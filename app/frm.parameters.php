<?php if(isset($_POST['user'])): ?>
	<?php  
		include_once '../includes/mysql.configuracion.php';
		include_once '../includes/mysql.funciones.php';
		header('Content-type: text/html');

		$com = new com_mysql();
		$cnn = $com->_conectar(HOST,USER,PASS,DATA);
		
		$_user = strtoupper($cnn->real_escape_string($_POST['user']));		
		
		
		$query = "call proc_read_parameters(?);";
		//$query_h = "select grupo, hostname_ip, puerto, activo from tcp_monitor where user_insert = ? and grupo = ?;";
		
		if($result_g = $cnn->prepare($query)){
			$result_g->bind_param('s', $_user);
			if($result_g->execute()){	
				header('Content-type: text/html');	
				echo "<div id='frm_parameters' class='frm bloque'>";
				$result_g->bind_result($id, $parametro, $valor, $_for);				
				
				while($row = $result_g->fetch()){					
						echo "	<div id='$id' class='param old fn enlinea boxshadow-low floL'>
									<div data-id='$id' class='icono fn enlinea floL o3'>
										<i class='fa fa-2x fa-cog'></i>
									</div>
									<div class='campos fn enlinea'>
										<div data-id='$id' data-field='_for' class='bloque'>
											<input value='$_for' title='For:' placeholder='For:' type='text'  id='tb_for' name='tb_for' required />
										</div>
										<div data-id='$id' data-field='parametro' class='bloque'>
											<input value='$parametro' title='Parameter:' placeholder='Parameter:' type='text' id='tb_parametro' name='tb_parametro' required />
										</div>
										<div data-id='$id' data-field='valor' class='bloque'>
											<input value='$valor' title='Value:' placeholder='Value:' type='text' id='tb_valor' name='tb_valor' required />
										</div>
									</div>								
								</div>";
				}//en while
				echo "<div id='new' class='param new inactive fn enlinea boxshadow-low floL '>								
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
						</div>
					</div>";
				$result_g->close();
			}else{
				header('Content-type: application/json');
				$response_array['status'] = 'error';
				$response_array['where'] = 'prepare';
				$response_array['error'] = $result_g->error;
				$response_array['query'] = $query;
				$response_array['msg'] = 'Host No Insertado.';  
				echo json_encode($response_array);
			}//end if
		}else{
			header('Content-type: application/json');
			$response_array['status'] = 'error';
			$response_array['where'] = 'prepare';
			$response_array['error'] = $result_g->error;
			$response_array['query'] = $query;
			$response_array['msg'] = 'Host No Insertado.';  
			echo json_encode($response_array);
		}//end if

		/*echo "</div>
		<script>
		var src = '/scripts/jquery.parameters.js?version=0.3.8';
		loadScript(src)
			    .catch(loadScript.bind(null, src))
			    .then(function(){
			    	//----------------------------------------------
					// Inicializa Funciones
					//----------------------------------------------					
					
			    }, function(){
			    	console.log('Error Cargando Script');
			    });
		</script>
		";*/	

		$com->_desconectar($cnn);	
 ?>
<?php endif; ?>