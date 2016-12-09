<?php
wr("Generating paradis-du-net prez ...");
$pdn_prez="";
if(isset($thumbs)){
	if(!isset($public_thumbs)||$public_thumbs==null){
		wr('Uploading thumbs on casimage ...');
		$public_thumbs=get_public_thumbs($thumbs);
	}
}
wr("Sumarry encodage = ".mb_detect_encoding($summary)." & imdb encoodage = ".mb_detect_encoding($summary)." & local encoding = ".mb_detect_encoding("essai"));
if(!isset($casimage_poster)||trim($casimage_poster)==""){
	$casimage_poster=move_to_casimage($poster);
}

$actors_str="";
foreach($actors as $k => $v){
	$actors_str.="[b]".$k."[/b]".(trim(strval($v))!=""?"(".$v.")":"").", ";
}
$actors_str=substr($actors_str, 0, -2);
$pdn_prez .= "[center][font=georgia][size=6][color=#113E6B][b] ".utf8_encode($title_fr)." [/b][/color][/size][/font]\n";
$pdn_prez .= "\n";
$pdn_prez .= "[img]".$casimage_poster."[/img]\n";
$pdn_prez .= "\n";
$pdn_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033613927553.png[/img]\n";
$pdn_prez .= "\n";
$pdn_prez .= "[b]Titre:[/b] ".utf8_encode($title_fr)."\n";
$pdn_prez .= "[b]Titre Original:[/b] ".utf8_encode($title_vo)."\n";
$pdn_prez .= "[b]Genre:[/b] ".$kind."\n";
$pdn_prez .= "[b]Duree:[/b] ".$duration."\n";
$pdn_prez .= "[b]Date De Sortie:[/b] ".utf8_encode($date)."\n";
$pdn_prez .= "[b]Realisateur:[/b] ".utf8_encode($director)."\n";
$pdn_prez .= "[b]Acteurs:[/b] ".utf8_encode($actors_str)."\n";
$pdn_prez .= "[b]Origine Du Film:[/b] ".utf8_encode($origin)."\n";
$pdn_prez .= "[b]Note Des Spectateurs:[/b] ".$note."\n";
$pdn_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033400942421.png[/img]\n";
$pdn_prez .= "\n[i]";
$pdn_prez .= utf8_encode($summary);

$pdn_prez .= "[/i]\n\n";
$pdn_prez .= "[b]Liens IMDB[/b] : [url]http://www.imdb.fr/title/tt".$imdb."[/url]\n";
$pdn_prez .= (isset($allocine)?"[b]Liens Allocine:[/b] [url]http://www.allocine.fr/film/fichefilm_gen_cfilm=".$allocine.".html[/url]\n":"");
$pdn_prez .= "\n";
if(isset($public_thumbs)&&$public_thumbs!=null){
	$pdn_prez .= "[img]http://nsa30.casimages.com/img/2013/01/09/13010903331487553.png[/img]\n";
	$pdn_prez .= "\n";
	$pdn_prez .= "[url=$public_thumbs][img]".$public_thumbs."[/img][/url]\n";
	$pdn_prez .= "\n";
}

$pdn_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033236877857.png[/img]\n";

$pdn_prez .= "\n";
$pdn_prez .= (isset($rlz_src)?"[b]Source:[/b] [color=green]".$rlz_src."[/color]":"")
."\n[b]Codec video: [/b][i] ".$vcodec."[/i]\n"
."[b]Codec audio: [/b][i] ".$acodec."[/i]\n"
."[b]Bitrate global: [/b][b][i][color=#ff0000]".$bt."[/color][/i][/b]\n"
."\n[b]Langue: [/b][i] ".$a_langs."[/i]"
."\n[b]Sous-titres: [/b][i] ".($s_langs!=""?$s_langs:"Aucun")."[/i]\n"
.(preg_match('#SUBFORCED#iUs', $filename)?"[b][u]Sous-titres forcés[/u][/b] (durant les passages étrangers) : [i] French[/i]\n":"")
."\n[size=5][color=red]Taille :[b] ".$size."[/b][/color][/size]\n";
$pdn_prez .= "[img]http://nsa30.casimages.com/img/2013/01/09/13010903255769042.png[/img][/center]\n";
$pdn_prez=str_replace('&#x27;', '\'', (html_entity_decode(urldecode(html_entity_decode($pdn_prez)))));
