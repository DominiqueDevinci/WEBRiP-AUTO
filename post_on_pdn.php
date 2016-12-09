<?php

$data['username']=$pdn_login;
$data['password']=$pdn_mdp;

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://le-paradis-du-net.com/takelogin.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/www/thal/ogmrip/tmp/cookies/pdn_cookies.txt");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_exec($ch);
$rs=curl_exec($ch);
$fp=fopen('/home/www/thal/ogmrip/tmp/pdn_connect.log.html', 'w+');
fwrite($fp, $rs);
fclose($fp);

$name=basename($torrent, '.torrent');
if(preg_match("#s[0-9]{2}e[0-9]{2}#iUs", $name)){
	$pdn_cat=8;
}elseif(preg_match("#dvdrip#iUs", $name)){
	$pdn_cat=16;
}else if(preg_match("#brrip#iUs", $name)){
	$pdn_cat=73;
}else if(preg_match("#bdrip#iUs", $name)){
	$pdn_cat=70;
}else if(preg_match("#bluray#iUs", $name)){
	$pdn_cat=69;
}else{
	$pdn_cat=16; // by default dvdrip
}

$datas=array();

$datas['torrentfile']="@".$torrent;
$datas['nfofile']="@".$nfo;
$datas['subject']=str_replace('.', ' ', $name);
$datas['message']=$pdn_prez;
$datas['category']=$pdn_cat;
$datas['t_link']='http://www.imdb.com/title/tt'.$imdb;

$datas['nothingtopost']=2;
// DL image imdb
$ext=explode('.', $casimage_poster);
$ext=$ext[count($ext)-1];
$tmp=md5(rand(0, 999).time());
$fp=fopen("/home/www/thal/ogmrip/tmp/$tmp.$ext", 'w');
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $poster);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt ($ch, CURLOPT_FILE, $fp);
curl_exec($ch);
$datas['t_image_file']="@/home/www/thal/ogmrip/tmp/$tmp.$ext;type=image/jpeg";

$datas['video[codec]']=$vcodec;
$datas['video[bitrate]']=str_replace(' Kbps', '', $vbt);
$datas['video[resulation]']=str_replace(' pixels', '', $reso);
$datas['video[length]']=str_replace(' min', '', $duration);
$datas['video[quality]']='';


$datas['audio[codec]']=$acodec;
$datas['audio[bitrate]']=str_replace(' Kbps', '', $abt);
$datas['audio[frequency]']=$afreq;
$datas['audio[language]']=$a_langs;
print_r($datas);
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://le-paradis-du-net.com/upload.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)" );
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/www/thal/ogmrip/tmp/cookies/pdn_cookies.txt");
$rs=curl_exec($ch);
$fp=fopen('/home/www/thal/ogmrip/tmp/pdn.log.html', 'w+');
fwrite($fp, $rs);
fclose($fp);
wr("************    TORRENT UPLOADED ON Paradis-du-net   ***********");
curl_close($ch);

$tab=preg_split("#content=\"2;URL=https://le\-paradis\-du\-net\.com/details\.php\?id=([0-9]+)\"#iUs", $rs, -1, PREG_SPLIT_DELIM_CAPTURE);
$id_torrent=$tab[1];

$t='/home/www/thal/ogmrip/tmp/torrents/PDN_'.md5(rand(0,90000)).'_'.time().'.torrent';
$tmp='https://le-paradis-du-net.com/download.php?id='.$id_torrent;
wr("torrent = $tmp");
$fp=fopen($t, 'w');
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $tmp);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/www/thal/ogmrip/tmp/cookies/pdn_cookies.txt");
curl_setopt ($ch, CURLOPT_FILE, $fp);
curl_exec($ch);

add_to_remote_seedbox("http://62.75.252.71:51443/rut/pdn/", $t, "/home/thal/disk2/dl/", 'dominique', '***********');
wr("[PDN] Torrent adding to Paradis-du-Net dedicated seedbox ...");
$nb_up++;

