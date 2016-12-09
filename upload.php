<?php
include_once('vars.php');
print_r($argv);
require_once($dir_this.'functions.php');
require_once($dir_this.'dom.php');
$args=array();
if(isset($argv[1])){
	$tmp=explode('&', $argv[1]);
	foreach($tmp as $v){
		$item=split('=', $v);
		$args[$item[0]]=$item[1];
	}
}
if(isset($args['profil'])){
	wr("Using file ".$args['profil'].".mdp.php for upload ...");
	require_once($dir_this.''.$args['profil'].'.mdp.php');
}
if(isset($args['trackers'])){
	$trackers=explode(',', trim($args['trackers']));
	if(count($trackers)<=0){
		wr("Selectionned tracker(s) required ...");
		exit(0);
	}
}else{
	wr("Selectionned tracker(s) required ...");
	exit(0);
}
if(isset($args['imdb'])){
	$imdb=strval($args['imdb']);
}else{
	wr('A valid IMDB id is required !');
	exit(0);
}
if(isset($args['nfo'])){
	$nfo=strval($args['nfo']);
}else{
	wr('NFO file required !');
	exit(0);
}
if(isset($args['torrent'])){
	$torrent=strval($args['torrent']);
}else{
	wr('Torrent file required !');
	exit(0);
}
if(isset($args['file'])){
	$file=strval($args['file']);
}else{
	wr('Args file is required !');
	exit(0);
}
if(isset($args['thumbs'])){
	$thumbs=strval($args['thumbs']);
}
if(isset($args['acodec'])){
	$acodec=strval($args['acodec']);
}
if(isset($args['vcodec'])){
	$vcodec=strval($args['vcodec']);
}
if(isset($args['mediainfo'])){
	$mediainfo=$args['mediainfo'];
}
if(isset($args['size'])){
	$size=strval($args['size']);
}
if(isset($args['bt'])){
	$bt=strval($args['bt']);
}
if(isset($args['poster'])){
	$poster=urldecode($args['poster']);
}
if(isset($args['rlz_src'])){
	$rlz_src=strval($args['rlz_src']);
}
if(isset($args['a_langs'])){
	$a_langs=strval($args['a_langs']);
}
if(isset($args['vbt'])){
	$vbt=strval($args['vbt']);
}
if(isset($args['abt'])){
	$abt=strval($args['abt']);
}
if(isset($args['reso'])){
	$reso=strval($args['reso']);
}
if(isset($args['afreq'])){
	$afreq=strval($args['afreq']);
}
if(isset($args['s_langs'])){
	$s_langs=strval($args['s_langs']);
}else{
	$s_langs='Aucun';
}
if(isset($args['allocine'])&&intval($args['allocine'])>0){
	$allocine=strval($args['allocine']);
}
if(intval($imdb)<=0){
	$imdb=get_id_imdb(basename($torrent, '.torrent'));
	if($imdb==false){
		exit(0);
	}
}
if(isset($args['fps'])){
	$fps=strval($args['fps']);
}

if(parse_title(basename($torrent, '.torrent'))==false){
	wr("Warning, title invalid ...");
	exit(0);
}

$html1=new simple_html_dom();
wr("Authentificate account on imdb (for set langage) ...");
include($dir_this.'authentificate_imdb.php');
wr("Scanning IMDB ...");
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.imdb.com/title/tt'.$imdb.'/combined');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_COOKIEFILE, $dir_cookies."imdb_cookies2.txt");
curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent=Mozilla/5.0 (X11; Linux x86_64; rv:20.0) Gecko/20100101 Firefox/20.0" );
curl_setopt($ch, CURLOPT_VERBOSE, false);
$tmp=curl_exec($ch);
try{
	if($tmp!=false){
		//$tmp=mb_convert_encoding($tmp, 'UTF-8', 'iso-8859-1');
		$html1->load($tmp);
		unset($tmp);
		
		$title_fr=null;
		$title_vo=null;
		$kind=null;
		$scenarists=array();
		$date=null;
		$director=null;
		$actors=array();
		$note=null;
		$summary=null;
		$origin=null;
		$duration=null;
		$array_kind=array();
		
		#                           Général                      #
		
		//french title
		$title_fr=str_replace('\'', ' ', str_replace('&#x27;', ' ', html_entity_decode(urldecode(trim(strip_tags(preg_replace("#<span(.+)>(.+)</span>#iUs", "", $html1->find('#tn15title', 0)->innertext)))))));
		
		if((!isset($allocine)||intval($allocine)<=0||$allocine==null||trim($allocine)=="")&&$allocine!="no"){
			$tabs=preg_split("#^(.+)\.([0-9]{4})\.(.+)$#iUs", basename($torrent, '.torrent'), -1, PREG_SPLIT_DELIM_CAPTURE);
			$year=$tabs[2];
			$allocine=get_id_allocine($title_fr.".".$year.".XXX"); // search with fr title
			if($allocine==false){
				$allocine==null;
			}
		}
		
		wr('French title : '.$title_fr);
			
		// looking for a poster ...
		if(isset($poster)&&trim($poster)!="0"){
			wr("A customize poster is defined ... $poster");
		}else{
			wr("Nothing customize poster required, searching default poster ...");
			include($dir_this.'get_poster.php');	
		}
		// looking for a summary ...
		wr('Looking for a summary ...');
		include($dir_this.'get_summary.php');	
		// note
		include($dir_this.'get_note.php');	
		wr('Note: '.$note);
		
		// director
		$director=$html1->find('#director-info a', 0)->plaintext;
		wr('Producteur: '.$director);
		
		// actors
		include($dir_this.'get_actors.php');
		wr('Number actors: '.count($actors));	
		
		// others informations
		include($dir_this.'get_others.php');
		
		$nb_up=0;
		if(is_readable($dir_this.'includes.php')){
			include($dir_this.'includes.php'); // pour pouvoir modifier des infos manuellement (summary etc.)
		}
		$filename=basename($torrent, '.torrent');
		/**
		 * 
		 *                  UPLOADING ON GKS
		 * 
		 * 		Default torrent announce is for GKS ... so not need edit torrent announce.
		 * 
		*/
		if(in_array('gks', $trackers)){
			wr("Uploading on GKS ...");
			// generate_prez
			include($dir_this.'get_gks_prez.php');
			// post on GKS
			include($dir_this.'post_on_gks.php');
		}
		/**
		 * 
		 *                  UPLOADING ON FTDB
		 * 
		 * 		Default torrent announce is for GKS ... but FTDB cannot require his announce.
		 *      So not need edit torrent announce.
		 * 
		*/
		if(in_array('ftdb', $trackers)){
			wr("Uploading on FTDB ...");
			// generate_prez
			include($dir_this.'get_ftdb_prez.php');
			// post on FTDB
			include($dir_this.'post_on_ftdb.php');
		}
		/**
		 * 
		 *                  UPLOADING ON T411
		 * 
		 * 		Default torrent announce is for GKS ... but T411 cannot require his announce.
		 *      So not need edit torrent announce.
		 * 
		*/
		if(in_array('t411', $trackers)){
			wr("Uploading on T411 ...");
			// generate_prez
			include($dir_this.'get_t411_prez.php');
			// post on T411
			include($dir_this.'post_on_t411.php');
		}		
		/**
		 * 
		 *                  UPLOADING ON PDN
		 * 
		 * 		Default torrent announce is for GKS ... but T411 cannot require his announce.
		 *      So not need edit torrent announce.
		 * 
		*/
		if(in_array('pdn', $trackers)){
			wr("Uploading on PDN ...");
			// generate_prez
			include($dir_this.'get_pdn_prez.php');
			// post on T411
			include($dir_this.'post_on_pdn.php');
		}		
			
		if(in_array('bt', $trackers)){
			wr("Uploading on BlueTigers...");
			// generate_prez
			include($dir_this.'get_bluetigers_prez.php');
			// post on Blue Tigers
			include($dir_this.'post_on_bluetigers.php');
		}
		if(in_array('ut', $trackers)){
			wr("Uploading on Unlimited-Tracker...");
			// generate_prez
			include($dir_this.'get_ut_prez.php');
			// post on Blue Tigers
			include($dir_this.'post_on_ut.php');
		}
		if(in_array('rz', $trackers)){
			wr("Uploading on RZ ...");
			// generate_prez
			include($dir_this.'get_rz_prez.php');
			// post on RZ
			include($dir_this.'post_on_rz.php');
		}	
	}else{
		echo 'Connection error occured connecting IMDB. #0002';
		exit(0);
	}
}catch(Exception $e){
	wr('Internal error #0001');
	exit(0);
}
