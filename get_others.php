<?php

foreach($html1->find('#tn15content .info') as $item){
	$h5=$item->find('h5');
	if(count($h5)>0){
		$h5=$h5[0]->plaintext;
		if(intval(strpos($h5, "riters"))>0){
			foreach($item->find('a') as $val){
				$val=trim($val->plaintext);
				if($val!='(more)'){
					$scenarists[]=$val;
				}
			}
			if(!isset($verbose)||$verbose==true){
				wr('Number scenarists: '.count($scenarists));
			}
		}
		switch($h5){
			case 'Alias:':
				$title_vo=split('"', $item->find('.info-content', 0)->plaintext);
				if(count($title_vo)>=2){
					$title_vo=$title_vo[1];
				}else{
					$title_vo=$title_fr;
				}
				if(!isset($verbose)||$verbose==true){
					wr('Original title: '.$title_vo);
				}
				break;		
			case 'Country:':
				$origin=explode('|', $item->find('.info-content', 0)->plaintext);
				$origin=$origin[0];
				if(!isset($verbose)||$verbose==true){
					wr('Land\'s origin: '.$origin);
				}
				break;
			case 'Runtime:':
				$duration=explode('|', $item->find('.info-content', 0)->plaintext);
				$duration=$duration[0];			
				if(!isset($verbose)||$verbose==true){
					wr('Duration: '.$duration);
				}
				break;			
			case 'Genre:':
				$kind=$item->find('.info-content', 0)->plaintext;
				$kind=str_replace('  ', ' ', str_replace('See more&nbsp;&raquo;', '', $kind));
				if(!isset($verbose)||$verbose==true){
					wr('Kind: '.$kind);
					$tmp=trim(str_replace(' ', '', str_replace(' ', '', $kind)));
					$array_kind=explode('|', $tmp);					
				}
				break;
			case 'Release Date:':
				$date=trim(str_replace("&nbsp;", "", str_replace("&raquo;", "", str_replace(" See more", "", $item->find('.info-content', 0)->plaintext))));
				if(!isset($verbose)||$verbose==true){
					wr('Date: '.$date);
				}
				break;
			default:
			//echo "--------- $h5\n";
			break;
		}
	}
}
if(!isset($title_vo)||trim($title_vo)==""){
	$title_vo=$title_fr;
}
/*
$u="http://imdbapi.org/?id=tt".$imdb."&type=json&plot=full";
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0 );
curl_setopt($ch, CURLOPT_VERBOSE, true );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)" );
curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/www/thal/ogmrip/tmp/cookies/imdbapi_cookies.txt");
curl_setopt($ch, CURLOPT_URL, $u);
$imdbapi=curl_exec($ch);
$imdbapi=json_decode($imdbapi, true);
print_r($imdbapi);
$duration=$imdbapi['runtime'];
$array_kind=$imdbapi['genres'];
$kind=implode(' | ', $array_kind);
$origin=$imdbapi['country'];*/

