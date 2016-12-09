<?php

/*** Authentification on T411 ****/

$data['login']=$t411_login;
$data['password']=$t411_mdp;

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.t411.me/users/login/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/www/thal/ogmrip/tmp/cookies/t411_cookies.txt");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_exec($ch);

$name=basename($torrent, '.torrent');
$t411_os=684; // Platine, lecteur multimédia, PC, Streaming
$t411_type=22; // 2D
$t411_lang=541;
if(preg_match("#(dvdrip|brrip|bdrip)#iUs", $name)){
	if(preg_match("#(dvdrip)#iUs", $name)){
		$t411_quality=10;
	}else if(preg_match("#(bdrip)#iUs", $name)){
		$t411_quality=8;
	}else if(preg_match("#(brrip)#iUs", $name)){
		$t411_quality=9;
	}else{
		$t411_quality=10;
	}
}else if(preg_match("#bluray#iUs", $name)){
	if(preg_match("#1080p#iUS", $name)){
		$t411_quality=16; // BluRay 1080p
	}else if(preg_match("#720p#iUS", $name)){
		$t411_quality=15; // BluRay 720p
	}else{
		$t411_quality=17; // FULL BluRay
	}
}else{
	$t411_quality=10; // dvdrip par défaut
}
if(preg_match("#vostfr#iUS", $name)){
		$t411_lang=721;
	}else if(preg_match("#multi#iUS", $name)){
		$t411_lang=542;
	}else{
		$t411_lang=541; // fr
	}
$t411_format="";
if(isset($fps)){
	wr("FPS : $fps");
	if(round($fps, 1)==25.0){ // PAL
		$t411_format=21;
	}else{ // NTSC
		$t411_format=20;
	}
}else{
	wr("Warning, fps not defined ...");
}

$datas=array();

$datas['torrent']="@".$torrent;
$datas['nfo']="@".$nfo;
$datas['category']=(preg_match("#s[0-9]{2}e[0-9]{2}#iUs", $name)?"433":"631");
$datas['name']=str_replace('.', ' ', $name);
$datas['descr']=$t411_prez;
$datas['term[8][]']=$t411_format; // NTSC or PAL
$datas['term[7][]']=$t411_quality; // DVDRiP, BRRiP or BDRiP
$datas['term[34][]']=$t411_os; // support (multimedia, computer, streaming platform)
$datas['term[9][]']=$t411_type; // 2d or 3D
$datas['term[2][]']=""; // kind
$i=0;
foreach($array_kind as $val){
	$k=T411_get_kind($val);
	if($k!=0){
		$i++;
		wr($val." => $k");
		$datas['term[2][]']=$k;
	}else{
		wr("La valeur $val de kind n'est pas supportée par T411_get_kind ...");
	}
}
if($i==0){
	// aucune catégorie trouvée pour t411 ...
	// sélection par dfaut de la case drame (le plus fréquent et passe partout).
	$datas['term[2][]']=39;
}
$datas['term[17][]']=$t411_lang; // langue

print_r($datas);
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.t411.me/torrents/upload-step-2/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/www/thal/ogmrip/tmp/cookies/t411_cookies.txt");
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_VERBOSE, false);

$rs=curl_exec($ch);
$id_torrent=preg_replace("#^.+/torrents/download/\?id=([0-9]+)\".+$#iUs", "$1", $rs);
if(strlen($id_torrent)>1000){
	$id_torrent=null;
}
wr("************    TORRENT UPLOADED ON T411   ***********");
wr("T411 torrent_id = $id_torrent ... adding this to rtorrent.");

$fp=fopen('/home/www/thal/ogmrip/tmp/torrents/'.$id_torrent.'.torrent', 'w');
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.t411.me/torrents/download/?id='.$id_torrent);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/www/thal/ogmrip/tmp/cookies/t411_cookies.txt");
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt ($ch, CURLOPT_FILE, $fp);
curl_exec($ch);
add_to_remote_seedbox("http://62.75.252.71:51443/rut/t411/", '/home/www/thal/ogmrip/tmp/torrents/'.$id_torrent.'.torrent', "/home/thal/disk2/dl/", 'dominique', '*********');
$nb_up++;
