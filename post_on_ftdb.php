<?php
include_once('/vars.php');
wr("Uploading on FTDB ...");
$name=basename($torrent, '.torrent');
if(preg_match("#s[0-9]{2}e[0-9]{2}#iUs", $name)){
	$ftdb_cat=95;
}elseif(preg_match("#(dvdrip|brrip)#iUs", $name)){
	if(preg_match("#vostfr#iUS", $name)){
		$ftdb_cat=80;
	}else{
		$ftdb_cat=71;
	}
}else{
	$ftdb_cat=127;
}

$FTDB_apikey="API_000CC38544B5DD9022";
$datas=array();
$datas['api_key']=$FTDB_apikey;
$datas['torrent']="@".$torrent;
if(isset($mediainfo)){
	$datas['mediainfo']="@".$mediainfo;
}
if ( !file_exists( $ftdb_prez_file ) || !is_readable( $ftdb_prez_file ) ){
     wr("(FTDB] Fichier descr illisible #0014");
}else{
	wr("Fichier descr bien lisible ... $ftdb_prez_file");
	$datas['descr']="@" . $ftdb_prez_file;
}

$datas['nfo']="@".$nfo;
$datas['category']=$ftdb_cat;

print_r($datas);

$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0 );
curl_setopt($ch, CURLOPT_VERBOSE, true );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)" );
curl_setopt($ch, CURLOPT_URL, 'http://www.frenchtorrentdb.com/?section=SERVICES&module=mod_api_ftdbup');
curl_setopt($ch, CURLOPT_POST, true );
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas );
$rs=json_decode(curl_exec($ch), true);
print_r($rs);
if(isset($rs['ok'])&&$rs['ok']==200){
	wr("************    TORRENT UPLOADED ON FTDB   ***********");
	$t=$dir_tmp.'FTDB_'.md5(rand(0,90000)).'_'.time().'.torrent';
	FTDB_download_torrent($rs['torrent_id'], $t, $ftdb_login ,$ftdb_mdp);
	//add_to_remote_seedbox("http://62.75.252.71:51443/rut/ftdb/", $t, "/home/thal/disk2/dl/", 'dominique', '*********');
	wr("[FTDB] Torrent adding to ftdb dedicated seedbox ...");
	$nb_up++;
}else{
	wr("############    Fail for uploading torrent on FTDB   #########");
}

