<?php

if(!isset($_GET['q'])){
	echo '<form action="check.php" method="get" ><input type="text" name="q" placeholder="ex: Tears of Sun" /><input type="submit" value=" Check --> " /></form>';
}else{
	include('dom.php');
	include('thales0796.mdp.php');
	if(!isset($_GET['auto'])){
		$_GET['auto']='';
	}
	// FTDB
	$i=0;
	$datas=array();
	$datas['username']=$ftdb_login;
	$datas['password']=$ftdb_mdp;
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://www.frenchtorrentdb.com/?section=LOGIN');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "ftdb_cookies.txt");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
	curl_setopt($ch, CURLOPT_VERBOSE, false);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)" );
	curl_exec($ch);	

	curl_setopt($ch, CURLOPT_COOKIEFILE, "ftdb_cookies.txt");

	curl_setopt($ch, CURLOPT_URL, 'http://www.frenchtorrentdb.com/?name='.str_replace(' ', '+', $_GET['q'].'&search=Rechercher&exact=1&section=TORRENTS&group='));
	$rs=curl_exec($ch);
	//echo $rs;
	$html=new simple_html_dom();
	$html->load($rs);
	$rs=$html->find('.DataGrid .torrents_name a');
	foreach($rs as $trs){
		$tmp='<span style="color:blue;" >[FTDB] </span>'.$trs->title;
		if(preg_match("#[(dvdrip)|(brrip)][\.| ](x264|ac3)[\.| ](x264|ac3)#iUs", "BRRiP x264 ac3", $trs->title)){
			$i++;
			$tmp='<span style="font-weight:bold;" >'.$tmp.'</span>';
		}
		if($_GET['auto']!='on'){
			echo $tmp.'<br />';
		}
	}
	
	// GKS
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://gks.gs/sphinx/?q=".str_replace(' ', '+', $_GET['q']));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_VERBOSE, false);
	curl_setopt($ch, CURLOPT_COOKIE, "pw=$GKS_encrypted_mdp; uid=$GKS_uid");
	$rs=curl_exec($ch);
	$html->load($rs);
	$rs=$html->find('#torrent_list td.name_torrent_1 a');
	$rs2=$html->find('#torrent_list td.name_torrent_0 a');
	foreach($rs as $trs){
	$tmp='<span style="color:red;" >[GKS] </span>'.$trs->title;
		if(preg_match("#[(dvdrip)|(brrip)][\.| ](x264|ac3)[\.| ](x264|ac3)#iUs", "BRRiP x264 ac3", $trs->title)){
			$i++;
			$tmp='<span style="font-weight:bold;" >'.$tmp.'</span>';
		}
		if($_GET['auto']!='on'){
			echo $tmp.'<br />';
		}
	}
	foreach($rs2 as $trs){
		$tmp='<span style="color:red;" >[GKS] </span>'.$trs->title;

		if(preg_match("#[(dvdrip)|(brrip)][\.| ](x264|ac3)[\.| ](x264|ac3)#iUs", "BRRiP x264 ac3", $trs->title)){
			$i++;
			$tmp='<span style="font-weight:bold;" >'.$tmp.'</span>';
		}
		
		if($_GET['auto']!='on'){
			echo $tmp.'<br />';
		}
	}
	if($i>0&&$_GET['auto']=='on'){
		echo '1';
	}else if($_GET['auto']=='on'){
		echo '0';
	}
}
