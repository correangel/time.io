<?php
  header('Content-type: application/json');

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
    if(isset($_POST['action'],$_POST['year'])&&$_POST['action']==='rep-generales::get::periodos'){

      $year = $_POST['year'];
      $query = 'exec cat.proc_get_list_periodos_byyear @year = ?';
      $params = array(&$year);
      $html='';
       if($stmt = $com->_create_stmt($cnn, $query, $params)){


         while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
           $html.= "<div data-id='".$row['id_periodo']."'
                          class='fn option bloque'
                          data-ini='".$row['_ini']."'
                          data-fin='".$row['_fin']."'
                          data-label='".$row['_label']."'
                          data-parent='#cont-periodo'>".$row['_label']."</div>";
         }// end while

         sqlsrv_free_stmt($stmt);
         $resp['status'] = 'ok';
         $resp['html'] = $html;
         $resp['post'] = $_POST;
       }else{
         $resp['status'] = 'error';
         $resp['error'] = sqlsrv_errors();
         $resp['post'] = $_POST;
       }//end if
    }elseif(isset($_POST['action'],$_POST['ope'])&&$_POST['action']==='get::employees'){
         $query="exec cat.proc_get_employees_by_ope @ope = ? ,@dep = ".((isset($_POST['dep'])&& $_POST['dep']!=='*')?'?':'null')."";
         $ope = $_POST['ope'];
         $dep = $_POST['dep'];
         $params= array(array(&$ope, SQLSRV_PARAM_IN));
         if(isset($_POST['dep'])&& $_POST['dep']!=='*') array_push($params,array(&$dep,SQLSRV_PARAM_IN));
         $html='';
         if($stmt = $com->_create_stmt($cnn, $query, $params)){
           while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
             $html.="<div id='".$row['employee_id']."'
                          class='row fs bloque for-filtra'
                          data-alter='".$row['_alter_id']."'
                          data-name='".$row['_nombre']."'
                          data-departamento='".$row['_departamento_name']."'
                          data-departamento-code='".$row['_departamento_code']."'
                          data-posicion='".$row['_posicion_name']."'
                          data-posicion-code='".$row['_posicion_code']."'
                          data-locacion-code='".$row['_locacion_code']."'
                          data-clase='".$row['_clase']."'
                          data-hire='".$row['_hire_date']."'>
                          <div class='_alter_id fs enlinea floL'>".$row['_alter_id']."</div>
                          <div class='_nombre fs enlinea floL'>".$row['_nombre']."</div>
                     </div>";
           }//end while
           $resp['status'] = 'ok';
           $resp['html'] = $html;
           $resp['post'] = $_POST;
           sqlsrv_free_stmt($stmt);
         }else{

             $resp['status'] = 'error';
             $resp['error'] = sqlsrv_errors();
             $resp['msg'] = 'error sql...';
             $resp['post'] = $_POST;
         }//end if
    }elseif(isset($_POST['action'],$_POST['tip'],$_POST['ope'],$_POST['aus'],$_POST['dep'],$_POST['emp'])&& $_POST['action']==='rep-generales::exec::report'
                                                                                                         && $_POST['tip']==='rep-ausentismos'){
      $ope = $_POST['ope'];
      $aus = $_POST['aus'];
      $dep = $_POST['dep'];
      $emp = $_POST['emp'];
      $ini = $_POST['ini'];
      $fin = $_POST['fin'];

      $query = "exec rep.proc_ausentismo_byope @ope = ?
                                            	,@aus = ?
                                            	,@dep = ?
                                            	,@emp = ?
                                            	,@ini = ".((isset($_POST['ini'])&& $_POST['ini']!=='')?'?':'null')."
                                            	,@fin = ".((isset($_POST['fin'])&& $_POST['fin']!=='')?'?':'null')."";
      $params = array(array(&$ope,SQLSRV_PARAM_IN)
                      ,array(&$aus,SQLSRV_PARAM_IN)
                      ,array(&$dep,SQLSRV_PARAM_IN)
                      ,array(&$emp,SQLSRV_PARAM_IN));

      if(isset($_POST['ini'])&& $_POST['ini']!=='') array_push($params,array(&$ini,SQLSRV_PARAM_IN));
      if(isset($_POST['fin'])&& $_POST['fin']!=='') array_push($params,array(&$fin,SQLSRV_PARAM_IN));


      $html='';
      $c=0;

      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        $table= "<table id='rep-grid' class='fs table table-condensed table-hover table-striped'>
                        <thead class='fs'><tr>
                          <th class='fs oculto' data-column-id='IDTraAusentismo' >IDTraAusentismo</th>
                          <th class='fs' data-column-id='Codigo' data-type='numeric' data-order='asc' >Codigo</th>
                          <th class='fs' data-column-id='Nombres' >Nombres</th>
                          <th class='fs' data-column-id='ApellidoPaterno' >ApellidoPaterno</th>
                          <th class='fs' data-column-id='ApellidoMaterno' >ApellidoMaterno</th>
                          <th class='fs' data-column-id='Clase' >Clase</th>
                          <th class='fs' data-column-id='IDDepartamento' >IDDepartamento</th>
                          <th class='fs' data-column-id='Departamento' >Departamento</th>
                          <th class='fs oculto' data-column-id='IDPosicion' >IDPosicion</th>
                          <th class='fs oculto' data-column-id='Posicion' >Posicion</th>
                          <th class='fs' data-column-id='Periodo' >Periodo</th>
                          <th class='fs' data-column-id='FechaAusentismo' >FechaAusentismo</th>
                          <th class='fs' data-column-id='Letra' >Letra</th>
                          <th class='fs' data-column-id='DescripcionAusentismo' >DescripcionAusentismo</th>
                          <th class='fs' data-column-id='Causa' >Causa</th>
                          <th class='fs' data-column-id='FechaCaptura' >FechaCaptura</th>
                          <th class='fs' data-column-id='UsuarioCaptura' >UsuarioCaptura</th>
                          <th class='fs' data-column-id='Comentarios' >Comentarios</th>
                          <th class='fs' data-column-id='FechaTomada' >FechaTomada</th>
                     </tr></thead>
                  <tbody class='fs'>";

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $c++;
          $table.= "<tr class='fs' >
                    <td class='fs oculto'>".$row['IDTraAusentismo']."</td>
                    <td class='fs'>".$row['Codigo']."</td>
                    <td class='fs'>".$row['Nombres']."</td>
                    <td class='fs'>".$row['ApellidoPaterno']."</td>
                    <td class='fs'>".$row['ApellidoMaterno']."</td>
                    <td class='fs'>".$row['Clase']."</td>
                    <td class='fs'>".$row['IDDepartamento']."</td>
                    <td class='fs'>".$row['Departamento']."</td>
                    <td class='fs oculto'>".$row['IDPosicion']."</td>
                    <td class='fs oculto'>".$row['Posicion']."</td>
                    <td class='fs'>".$row['Periodo']."</td>
                    <td class='fs'>".$row['FechaAusentismo']."</td>
                    <td class='fs'>".$row['Letra']."</td>
                    <td class='fs'>".$row['DescripcionAusentismo']."</td>
                    <td class='fs'>".$row['Causa']."</td>
                    <td class='fs'>".$row['FechaCaptura']."</td>
                    <td class='fs'>".$row['UsuarioCaptura']."</td>
                    <td class='fs'>".$row['Comentarios']."</td>
                    <td class='fs'>".$row['FechaTomada']."</td>
                </tr>";
        }// end while
        $table.= "</tbody></table>";
        //$body.= "</div>";
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['table'] = $table;
        $resp['count'] = $c;
        //$resp['body'] = $body;
        //$resp['head'] = $head;
        $resp['post'] = $_POST;
      }else{
        // º$resp['query'] = $query;
        $resp['status'] = 'error';
        $resp['error'] = sqlsrv_errors();
        $resp['post'] = $_POST;
      }//end if
    }elseif(isset($_POST['action'],$_POST['tip'],$_POST['ope'],$_POST['dep'],$_POST['emp'])&& $_POST['action']==='rep-generales::exec::report'
                                                                                           && $_POST['tip']==='rep-horas-extras'){
      $ope = $_POST['ope'];
      $dep = $_POST['dep'];
      $emp = $_POST['emp'];
      $ini = $_POST['ini'];
      $fin = $_POST['fin'];

      $query = "exec rep.proc_horas_extras_byope @ope = ?
                                              	,@dep = ?
                                              	,@emp = ?
                                              	,@ini = ".((isset($_POST['ini'])&& $_POST['ini']!=='')?'?':'null')."
                                              	,@fin = ".((isset($_POST['fin'])&& $_POST['fin']!=='')?'?':'null')."";
      $params = array(array(&$ope,SQLSRV_PARAM_IN)
                      ,array(&$dep,SQLSRV_PARAM_IN)
                      ,array(&$emp,SQLSRV_PARAM_IN));

      if(isset($_POST['ini'])&& $_POST['ini']!=='') array_push($params,array(&$ini,SQLSRV_PARAM_IN));
      if(isset($_POST['fin'])&& $_POST['fin']!=='') array_push($params,array(&$fin,SQLSRV_PARAM_IN));


      $html='';
      $c=0;

      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        $table= "<table id='rep-grid' class='fs table table-condensed table-hover table-striped'>
                        <thead class='fs'><tr>
                          <th class='fs oculto' data-column-id='IDHorasExtrasLog' >IDHorasExtrasLog</th>
                          <th class='fs' data-column-id='Codigo' data-type='numeric' data-order='asc' >Codigo</th>
                          <th class='fs' data-column-id='Nombres' >Nombres</th>
                          <th class='fs' data-column-id='ApellidoPaterno' >ApellidoPaterno</th>
                          <th class='fs' data-column-id='ApellidoMaterno' >ApellidoMaterno</th>
                          <th class='fs' data-column-id='Clase' >Clase</th>
                          <th class='fs' data-column-id='IDDepartamento' >IDDepartamento</th>
                          <th class='fs' data-column-id='Departamento' >Departamento</th>
                          <th class='fs oculto' data-column-id='IDPosicion' >IDPosicion</th>
                          <th class='fs oculto' data-column-id='Posicion' >Posicion</th>
                          <th class='fs' data-column-id='Periodo' >Periodo</th>
                          <th class='fs' data-column-id='FechaJornada' >FechaJornada</th>
                          <th class='fs' data-column-id='Horas' >Horas</th>
                          <th class='fs' data-column-id='FechaCaptura'>FechaCaptura</th>
                          <th class='fs' data-column-id='UsuarioCaptura'>UsuarioCaptura</th>

                     </tr></thead>
                  <tbody class='fs'>";

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $c++;
          $table.= "<tr class='fs' >
                    <td class='fs oculto'>".$row['IDHorasExtrasLog']."</td>
                    <td class='fs'>".$row['Codigo']."</td>
                    <td class='fs'>".$row['Nombres']."</td>
                    <td class='fs'>".$row['ApellidoPaterno']."</td>
                    <td class='fs'>".$row['ApellidoMaterno']."</td>
                    <td class='fs'>".$row['Clase']."</td>
                    <td class='fs'>".$row['IDDepartamento']."</td>
                    <td class='fs'>".$row['Departamento']."</td>
                    <td class='fs oculto'>".$row['IDPosicion']."</td>
                    <td class='fs oculto'>".$row['Posicion']."</td>
                    <td class='fs'>".$row['Periodo']."</td>
                    <td class='fs'>".$row['FechaJornada']."</td>
                    <td class='fs'>".$row['Horas']."</td>
                    <td class='fs'>".$row['FechaCaptura']."</td>
                    <td class='fs'>".$row['UsuarioCaptura']."</td>
                </tr>";
        }// end while
        $table.= "</tbody></table>";
        //$body.= "</div>";
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['table'] = $table;
        $resp['count'] = $c;
        //$resp['body'] = $body;
        //$resp['head'] = $head;
        $resp['post'] = $_POST;
      }else{
        // º$resp['query'] = $query;
        $resp['status'] = 'error';
        $resp['error'] = sqlsrv_errors();
        $resp['post'] = $_POST;
      }//end if
    }elseif(isset($_POST['action'],$_POST['tip'],$_POST['ope'],$_POST['dep'],$_POST['emp'])&& $_POST['action']==='rep-generales::exec::report'
                                                                                           && $_POST['tip']==='rep-jornadas'){
      $ope = $_POST['ope'];
      $dep = $_POST['dep'];
      $emp = $_POST['emp'];
      $ini = $_POST['ini'];
      $fin = $_POST['fin'];

      $query = "exec rep.proc_jornadas_byope @ope = ?
                                              	,@dep = ?
                                              	,@emp = ?
                                              	,@ini = ".((isset($_POST['ini'])&& $_POST['ini']!=='')?'?':'null')."
                                              	,@fin = ".((isset($_POST['fin'])&& $_POST['fin']!=='')?'?':'null')."";
      $params = array(array(&$ope,SQLSRV_PARAM_IN)
                      ,array(&$dep,SQLSRV_PARAM_IN)
                      ,array(&$emp,SQLSRV_PARAM_IN));

      if(isset($_POST['ini'])&& $_POST['ini']!=='') array_push($params,array(&$ini,SQLSRV_PARAM_IN));
      if(isset($_POST['fin'])&& $_POST['fin']!=='') array_push($params,array(&$fin,SQLSRV_PARAM_IN));


      $html='';
      $c=0;

      if($stmt = $com->_create_stmt($cnn, $query, $params)){
        $table= "<table id='rep-grid' class='fs table table-condensed table-hover table-striped'>
                        <thead class='fs'><tr>
                          <th class='fs oculto' data-column-id='IDJornada' >IDJornada</th>
                          <th class='fs' data-column-id='Codigo' data-type='numeric' data-order='asc' >Codigo</th>
                          <th class='fs' data-column-id='Nombres' >Nombres</th>
                          <th class='fs' data-column-id='ApellidoPaterno' >ApellidoPaterno</th>
                          <th class='fs' data-column-id='ApellidoMaterno' >ApellidoMaterno</th>
                          <th class='fs' data-column-id='Clase' >Clase</th>
                          <th class='fs' data-column-id='IDDepartamento' >IDDepartamento</th>
                          <th class='fs' data-column-id='Departamento' >Departamento</th>
                          <th class='fs oculto' data-column-id='IDPosicion' >IDPosicion</th>
                          <th class='fs oculto' data-column-id='Posicion' >Posicion</th>
                          <th class='fs' data-column-id='Periodo' >Periodo</th>
                          <th class='fs' data-column-id='FechaInicio' >FechaInicio</th>
                          <th class='fs' data-column-id='Entrada' >Entrada</th>
                          <th class='fs' data-column-id='Salida'>Salida</th>
                          <th class='fs' data-column-id='Jornada'>Jornada</th>
                          <th class='fs' data-column-id='DispositivoEnt'>DispositivoEnt</th>
                          <th class='fs' data-column-id='DispositivoSal'>DispositivoSal</th>

                     </tr></thead>
                  <tbody class='fs'>";

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $c++;
          $table.= "<tr class='fs' >
                    <td class='fs oculto'>".$row['IDJornada']."</td>
                    <td class='fs'>".$row['Codigo']."</td>
                    <td class='fs'>".$row['Nombres']."</td>
                    <td class='fs'>".$row['ApellidoPaterno']."</td>
                    <td class='fs'>".$row['ApellidoMaterno']."</td>
                    <td class='fs'>".$row['Clase']."</td>
                    <td class='fs'>".$row['IDDepartamento']."</td>
                    <td class='fs'>".$row['Departamento']."</td>
                    <td class='fs oculto'>".$row['IDPosicion']."</td>
                    <td class='fs oculto'>".$row['Posicion']."</td>
                    <td class='fs'>".$row['Periodo']."</td>
                    <td class='fs'>".$row['FechaInicio']."</td>
                    <td class='fs'>".$row['Entrada']."</td>
                    <td class='fs'>".$row['Salida']."</td>
                    <td class='fs'>".(gmdate('H:i', floor(($row['Jornada']/60) * 3600)))."</td>
                    <td class='fs'>".$row['DispositivoEnt']."</td>
                    <td class='fs'>".$row['DispositivoSal']."</td>
                </tr>";
        }// end while
        $table.= "</tbody></table>";
        //$body.= "</div>";
        sqlsrv_free_stmt($stmt);
        $resp['status'] = 'ok';
        $resp['table'] = $table;
        $resp['count'] = $c;
        //$resp['body'] = $body;
        //$resp['head'] = $head;
        $resp['post'] = $_POST;
      }else{
        // º$resp['query'] = $query;
        $resp['status'] = 'error';
        $resp['error'] = sqlsrv_errors();
        $resp['post'] = $_POST;
      }//end if
    }else{
      $resp['status'] = 'error';
      $resp['msg'] = 'Posteo Incompleto...';
      $resp['post'] = $_POST;
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
