<?php
if(isset($_GET['id_allocine'])){
	$allocine=$_GET['id_allocine'];
}
if(isset($_GET['id_imdb'])){
	$imdb=$_GET['id_imdb'];
}
if(isset($_GET['quiet'])){
	require_once("dom.php");
	function wr($s){
		echo "<b style='color:red;' >$s</b><br />";
	}
}
if(isset($allocine)){
	if(!isset($_GET['quiet'])){
		wr('Searching on Allocine ...');
	}
	if(intval($allocine)>0){
		$tmp=file_get_contents('http://www.allocine.fr/film/fichefilm_gen_cfilm='.$allocine.'.html');
	}else{
		wr("Warning, Allocine id not found ...");
		$tmp=true;
	}
	if($tmp!=false){
		$html2=new simple_html_dom();
		$html2->load($tmp);
		$summary=substr($html2->find('#col_main p[itemprop="description"]', 0)->plaintext, 1, -1);
		if(trim($summary)==''){
			if(!isset($_GET['quiet'])){
				wr('French summary not found on Allocine ... #0005');
			}
			$summary=null;
		}else{
			if(!isset($_GET['quiet'])){
				wr('French summary found on Allocine !');
			}
		}
	}else{
		wr('Connection error occured connecting Allocine. #0004');
		$summary=null;
	}
}else{
	if(!isset($_GET['quiet'])){
		wr('Allocine id not informed ... #0003');
	}
}
if($summary==null){
	if(!isset($_GET['quiet'])){
		wr('Searching summary on IMDB ...');
	}
			$tmp=file_get_contents('http://www.imdb.com/title/tt'.$imdb.'/plotsummary');
			if($tmp!=null){
				$html2=new simple_html_dom();
				$html2->load($tmp);
				$summary=trim($html2->find('#tn15adrhs .plotpar', 0)->plaintext);
				if($summary==""){
					$summary=null;
				}
			}else{
				if(!isset($_GET['quiet'])){
					echo 'Connection error occured connecting IMDB. #0008';
				}
			}
}
if($summary==null){
	if(!isset($_GET['quiet'])){
		wr('Nothing summary found ... #0007');
	}
	$summary='Nothing summary found on IMDB or Allocine ...';
}
if(isset($_GET['quiet'])){
	echo $summary;
}
