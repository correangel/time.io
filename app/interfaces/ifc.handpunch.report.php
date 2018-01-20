<?php
    header('Content-type: application/json');
    //$resp['data'] = 'hola mundo';
    //echo json_encode($resp);
    if(isset($_POST['time'], $_POST['smtpto'],$_POST['smtphost'],$_POST['smtpport'],$_POST['pais'],$_POST['source'],$_POST['destino'])){
      //---------------------------------------------------------------------------------------------------
      $source = $_POST['source'];
      $destino = $_POST['destino'];
      //---------------------------------------------------------------------------------------------------
      // Includes de clases y constantes - Conexion básica
      include_once "../../includes/constantes.php";
      include_once "../../includes/class.mssql.php";
      include_once '../../includes/class.mailer.php';
      //---------------------------------------------------------------------------------------------------
      // Objeto de mssql
      $com = new com_mssql();
      $cnn = $com->_conectar_win(HOST,DATA);

      //---------------------------------------------------------------------------------------------------
      // Obtengo datos de conexion a db source
      //$host_s = $com->_get_val($cnn, '_server', 'adm.conexiones', '_name', $source, 'nvarchar(32)' ,1);
      //$user_s = $com->_get_val($cnn, '_user', 'adm.conexiones', '_name', $source, 'nvarchar(32)' ,1);
      //$pass_s = $com->_get_val($cnn, '_pass', 'adm.conexiones', '_name', $source, 'nvarchar(32)' ,1);
      //$data_s = $com->_get_val($cnn, '_data', 'adm.conexiones', '_name', $source, 'nvarchar(32)' ,1);

      //---------------------------------------------------------------------------------------------------
      // Obtengo datos de conexion a db destino
      $host_d = $com->_get_val($cnn, '_server', 'adm.conexiones', '_name', $destino, 'nvarchar(32)' ,1);
      $user_d = $com->_get_val($cnn, '_user', 'adm.conexiones', '_name', $destino, 'nvarchar(32)' ,1);
      $pass_d = $com->_get_val($cnn, '_pass', 'adm.conexiones', '_name', $destino, 'nvarchar(32)' ,1);
      $data_d = $com->_get_val($cnn, '_data', 'adm.conexiones', '_name', $destino, 'nvarchar(32)' ,1);
      //---------------------------------------------------------------------------------------------------

      //---------------------------------------------------------------------------------------------------
      // Conexion destino
      $cnn_d = $com->_conectar_uid($host_d,$user_d,$pass_d,$data_d);

      //---------------------------------------------------------------------------------------------------
      $mailer = new mailer();
      $smtphost = $_POST['smtphost'];// || 'SRRMPSPR';
			$smtpport = intval($_POST['smtpport']);// || 25;
      $smtpto = $_POST['smtpto'];
			$from = "IFC HandPunch RCD"; //$_POST['smtpfrom'];// || 'MonitoreoRCD';
      //---------------------------------------------------------------------------------------------------
      $pais = $_POST['pais'];

      //try {
        //$server_ip = $_SERVER['REMOTE_ADDR'];
  			//$server = gethostbyaddr($server_ip);
      //} catch (Exception $e) {
        $server_ip = $_SERVER['LOCAL_ADDR'];
  			$server = $_SERVER['SERVER_NAME'];
      //}//end catch


      //---------------------------------------------------------------------------------------------------
      // Inicializo variables de status
      $status = 'OK';
      $status_rows='OK';
      $status_checada='OK';
      $status_checadores='OK';
      //---------------------------------------------------------------------------------------------------

      $dirc = explode(',', $smtpto);
      $time = (new \DateTime())->format('Y-m-d H:i:s');

      $query = 'select top 1
                	--h.*,
                	--,c.*
                	c.FECHA_HORA [Checada]
                	,datediff(hour,c.FECHA_HORA, getdate()) [Diferencia]
                from ifc.handpunch h
                left join dbo.checa2 c on h.id_destino = c.rowguid
                where h.id_source is not null
                order by fecha_insert desc;';
      $stmt = $com->_create_stmt($cnn_d, $query, array());
      //---------------------------------------------------------------------------------------------------
      // Recorro status de checadores
      if($stmt){
        //$body.= $stmt;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
          $checada_dt = $row['Checada'];
          if(intval($row['Diferencia'])> 3) {
            $status_checada = 'Error';
          }//end if
        }// end while
        sqlsrv_free_stmt( $stmt);
      }// end if<tr>
      $checada = $checada_dt->format('Y-m-d H:i:s');
      //$stmt = nothing;

      //---------------------------------------------------------------------------------------------------
      // Recorro status de checadores
      $query = 'exec ifc.proc_ids_pendientes_count;';
      $stmt = $com->_create_stmt($cnn_d, $query, array());
      if($stmt){
        //$body.= $stmt;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
          $rows = $row['pendientes'];
          if(intval($rows)>= 1000) {
            $status_rows = 'Error';
          }//end if
        }// end while
        sqlsrv_free_stmt( $stmt);//$stmt->close();
      }// end if<tr>

      //---------------------------------------------------------------------------------------------------
      // Recorro status de checadores
      $query = 'exec ifc.proc_device_status @hours = 1;';
      $stmt = $com->_create_stmt($cnn_d, $query, array());
      if($stmt){
        //$body.= $stmt;
        $devices = "";
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
          $devices.= "<tr>
                      <td width='120' bgcolor='#EBF4F7'><font face='verdana' size='2'>".$row['DEVICE']."</font></td>
                      <td width='194' bgcolor='#F9F9F5'><font face='verdana' size='2'>".$row['LOCATION']."</font></td>
                      <td width='194' bgcolor='#F9F9F5'><font color='".($row['STATUS']=='ERROR'? 'crimson': 'green')."' face='verdana' size='2'>".$row['STATUS']."</font></td>
                      <td width='194' bgcolor='#F9F9F5'><font color='".(intval($row['TASKS'])>0? 'crimson': 'green')."' face='verdana' size='2'>".$row['TASKS']."</font></td>
                    </tr>";

          if($row['STATUS']== 'ERROR') {
            $status_checadores = 'Error';
          }//end if
        }// end while
        sqlsrv_free_stmt( $stmt);
      }// end if<tr>
      //---------------------------------------------------------------------------------------------------
      // Valor de General del Reporte
      if ($status_checada == 'Error' || $status_rows == 'Error' || $status_checadores == 'Error') $status= 'Errores';
      $asun = "Reporte IFC HandPunch [".$status."][".$pais."][".$server."][".$time."]";
      //---------------------------------------------------------------------------------------------------
      // Valor de Body en HTML
      $body = "<div bgcolor='#ffffff' text='#000000' link='#0000ff' vlink='#0000ff' alink='#0000ff'><div class='adM'>

      </div><table border='0' cellpadding='5' cellspacing='1' width='100%'>
        <tbody>
          <tr>
            <td colspan='3' bgcolor='#294B72'><font  color='white' face='verdana' size='2'><b>Reporte de Status</b></font></td>
          </tr>
          <tr>
            <td colspan='3' bgcolor='#F9F9F5'><font color='".($status == 'Errores' ? 'crimson':'green')."' face='verdana' size='2'>[$status]</font><font face='verdana' size='2'> - ".($status == 'Errores' ? 'Se han encontrado errores en la revisión.':'No se han encontrado errores que reportar.')."</font></td>
          </tr>
          <tr>
            <td colspan='3' bgcolor='#294B72'><font  color='white' face='verdana' size='2'><b>Detalles de Status</b></font></td>
          </tr>
          <tr>
            <td width='120' bgcolor='#EBF4F7'><font face='verdana' size='2'>Registros Pendientes:</font></td>
            <td width='120' bgcolor='#F9F9F5'><font face='verdana, helvetica, arial' size='2'>$rows</font></td>
            <td width='460' bgcolor='#F9F9F5'><font color='".($status_rows == 'Error'? 'crimson':'green')."' face='verdana, helvetica, arial' size='2'>$status_rows</font></td>
          </tr>
          <tr>
            <td width='120' bgcolor='#EBF4F7'><font face='verdana' size='2'>Ultima checada procesada:</font></td>
            <td width='120' bgcolor='#F9F9F5'><font face='verdana, helvetica, arial' size='2'>$checada</font></td>
            <td width='460' bgcolor='#F9F9F5'><font color='".($status_checada == 'Error'? 'crimson':'green')."' face='verdana, helvetica, arial' size='2'>$status_checada</font></td>
          </tr>


        </tbody>
      </table>
      <table border='0' cellpadding='5' cellspacing='1' width='100%'>
        <tbody>
          <tr>
            <td colspan='4' bgcolor='#294B72'><font color='white' face='verdana' size='2'><b>Status Checadores</b></font></td>
          </tr>
          <tr>
            <td width='120' bgcolor='#EBF4F7'><font face='verdana' size='2'><strong>Dispositivo</strong></font></td>
            <td width='194' bgcolor='#F9F9F5'><font face='verdana' size='2'><strong>Locacion</strong></font></td>
            <td width='194' bgcolor='#F9F9F5'><font face='verdana' size='2'><strong>Status</strong></font></td>
            <td width='194' bgcolor='#F9F9F5'><font face='verdana' size='2'><strong>Tareas</strong></font></td>
          </tr>";
            $body.=$devices;

      $body.="</tbody>
      </table>
      <table border='0' cellpadding='5' cellspacing='1' width='100%'>
        <tbody>
          <tr>
            <td colspan='2' bgcolor='#294B72'><font color='white' face='verdana' size='2'><b>Ubicación de IFC</b></font></td>
          </tr>
          <tr>
            <td width='120' bgcolor='#EBF4F7'><font face='verdana' size='2'>Hostname:</font></td>
            <td width='580' bgcolor='#F9F9F5'><font face='verdana' size='2'>$server</font></td>
          </tr>
          <tr>
            <td width='120' bgcolor='#EBF4F7'><font face='verdana' size='2'>IP:</font></td>
            <td width='580' bgcolor='#F9F9F5'><font face='verdana' size='2'>$server_ip</font></td>
          </tr>
        </tbody>
      </table>

      <table border='0' cellpadding='5' cellspacing='1' width='100%'>
      <tbody><tr>
           <td align='right' bgcolor='#294B72'><font color='lightgray' face='verdana, helvetica, arial' size='2'>Reporte generado el: ".$time."</font></td>
        </tr>
      </tbody></table><div class='yj6qo'></div><div class='adL'>

      </div></div>";

			$enviado = $mailer->enviar_correo($dirc, $asun, $body, $smtphost, $smtpport, $from);
      if($enviado == true){
        $resp['status'] = 'ok';
        $resp['msg'] = 'Reporte Enviado';
      }else{
        $resp['status'] = 'error';
        $resp['msg'] = 'Error de envio';
      }//end if

      echo json_encode($resp);
    }else{
      $resp['status'] = 'error';
      $resp['msg'] = 'Error de Posteo';
      echo json_encode($resp);
    }//end if
  ?>
