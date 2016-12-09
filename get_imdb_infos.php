<?php
include_once('/vars.php');
include_once($dir_this."functions.php");
if(isset($_GET['id'])){
	$tmp=file_get_contents('http://www.imdb.fr/title/tt'.$_GET['id'].'/combined');
	$infos=array();
	if($tmp!=false){
		$tmp=mb_convert_encoding($tmp, "UTF-8", "iso-8859-1");
		$html1=new simple_html_dom();
		$html1->load($tmp);
		$f=fopen('text.html', 'w+');
		fwrite($f, $tmp);
		fclose($f);
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
		$verbose=false;
		#                           Général                      #
		//french title
		$title_fr=str_replace('\'', ' ', str_replace('&#x27;', ' ', html_entity_decode(urldecode(trim(strip_tags(preg_replace("#<span(.+)>(.+)</span>#iUs", "", $html1->find('#tn15title', 0)->innertext)))))));
		$tabs=preg_split("#^(.+)\.([0-9]{4})\.(.+)$#iUs", urldecode($_GET['title']), -1, PREG_SPLIT_DELIM_CAPTURE);
		$year=(isset($tabs[2])?$tabs[2]:0);
		$allocine=get_id_allocine($title_fr.".".$year.".XXX", false); // search with fr title
		$infos['allocine']=$allocine;
		
		$infos['title_fr']=$title_fr;	
		include($dir_this.'get_poster.php');
		$infos['poster']=$poster;
		
		// looking for a summary ...
		//include('/home/www/thal/ogmrip/get_summary.php');	
		
		// note
		//include('/home/www/thal/ogmrip/get_note.php');	
		
		// director
		//$director=$html1->find('#director-info a', 0)->plaintext;
		
		// actors
		//include('/home/www/thal/ogmrip/get_actors.php');
		
		// others informations
		include($dir_this.'get_others.php');
		
		$infos['date']=$date;
		$infos['title_vo']=$title_vo;
		$infos['duration']=$duration;
		
		echo json_encode($infos);
		return;
	}else{
		wr("Bad response from imdb ...");
	}
}else{
	echo "A id imdb is required ...";
}
