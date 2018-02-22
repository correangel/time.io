<?php

	//######################################################################
	// Funciones para uso de ldap
	//######################################################################
	class _ldap{
		//##################################################################
		// Funcion que conecta a LDAP
		//##################################################################
		public function connect($host,$port){
			try {
				$ldap = ldap_connect($host,$port);
				return $ldap;
			} catch (Exception $e) {
				echo print_r( $e);
				return null;
			}//end try
		}//end Funcition

		//##################################################################
		// Función Que retorna verdadero si el usuario y su pasword
		// existen en el directorio activo configurado.
		//##################################################################
		public function login($ldap, $user, $pass){
			//echo $ldap;
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
			if (ldap_bind($ldap, $user, $pass)) {
				return 1;
			}else{
				return 0;
			}//end if
		}//end Funcition

		//##################################################################
		// Función Que retorna verdadero si el usuario pertenece al Grupo.
		//##################################################################
		public function check_grupo($ad, $grupo,$bdn, $ous ,$user){
			try {
				$ous = "CN=".$grupo.",".$ous;
				//$bdn;
				$results = ldap_search($ad,$bdn,"(sAMAccountName=$user)",array("memberof"));
				$entries = ldap_get_entries($ad, $results);
				$output = $entries[0]['memberof'];
				if( !$output) return 0;
				foreach ($output as $value){
					if($value === $ous){
						ldap_unbind($ad);
						return 1;
					}//end if
				}//end foreach
				return 0;
			} catch (Exception $e) {
				return 0;
			}//end try
		}//end if

		//##################################################################
		// Función para agregar members a grupos de AD
		//##################################################################
		public function add_login($ad, $grupo, $user,$bdn ,$ous, $bind_user, $bind_pass){
			try {
				$ous = "CN=".$grupo.",".$ous;
				//echo $ous;
				if (self::login($ad, $bind_user, $bind_pass)) {
					ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
					ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
					$results = ldap_search($ad,$bdn,"(sAMAccountName=$user)", array("sn","cn"), 0, 1);
					$entry = ldap_get_entries($ad, $results);
					$first = ldap_first_entry($ad, $results);
					$dn = ldap_get_dn($ad, $first);

					$data = $entry[0]['cn'][0];
					//$dn = str_replace($data, $user, $dn);
					//echo $dn;
					$user_array['member'] = $dn;
					//echo $ous;
					if(ldap_mod_add($ad, $ous, $user_array)){
						return 1;
					}else{
						return 0;
					}//end if*/
				}else{
					return 0;
				}//end if
			} catch (Exception $e) {
				return 0;
			}//end try
		}//end function


	public function _get_members($ldap, $ldap_dn, $group=FALSE,$inclusive=FALSE) {

		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
		// Search AD
		$ous = "CN=".$group.",".$ldap_dn;
		$ldapSearch = ldap_search($ldap, $ous, "(member=*)",array('member'));
		$entries = ldap_get_entries($ldap, $ldapSearch);
		array_shift($entries);
		$members['info'] = array();
		$members['string'] = '';
		$first = true;
		foreach($entries as $available_user) {
				$c = intval($available_user['member']['count']);
				for ($i=0; $i < $c ; $i++) {
					$dn = $available_user['member'][$i];
					$filter="(|(sn=*)(givenname=*))";
					$justthese = array("sAMAccountName", 'givenname', 'sn');
					$sr=ldap_search($ldap, $dn, $filter, $justthese);
					$sam = ldap_get_entries($ldap, $sr);
					//array_shift($sam);
					//print_r($sam);
					//$members = $sam;
					$info['sama'] = $sam[0]['samaccountname'][0];
					//$info['name'] = $sam[0]['givenname'][0].' '.$sam[0]['sn'][0];
					$info['dn'] = $sam[0]['dn'];

					array_push($members['info'] ,$info);

					if($first){
						$members['string'] = $info['sama'];
						$first = false;
					}else{
						$members['string'].= ','.$info['sama'];
					}//end if
					//array_push($members,$sam);
				}// end if
		}//end foreach

		return $members;
	}
}//end Class
?>
