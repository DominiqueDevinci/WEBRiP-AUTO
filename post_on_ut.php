<?php
$name=basename($torrent, '.torrent');
$tmp=file_get_contents($torrent);
$tmp=str_replace('announce30:http://tk.gks.gs:6969/announce', 'announce79:http://www.unlimited-tracker.net:2710/6a83f1328045733a73fb19ffbcf5baf9/announce', $tmp);
$torrent_ut='/home/www/thal/bot_public/'.$name.'_UT.torrent';
$fp=fopen($torrent_ut, 'w+');
fwrite($fp, $tmp);
fclose($fp);

$name=basename($torrent, '.torrent');

if(preg_match("#s[0-9]{2}e[0-9]{2}#iUs", $name)){
	$ut_cat=25;// serie tv
}else if(preg_match("#dvdrip#iUs", $name)){
	$ut_cat=20;
}else if(preg_match("#(brrip|bdrip)#iUs", $name)){
	$ut_cat=20;
}else if(preg_match("#bluray#iUs", $name)){
	if(preg_match("#1080p#iUS", $name)){
		$ut_cat=23; // BluRay 1080p
	}else if(preg_match("#720p#iUS", $name)){
		$ut_cat=22; // BluRay 720p
	}else{
		$ut_cat=21; // FULL BluRay
	}
}else{
	$ut_cat=20; // by default dvdrip
}

if(preg_match("#french|truefrench|multi#iUS", $name)){
	$ut_lang=1;
}else{
	$ut_lang=2;
}

$datas=array();

$datas['torrent']="@".$torrent_ut;
$datas['nfo']="@".$nfo;
$datas['name']=str_replace('.', ' ', $name);
$datas['descr']=$ut_prez;
$datas['cat']=$ut_cat;
$datas['lang']=$ut_lang;
$datas['keyword']=str_replace(' | ', ',', $kind);

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.unlimited-tracker.net/private/upload.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_COOKIE, "pass=$UT_pass; uid=$UT_uid");
$rs=curl_exec($ch);
$fp=fopen('/home/www/thal/ogmrip/tmp/ut.log.html', 'w+');
fwrite($fp, $rs);
fclose($fp);
wr("************    TORRENT UPLOADED ON UT   ***********");
curl_close($ch);
$tab=preg_split("#href=\"http://www\.unlimited-tracker\.net/private/download\.php\?id=([0-9]+)\">#iUs", $rs, -1, PREG_SPLIT_DELIM_CAPTURE);
$id_torrent=$tab[1];

$t='/home/www/thal/ogmrip/tmp/torrents/UT_'.md5(rand(0,90000)).'_'.time().'.torrent';
$tmp='http://www.unlimited-tracker.net/private/download.php?id='.$id_torrent;
wr("torrent = $tmp");
$fp=fopen($t, 'w');
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $tmp);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_COOKIE, "pass=".$UT_pass."; uid=".$UT_uid."");
curl_setopt ($ch, CURLOPT_FILE, $fp);
curl_exec($ch);

add_to_remote_seedbox("http://62.75.252.71:51443/rut/ut/", $t, "/home/thal/disk2/dl/", 'dominique', '**********');
wr("[UT] Torrent adding to UT dedicated seedbox ...");
$nb_up++;
