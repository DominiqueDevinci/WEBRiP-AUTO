<?php
require_once('dom.php');
require_once('vars.php');

function wr($s){
	echo $s."\n";
}
function GKS_upload_img($img){
	if(preg_match("#^http://#iUs", $img)){
		wr("Uploading image on GKS with url ...");
		$url="https://s.gks.gs/img/extern.php?c=$img";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_exec($ch);
		$tmp=curl_getinfo($ch);
		return $tmp['url'];
		exit(0);
	}else{
		$data=array();
		$data['MAX_FILE_SIZE']='5242880';
		$data['photo'] = "@".$img;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://s.gks.gs/img/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		$rs = curl_exec($ch);
		$html=new simple_html_dom();
		$html->load($rs);
		if($html!=null){
			$tmp=$html->find('.lend', 0);
			if($tmp!=null){
				$tmp=$tmp->find('a', 0);
				if($tmp!=null){
					$tmp=$tmp->plaintext;
				}else{
					echo '#error #ef0003';
				}
			}else{
				echo '#error #ef0002';
			}
		}else{
			echo '#error #ef0001';
		}
		if($tmp!=null&&trim($tmp)!=''){
			return $tmp;
		}else{
			return null;
		}
	}
}
function move_to_casimage($img){
	global $dir_tmp;
	$ext=explode('.', $img);
	$ext=$ext[count($ext)-1];
	$tmp=md5(rand(0, 999).time());
	copy($img, $dir_tmp.$tmp.$ext);
	return get_public_thumbs($dir_tmp.$tmp.$ext);
}
function get_public_thumbs($img){
	$data=array();
	$data['image'] = "@".$img;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://www.casimages.com/upload_ano.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    $rs = curl_exec($ch);
    $html=new simple_html_dom();

    if(preg_match("#^.+document\.location\.href=\"codes_ano\.php\?img=[0-9]+\..{3,4}&nsa=nsa[0-9]+&.+$#iUs", $rs)){
		$tmp=preg_split("#^.+document\.location\.href=\"(codes_ano\.php\?img=[0-9]+\..{3,4}&nsa=nsa[0-9]+&module=).+$#iUs", $rs, -1, PREG_SPLIT_DELIM_CAPTURE);
		$tmp=$tmp[1];
	}
	$rs=file_get_contents('http://www.casimages.com/'.$tmp);
	$html->load($rs);
	$tmp=$html->find('#codano img', 0)->src;
	if($tmp!=null&&trim($tmp)!=''){
		return str_replace('mini_', '', $tmp);
	}else{
		return null;
	}
}
function get_id_imdb($title, $v=true){
	if(preg_match("#^(.+)\.[0-9]{4}\.(.+)$#iUs", $title)){
		$tabs=preg_split("#^(.+)\.([0-9]{4})\.(.+)$#iUs", $title, -1, PREG_SPLIT_DELIM_CAPTURE);
		$title=str_replace('.', ' ', $tabs[1]);
		$year=$tabs[2];
		if($v){
			wr('Title = '.$title);
			wr('Year = '.$year);
		}
		$url="http://www.imdb.com/find?q=".str_replace('.', '+', str_replace(' ', '+', $title))."+".$year."&s=all";
		if($v){
			wr("Send request to $url ...");
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$html=new simple_html_dom();
		$html->load(curl_exec($ch));
		$tmp=trim($html->find('.result_text a', 0)->href);
		if($tmp!=null&&strlen($tmp)>5){
			$id=preg_replace("#^(.+)([0-9]{7})(.+)?$#iUs", "$2", $tmp);
			if(intval($id)>0){
				if($v){
					wr("ID_IMDB = $id");
				}
				return $id;
			}else{
				if($v){
					wr("The id IMDB found is false ... #0012");
				}
				return false;
			}
		}else{
			if($v){
				wr("Error for find a imdb id ... #0009");
			}
			return false;
		}
	}else{
		wr('Titre mal renseigné pour faire une recherche sur imdb.');
		return false;
	}
}
function get_id_allocine($title, $v=true){
	if(preg_match("#\.[0-9]{4}\.#iUs", $title)){
		$tabs=preg_split("#(.+)\.([0-9]{4})\.#iUs", $title, -1, PREG_SPLIT_DELIM_CAPTURE);
		$title=str_replace('.', ' ', $tabs[1]);
		$year=$tabs[2];
		if($v){
			wr('Title = '.$title);
			wr('Year = '.$year);
		}
		$url="http://www.google.fr/search?q=".str_replace(' ', '+', $title)."+".$year."+site:www.allocine.fr";
		if($v){
			wr("Send request to $url ...");
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$html=new simple_html_dom();
		$html->load(curl_exec($ch));
		$tmp=html_entity_decode(urldecode(trim($html->find('#ires .r a', 0)->href)));
		if($v){
			wr($tmp);
		}
		if($tmp!=null&&strlen($tmp)>5){
			$id=preg_replace("#^(.+)?http://www\.allocine\.fr/film/fichefilm_gen_cfilm=([0-9]{1,})\.html(.+)?$#iUs", "$2", $tmp);
			if(intval($id)>0){
				if($v){
					wr("ID_ALLOCINE = $id");
				}
				return $id;
			}else{
				if($v){
					wr("The id allociné found is false ... #0011");
				}
				return false;
			}
		}else{
			if($v){
				wr("Error for find a allociné id ... #0010");
			}
			return false;
		}
	}else{
		wr('Année mal renseigné pour faire une recherche sur allociné #0012 --> '.$title);
		return false;
	}
}
function convert_to_time($h, $m, $s){
	$h=intval($h);
	$m=intval($m);
	$s=intval($s);
	
	$rs=0;
	
	if($h>0){
		$rs+=$h*60*60;
	}
	if($m>0){
		$rs+=$m*60;
	}
	if($s>0){
		$rs+=$s;
	}
	return $rs;
}
function add_to_remote_seedbox($url, $file, $dir, $login=null, $pass=null){
	// cheking params
	if($dir==null){
		$dir="";
	}
	if(trim($dir)==""){
		wr("A target directory is required ...");
	}
	// checking server ...
	
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_URL, $url.'php/addtorrent.php');
	if($login!=null&&$pass!=null){
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_USERPWD, "$login:$pass");
	}
	curl_exec($ch);
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE)==302){
		wr("This server use ruTorrent ...");
		$datas=array();
		$datas['dir_edit']=$dir;
		$datas['fast_resume']='on';
		/*$datas['torrents_start_stopped']='';
		$datas['not_add_path']='';
		$datas['tadd_label']='';*/
		$datas['torrent_file']="@".$file;
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url.'/php/addtorrent.php');
		if($login!=null&&$pass!=null){
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_USERPWD, "$login:$pass");
		}
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
   
		wr(curl_exec($ch));		
	}else{
		wr("Url server not supported ... #r0015");
		curl_close($ch);
		return false;
	}
	curl_close($ch);
}
function FTDB_download_torrent($id, $target, $ftdb_login, $ftdb_mdp){
	global $dir_cookies;
	$datas=array();
	$datas['username']=$ftdb_login;
	$datas['password']=$ftdb_mdp;
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://www.frenchtorrentdb.com/?section=LOGIN');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $dir_cookies."ftdb_cookies.txt");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
	curl_setopt($ch, CURLOPT_VERBOSE, false);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)" );
	curl_exec($ch);	
	
	curl_setopt($ch, CURLOPT_COOKIEFILE, $dir_cookies."ftdb_cookies.txt");
	
	curl_setopt($ch, CURLOPT_URL, 'http://www.frenchtorrentdb.com/?section=DOWNLOAD&id='.$id);
	$rs=curl_exec($ch);
	$link=preg_split("#<a class=\"dl_link\" href=\"(/\?section=DOWNLOAD&id=[0-9]+&hash=[a-z0-9A-Z]+&uid=[0-9]+&get=1)\">.+#iUs", $rs, -1, PREG_SPLIT_DELIM_CAPTURE);
	$link="http://www.frenchtorrentdb.com".$link[1];
	
	$fp=fopen($target, 'w');
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
	curl_close($ch);
	return true;
}
function uploadOnJHeberg($file){
	global $dir_public;
	if(is_readable($file)){
		shell_exec('ln -s '.$file.' '.$dir_public.basename($file));
		$datas=array();
		$datas['url']="http://62.75.252.71:51005/".basename($file);
		$datas['username']="Thales0796";
		$datas['password']="Nik@li@s07_96";
		$datas['filename']=basename($file);
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, "http://leela.jheberg.net/jheberg/index.php?method=apiRemote");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$rs=curl_exec($ch);
		$f=fopen('jheberg.up', 'a+');
		fwrite($f, $rs."\n");
		fclose($f);
		$tabs=json_decode($rs, true);
		if($tabs['error']==false){
			return $tabs['url'];
		}else{
			echo "Cannot upload on JHeberg ...";
			return false;
		}
	}else{
		echo "Cannot read file $file";
		return false;
	}	
}
function parse_title($name){
	if(preg_match("#^(.+)(\.[0-9]{4})?\.(french|vostfr|fastsub|fansub|multi|truefrench)(\..+)?(\.SUBFORCED)?\.(HDTV|BluRay\.[0-9]{3,4}p|BRRiP|DVDRiP|BDRiP|WEBRiP)\.((x264|xvid|mpeg2)(\.(AC3|AAC|DTS))?)-(.+)$#iUs", $name)){
		$rs=array();
		$tmp=preg_split("#^(.+)(\.[0-9]{4})?\.(french|vostfr|fastsub|fansub|multi|truefrench)(\..+)?(\.SUBFORCED)?\.(HDTV|BluRay\.[0-9]{3,4}p|BRRiP|DVDRiP|BDRiP|WEBRiP)\.((x264|xvid|mpeg2)(\.(AC3|AAC|DTS))?)-(.+)$#iUs", $name, -1, PREG_SPLIT_DELIM_CAPTURE);
		print_r($tmp);
		$rs['title']=str_replace('.', ' ', $tmp[1]);
		$rs['year']=substr($tmp[2], 1);
		$rs['lang']=strtoupper($tmp[3]);
		$rs['quality']=$tmp[6];
		$rs['codec']=str_replace('.', ' ', $tmp[7]);
		return $rs;
	}else{
		return false;
	}
}
function T411_get_kind($kind){
	switch(trim(str_replace(' ', '', str_replace(' ', '', strtolower($kind))))){
		case 'action':
			return 25;
			break;
		case 'thriller':
			return 59;
			break;
		case 'crime':
			return 55;
			break;
		case 'drame':
			return 39;
			break;
		case 'guerre':
			return 45;
			break;
		case 'comedie':
			return 31;
			break;
		case 'musique':
			return 51;
			break;
		case 'histoire':
			return 46;
			break;
		case 'science-fiction':
			return 58;
			break;
		case 'aventure':
			return 28;
			break;
		case 'romance':
			return 57;
			break;
		case 'horreur':
			return 40;
			break;
		case 'fantasy':
			return 44;
			break;
		case 'mystery':
			return 526;
			break;
		case 'drama':
			return 39;
			break;
		default:
			return 0;
			break;
	}
}
function remove_accents($s){
	$str = htmlentities($s, ENT_NOQUOTES, mb_detect_encoding($s));
    
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
    
    return $str;
}
