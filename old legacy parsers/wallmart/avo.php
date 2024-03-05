<?php

include_once('simple_html_dom.php');
$url = 'http://www.consiglionazionaleforense.it/web/cnf/cerca-avvocato?p_auth=nqMcUhlo&p_p_id=cnfricercaavvocati_WAR_cnfricercaavvocatiportlet&p_p_lifecycle=1&p_p_state=normal&p_p_mode=view&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_pag=15&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_cognome=&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_tipo=1&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_ordine=20&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_iv=&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_ip=&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_ic=&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_c=AO&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_javax.portlet.action=searchAvvocati&#p_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet';

$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';

$dir = dirname(__FILE__);

$config['cookie_file'] = $dir . '/cookies/' . md5($_SERVER['REMOTE_ADDR']) . '.txt';
echo $config['cookie_file'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_REFERER, 'https://www.google.com/');
	
	curl_setopt($curl, CURLOPT_COOKIEFILE, $config['cookie_file']);
curl_setopt($curl, CURLOPT_COOKIEJAR, $config['cookie_file']);

	curl_setopt($curl, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_URL, 'http://www.consiglionazionaleforense.it/web/cnf/cerca-avvocato?p_auth=nqMcUhlo&p_p_id=cnfricercaavvocati_WAR_cnfricercaavvocatiportlet&p_p_lifecycle=1&p_p_state=normal&p_p_mode=view&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_pag=15&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_cognome=&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_tipo=1&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_ordine=20&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_iv=&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_ip=&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_ic=&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_c=AO&_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet_javax.portlet.action=searchAvvocati&#p_cnfricercaavvocati_WAR_cnfricercaavvocatiportlet');
	
	
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo $result;

//$html = file_get_html($result);

//echo $html;

?>