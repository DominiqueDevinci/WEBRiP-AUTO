<?php
wr("Generating UT prez ...");
wr("Sumarry encodage = ".mb_detect_encoding($summary)." & imdb encoodage = ".mb_detect_encoding($summary)." & local encoding = ".mb_detect_encoding("essai"));

$ut_prez="";
if(isset($thumbs)){
	if(!isset($public_thumbs)||$public_thumbs==null){
		wr('Uploading thumbs on casimage ...');
		$public_thumbs=get_public_thumbs($thumbs);
	}
}
wr('Charset summary : '.mb_detect_encoding($summary));
if(!isset($casimage_poster)||trim($casimage_poster)==""){
$casimage_poster=move_to_casimage($poster);
}

$ut_prez .= "[center][font=georgia][size=6][color=#113E6B][b] ".utf8_encode($title_fr)." [/b][/color][/size][/font]\n";
$ut_prez .= "\n";
$ut_prez .= "[img]".$casimage_poster."[/img]\n";
$ut_prez .= "\n";
$ut_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033613927553.png[/img]\n";
$ut_prez .= "\n";
$ut_prez .= "[b]Titre:[/b] ".utf8_encode($title_fr)."\n";
$ut_prez .= "[b]Titre Original:[/b] ".utf8_encode($title_vo)."\n";
$ut_prez .= "[b]Genre:[/b] ".$kind."\n";
$ut_prez .= "[b]Durée:[/b] ".$duration."\n";
$ut_prez .= "[b]Date De Sortie:[/b] ".utf8_encode($date)."\n";
$ut_prez .= "[b]Réalisateur:[/b] ".utf8_encode($director)."\n";
$ut_prez .= "[b]Origine Du Film:[/b] ".$origin."\n";
$ut_prez .= "[b]Note Des Spectateurs:[/b] ".$note."\n";
$ut_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033400942421.png[/img]\n";
$ut_prez .= "\n[i]";
$ut_prez .= utf8_encode($summary);
wr("UT summary = $summary");
$ut_prez .= "[/i]\n\n";
$ut_prez .= "[b]Liens IMDB[/b] : [url]http://www.imdb.fr/title/tt".$imdb."[/url]\n";
$ut_prez .= (isset($allocine)?"[b]Liens Allociné:[/b] [url]http://www.allocine.fr/film/fichefilm_gen_cfilm=".$allocine.".html[/url]\n":"");
$ut_prez .= "\n";
if(isset($public_thumbs)&&$public_thumbs!=null){
	$ut_prez .= "[img]http://nsa30.casimages.com/img/2013/01/09/13010903331487553.png[/img]\n";
	$ut_prez .= "\n";
	$ut_prez .= "[url=$public_thumbs][img]".$public_thumbs."[/img][/url]\n";
	$ut_prez .= "\n";
}

$ut_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033236877857.png[/img]\n";

$ut_prez .= "\n";
$ut_prez .= (isset($rlz_src)?"[b]Source:[/b] [color=green]".$rlz_src."[/color]":"")
."\n[b]Codec vidéo: [/b][i] ".$vcodec."[/i]\n"
."[b]Codec audio: [/b][i] ".$acodec."[/i]\n"
."[b]Bitrate global: [/b][b][i][color=#ff0000]".$bt."[/color][/i][/b]\n"
."\n[b]Langue: [/b][i] ".$a_langs."[/i]"
."\n[b]Sous-titre: [/b][i] ".($s_langs!=""?$s_langs:"Aucun")."[/i]\n"
.(preg_match('#SUBFORCED#iUs', $filename)?"[b][u]Sous-titres forcés[/u][/b] (durant les passages étrangers) : [i] French[/i]\n":"")
."\n[size=5][color=red]Taille :[b] ".$size."[/b][/color][/size]\n";
$ut_prez .= "[img]http://nsa30.casimages.com/img/2013/01/09/13010903255769042.png[/img][/center]\n";
$ut_prez=html_entity_decode(urldecode($ut_prez));
