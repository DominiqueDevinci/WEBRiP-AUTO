<?php
$chars = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
    );

wr("Sumarry encodage = ".mb_detect_encoding($summary)." & imdb encoodage = ".mb_detect_encoding($summary)." & local encoding = ".mb_detect_encoding("essai"));

wr("Generating FTDB prez ...");
$ftdb_prez="";
if(isset($thumbs)){
	if(!isset($public_thumbs)||$public_thumbs==null){
		wr('Uploading thumbs on STOOORAGE ...');
		$public_thumbs=get_public_thumbs($thumbs);
	}
}
if(!isset($casimage_poster)||trim($casimage_poster)==""){
$casimage_poster=move_to_casimage($poster);
}
$actors_str="";
foreach($actors as $k => $v){
	$actors_str.="[b]".$k."[/b]".(trim(strval($v))!=""?"(".$v.")":"").", ";
}
$actors_str=substr($actors_str, 0, -2);
$ftdb_prez .= "[center][font=georgia][size=6][color=#113E6B][b] ".html_entity_decode($title_fr)." [/b][/color][/size][/font]\n";
$ftdb_prez .= "\n";
$ftdb_prez .= "[img]".$casimage_poster."[/img]\n";
$ftdb_prez .= "\n";
$ftdb_prez .= "[img]http://img2.stooorage.com/images/1969/8001710_httpss-gks-gs-img-img-01-2013-synopsis_du_film_silver_cirar_.png[/img]\n";
$ftdb_prez .= "\n[i]";
$ftdb_prez .= $summary;
$ftdb_prez .= "[/i]\n\n";
$ftdb_prez .= "[b]Liens IMDB[/b] : [url]http://www.imdb.fr/title/tt".$imdb."[/url]\n";
$ftdb_prez .= (isset($allocine)?utf8_decode("[b]Liens Allociné:[/b]")." [url]http://www.allocine.fr/film/fichefilm_gen_cfilm=".$allocine.".html[/url]\n":"");
$ftdb_prez .= "\n";
$ftdb_prez .= "[img]http://img1.stooorage.com/images/1319/8001733_httpss-gks-gs-img-img-01-2013-infos_sur_le_film_silver_cirar_.png[/img]\n";
$ftdb_prez .= "\n";
$ftdb_prez .= "[b]Titre:[/b] ".html_entity_decode($title_fr)."\n";
$ftdb_prez .= "[b]Titre Original:[/b] ".html_entity_decode($title_vo)."\n";
$ftdb_prez .= "[b]Genre:[/b] ".$kind."\n";
$ftdb_prez .= utf8_decode("[b]Durée:[/b] ").$duration."\n";
$ftdb_prez .= "[b]Date De Sortie:[/b] ".html_entity_decode($date)."\n";
$ftdb_prez .= utf8_decode("[b]Réalisateur:[/b] ").$director."\n";
$ftdb_prez .= "[b]Acteurs:[/b] ".html_entity_decode($actors_str)."\n";
$ftdb_prez .= "[b]Origine Du Film:[/b] ".html_entity_decode($origin)."\n";
$ftdb_prez .= "[b]Note Des Spectateurs:[/b] ".$note."\n";
$ftdb_prez .= "\n";
if(isset($public_thumbs)&&$public_thumbs!=null){
	$ftdb_prez .= "[img]http://img2.stooorage.com/images/1969/8001712_httpss-gks-gs-img-img-01-2013-thumbnails_silver_cirar.png[/img]\n";
	$ftdb_prez .= "\n";
	$ftdb_prez .= "[url=$public_thumbs][img]".$public_thumbs."[/img][/url]\n";
	$ftdb_prez .= "\n";
}
$ftdb_prez .= "[img]http://img2.stooorage.com/images/1969/8001713_httpss-gks-gs-img-img-01-2013-infos_sur_ll_upload_silver_cirar.png[/img]\n";

$ftdb_prez .= "\n";
$ftdb_prez .= (isset($rlz_src)?"[b]Source:[/b] [color=green]".$rlz_src."[/color]\n":"")
.utf8_decode("\n[b]Codec vidéo: [/b][i] ").$vcodec."[/i]\n"
.utf8_decode("[b]Codec audio: [/b][i] ").$acodec."[/i]\n"
."[b]Bitrate global: [/b][b][i][color=#ff0000]".$bt."[/color][/i][/b]\n"
."\n[b]Langue: [/b][i] ".$a_langs."[/i]"
."\n[b]Sous-titre: [/b][i] ".($s_langs!=""?$s_langs:"Aucun")."[/i]\n"
.utf8_decode((preg_match('#SUBFORCED#iUs', $filename)?"[b][u]Sous-titres forcés[/u][/b] (durant les passages étrangers) : [i] French[/i]\n":""))
."\n[size=5][color=red]Taille :[b] ".$size."[/b][/color][/size]\n";
$ftdb_prez .= "[img]http://img2.stooorage.com/images/1969/8001714_httpss-gks-gs-img-img-01-2013-restez_en_seed_silver_cirar.png[/img][/center]\n";
$ftdb_prez=str_replace('&#x27;', '\'', (urldecode(html_entity_decode($ftdb_prez))));

$ftdb_prez_file="/home/www/thal/bot_public/".basename($torrent, '.torrent').".txt";
$f=fopen($ftdb_prez_file, "w+");
fwrite($f, $ftdb_prez);
fclose($f);

