<?php

//$l = ldap_connect('10.129.146.54');//AD GVT
//$l = ldap_connect('10.41.9.218');//AD GVT
$l = ldap_connect('10.200.1.165');//AD GVT
//if($l){echo "Conexão OK" . "<br>" . $l . "<br>";}else{echo "Conexão NOK" . "<br>";exit;}//Testando conexão
ldap_set_option($l, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($l, LDAP_OPT_REFERRALS, false);
$bind = ldap_bind($l, 'G0033195@gvt.net.br', 'newM@trix35');//Bind GVT
//if($bind){echo "Bind OK" . "<br>";}else{echo "Bind NOK" . "<br>";exit;}//Testando Bind
$base = "DC=gvt,DC=net,DC=br";
$criteria = '(&(objectClass=user)(samaccountname=G0033195))';
$attributes = array("givenname","sn","title","department","userprincipalname","samaccountname","mail","employeeid","company","memberof","displayName");
$search = ldap_search($l, $base, $criteria, $attributes);
$entries = ldap_get_entries($l, $search);
for ($i=0; $i<$entries["count"]; $i++){
	echo $entries[$i]["givenname"][0] . "<br>";			//Nome
	echo $entries[$i]["sn"][0] . "<br>";			//Sobrenome
	echo $entries[$i]["title"][0] . "<br>";	            //Cargo
	echo $entries[$i]["department"][0] . "<br>";	    	//Setor
	echo $entries[$i]["userprincipalname"][0] . "<br>";//E-mail GVT
	echo $entries[$i]["samaccountname"][0] . "<br>";	//Matricula GVT
	echo $entries[$i]["mail"][0] . "<br>";	            //E-mail Telefonica
	echo $entries[$i]["employeeid"][0] . "<br>";	    //Matricula Telefonica
	echo $entries[$i]["company"][0] . "<br>" . "<br>";	//Empresa
	//echo $entries[$i]["memberof"][0] . "<br>";			//Membro de
	//echo $entries[$i]["displayname"][0] . "<br>"; 	//Nome Completo
}
ldap_close($l);


?>
