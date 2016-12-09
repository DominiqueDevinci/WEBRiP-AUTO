<?php

$name=basename($torrent, '.torrent');
if(preg_match("#s[0-9]{2}e[0-9]{2}#iUs", $name)){
	$bt_cat=17;
}elseif(preg_match("#dvdrip#iUs", $name)){
	$bt_cat=20;
}else if(preg_match("#(brrip|bdrip)#iUs", $name)){
	$bt_cat=59;
}else if(preg_match("#bluray#iUs", $name)){
	$bt_cat=22;
}else{
	$bt_cat=20; // by default dvdrip
}

if(preg_match("#french|truefrench|multi#iUS", $name)){
	$bt_lang=1;
}else{
	$bt_lang=2;
}


$datas=array();

$datas['takeupload']="yes";
$datas['torrent']="@".$torrent;
$datas['nfo']="@".$nfo;
$datas['name']=str_replace('.', ' ', $name);
$datas['descr']=$bluetigers_prez;
$datas['type']=$bt_cat;
$datas['url']='http://www.imdb.com/title/tt'.$imdb;

$ext=explode('.', $casimage_poster);
$ext=$ext[count($ext)-1];
$tmp=md5(rand(0, 999).time());

// DL image imdb
$fp=fopen("/home/www/thal/ogmrip/tmp/$tmp.$ext", 'w');
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $poster);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt ($ch, CURLOPT_FILE, $fp);
curl_exec($ch);
$datas['image0']="@/home/www/thal/ogmrip/tmp/$tmp.$ext;type=image/jpeg";
wr("poster = @/home/www/thal/ogmrip/tmp/$tmp.$ext");
$datas['allocine']='';
$datas['lang']=$bt_lang;

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.bluetigers.ca/torrents-upload.php');
//curl_setopt($ch, CURLOPT_URL, 'http://91.121.223.58/essai.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)" );
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_COOKIE, "pass=".$BlueTigers_pass."; uid=".$BlueTigers_uid."");
$rs=curl_exec($ch);
$fp=fopen('/home/www/thal/ogmrip/tmp/BlueTigers.log.html', 'w+');
fwrite($fp, $rs);
fclose($fp);
wr("************    TORRENT UPLOADED ON BlueTigers   ***********");
curl_close($ch);
$tab=preg_split("#download\.php\?id=([0-9]+)\s*>#iUs", $rs, -1, PREG_SPLIT_DELIM_CAPTURE);
$id_torrent=$tab[1];

$t='/home/www/thal/ogmrip/tmp/torrents/BlueTigers_'.md5(rand(0,90000)).'_'.time().'.torrent';
$tmp='https://www.bluetigers.ca/download.php?id='.$id_torrent;
wr("torrent = $tmp");
$fp=fopen($t, 'w');
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $tmp);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_COOKIE, "pass=".$BlueTigers_pass."; uid=".$BlueTigers_uid."");
curl_setopt ($ch, CURLOPT_FILE, $fp);
curl_exec($ch);

add_to_remote_seedbox("http://62.75.252.71:51443/rut/bt/", $t, "/home/thal/disk2/dl/", 'dominique', '**********');
wr("[BlueTigers] Torrent adding to BlueTigers dedicated seedbox ...");
$nb_up++;

