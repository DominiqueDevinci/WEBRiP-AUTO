<?php
echo $gks_prez;
$name=basename($torrent, '.torrent');
if(preg_match("#s[0-9]{2}e[0-9]{2}#iUs", $name)){
	$gks_cat=12;
}elseif(preg_match("#(dvdrip|brrip)#iUs", $name)){
	if(preg_match("#vostfr#iUS", $name)){
		$gks_cat=6; /// dvdrip vostfr
	}else{
		$gks_cat=5; // dvdrip french
	}
}else if(preg_match("#bluray#iUs", $name)){
	if(preg_match("#1080p#iUS", $name)){
		$gks_cat=16; // BluRay 1080p
	}else if(preg_match("#720p#iUS", $name)){
		$gks_cat=15; // BluRay 720p
	}else{
		$gks_cat=17; // FULL BluRay
	}
}else{
	$gks_cat=18; // divers
}
$gks_cat=21;
$datas=array();

$datas['MAX_FILE_SIZE']="3145728";
$datas['file']="@".$torrent;
$datas['nfo']="@".$nfo;
$datas['name']=str_replace('.', ' ', $name);
$datas['descr']=$gks_prez;
$datas['type']=$gks_cat;
$datas['summary']=0;
$datas['imdbid']=$imdb;

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://gks.gs/up');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_COOKIE, "pw=$GKS_encrypted_mdp; uid=$GKS_uid");
$rs=curl_exec($ch);
echo $rs;
$fp=fopen($dir_logs.'gks.log.html', 'w+');
fwrite($fp, $rs);
fclose($fp);
wr("************    TORRENT UPLOADED ON GKS   ***********");
curl_close($ch);
$tmp=new simple_html_dom();
$tmp->load($rs);

$t=$dir_tmp.'GKS_'.md5(rand(0,90000)).'_'.time().'.torrent';
$tmp=$tmp->find('#upload_good a', 0)->href;
$tmp='https://gks.gs'.$tmp;
wr("Torrent href = $tmp");
$fp=fopen($t, 'w');
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $tmp);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_COOKIE, "pw=$GKS_encrypted_mdp; uid=$GKS_uid");
curl_setopt ($ch, CURLOPT_FILE, $fp);
$rs=curl_exec($ch);
$f=fopen('gks.log.html', 'w+');
fwrite($f, $rs);
fclose($f);

add_to_remote_seedbox("http://85.25.255.228/rut/team/", $t, "/home/team/torrents/", 'dominique', '************');
wr("[GKS] Torrent adding to gks dedicated seedbox ...");
$nb_up++;
