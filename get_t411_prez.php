<?php
wr("Generating T411 prez ...");
wr("Sumarry encodage = ".mb_detect_encoding($summary)." & imdb encoodage = ".mb_detect_encoding($summary)." & local encoding = ".mb_detect_encoding("essai"));

$t411_prez="";

// Generating thumbs especially for T411 ...
shell_exec('mtn "'.$file.'" -f /usr/share/fonts/truetype/ttf-dejavu/DejaVuSerif.ttf -T "Encoded and proudly presents by CiRAR" -c 1 -r 3 -D 12 -j 96 -k FFFFFF -F 161616:10 -O /home/bot/ -o _t411.jpg > /home/www/thal/ogmrip/logs/mtn.log');
$t411_local_thumbs='/home/bot/'.substr(basename($file), 0, -4).'_t411.jpg';		
wr('Uploading t411 thumbs on casimage ...');
$t411_thumbs=get_public_thumbs($t411_local_thumbs);
wr($file." T411 local thumbs = ".$t411_local_thumbs." & T411 public thumbs = ".$t411_thumbs);



if(!isset($casimage_poster)||trim($casimage_poster)==""){
$casimage_poster=move_to_casimage($poster);
}

$actors_str="";
foreach($actors as $k => $v){
	$actors_str.="[b]".$k."[/b]".(trim(strval($v))!=""?"(".$v.")":"").", ";
}
$actors_str=str_replace('))', ')', $actors_str);
$actors_str=substr($actors_str, 0, -2);
$t411_prez .= "[center][font=georgia][size=6][color=#113E6B][b] ".$title_fr." [/b][/color][/size][/font]\n";
$t411_prez .= "\n";
$t411_prez .= "[img=".$casimage_poster."]\n";
$t411_prez .= "\n";
$t411_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033613927553.png[/img]\n";
$t411_prez .= "\n";
$t411_prez .= "[b]Titre:[/b] $title_fr\n";
$t411_prez .= "[b]Titre Original:[/b] $title_vo\n";
$t411_prez .= "[b]Genre:[/b] ".$kind."\n";
$t411_prez .= "[b]Durée:[/b] ".$duration."\n";
$t411_prez .= "[b]Date De Sortie:[/b] ".$date."\n";
$t411_prez .= "[b]Réalisateur:[/b] ".$director."\n";
$t411_prez .= "[b]Acteurs:[/b] ".$actors_str."\n";
$t411_prez .= "[b]Origine Du Film:[/b] ".$origin."\n";
$t411_prez .= "[b]Note Des Spectateurs:[/b] ".$note."\n";
$t411_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033400942421.png[/img]\n";
$t411_prez .= "\n[i]";
$t411_prez .= $summary;
$t411_prez .= "[/i]\n\n";
$t411_prez .= "[b]Liens IMDB[/b] : [url]http://www.imdb.fr/title/tt".$imdb."[/url]\n";
$t411_prez .= (isset($allocine)?"[b]Liens Allociné:[/b] [url]http://www.allocine.fr/film/fichefilm_gen_cfilm=".$allocine.".html[/url]\n":"");
$t411_prez .= "\n";
if(isset($t411_thumbs)&&$t411_thumbs!=null){
	$t411_prez .= "[img]http://nsa30.casimages.com/img/2013/01/09/13010903331487553.png[/img]\n";
	$t411_prez .= "\n";
	$t411_prez .= "[url=$public_thumbs][img=".$t411_thumbs."][/url]\n";
	$t411_prez .= "\n";
}

$t411_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033236877857.png[/img]\n";

$t411_prez .= "\n";
$t411_prez .= (isset($rlz_src)?"[b]Source:[/b] [color=green]".$rlz_src."[/color]":"")
."\n[b]Codec vidéo: [/b][i] ".$vcodec."[/i]\n"
."[b]Codec audio: [/b][i] ".$acodec."[/i]\n"
."[b]Bitrate global: [/b][b][i][color=#ff0000]".$bt."[/color][/i][/b]\n"
."\n[b]Langue: [/b][i] ".$a_langs."[/i]"
."\n[b]Sous-titre: [/b][i] ".($s_langs!=""?$s_langs:"Aucun")."[/i]\n"
.(preg_match('#SUBFORCED#iUs', $filename)?"[b][u]Sous-titres forcés[/u][/b] (durant les passages étrangers) : [i] French[/i]\n":"")
."\n[size=5][color=red]Taille :[b] ".$size."[/b][/color][/size]\n";
$t411_prez .= "[img]http://nsa30.casimages.com/img/2013/01/09/13010903255769042.png[/img][/center]\n";
$t411_prez=str_replace('&#x27;', '\'', (html_entity_decode(urldecode(html_entity_decode($t411_prez)))));
