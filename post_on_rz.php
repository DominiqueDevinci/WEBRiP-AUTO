<?php
/*
 * 
 * Authentification on RZ ...
 * 
 * */
$datas['form_sent']=1;
$datas['req_username']=$rz_login;
$datas['req_password']=$rz_mdp;
$datas['redirect_url']='http://www.real-zone.ws/index.php';
$datas['save_pass']=1;

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.real-zone.ws/login.php?action=in");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/www/thal/ogmrip/tmp/cookies/rz_cookies.txt");
curl_exec($ch);

/****
 * 
 *		Post on RZ ...
 * 
 **/
 
$name=basename($torrent, '.torrent');
$rs=parse_title($name);
print_r($rs);
if($rs!=false){
	$rz_title="[MULTI] ".$rs['title']." ".$rs['year']." [".str_replace("MULTI", "MULTILANGUE", $rs['lang'])."] [".$rs['quality']."] ".$rs['codec']."-CiRAR";
}else{
	wr("Cannot parse title because it's not a valid title ...");
	wr("Exit because a valid title is required for RZ ...");
	exit(0);
}

if(preg_match("#s[0-9]{2}e[0-9]{2}#iUs", $name)){
	$rz_cat=19;
}elseif(preg_match("#(dvdrip|brrip|bdrip)#iUs", $name)){
	if(preg_match("#vostfr#iUS", $name)){
		$rz_cat=11;
	}else{
		$rz_cat=12;
	}
}else{
	$rz_cat=12;
}

$datas=array();
$rz_sent_url="http://www.real-zone.ws/post.php?action=post&fid=".$rz_cat;
$datas['req_release']=$name;
$datas['req_subject']=$rz_title;
$datas['req_message']=$rz_prez;
$datas['form_sent']=1;
$datas['hide_smilies']=1;
$datas['subscribe']=1;
$datas['anon_up']='';
$datas['submit']=true;

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $rz_sent_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/www/thal/ogmrip/tmp/cookies/rz_cookies.txt");
$rs=curl_exec($ch);
$fp=fopen('/home/www/thal/ogmrip/tmp/rz.log.html', 'w+');
fwrite($fp, $rs);
fclose($fp);
wr("************    TORRENT UPLOADED ON RZ   ***********");
curl_close($ch);
