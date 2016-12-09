<?php
if(isset($thumbs)&&is_readable($thumbs)){
	wr('Uploading thumbs on GKS ...');
	$gks_thumbs=GKS_upload_img($thumbs);
	wr("Thumbs uploaded on GKS : ".$gks_thumbs);
}

wr('Uploading poster on GKS ...');
$gks_poster=GKS_upload_img($poster);
wr('Poster uploaded on GKS !');

wr('Generating GKS prez ...');
$actors_str="";
foreach($actors as $k => $v){
	$actors_str.="[b]".$k."[/b]".(trim(strval($v))!=""?"(".$v.")":"").", ";
}

$actors_str=substr($actors_str, 0, -2);

if(strtolower(mb_detect_encoding($summary))=='iso-8859-1'){
	wr("[GKS] Encodage iso-8859-1 ...    [OK]");
}else{
	wr("[GKS] Summary encodage encoding to iso-8859-1 ...          [OK]");
	$summary=mb_convert_encoding($summary, 'iso-8859-1', mb_detect_encoding($summary));
}
wr("Sumarry encodage = ".mb_detect_encoding($summary)." & imdb encoodage = ".mb_detect_encoding($summary)." & local encoding = ".mb_detect_encoding("essai"));

$gks_prez="[center][color=red][size=7][b]".$title_fr."[/b][/size][/color]"
."\n\n"
."[size=4][img]".$gks_poster."[/img]"
."\n\n"
."[img]https://s.gks.gs/img/img/01-2013/httpss.gks.gs.img.img.01-2013.Synopsis_du_Film_silver_CiRar_.png[/img]"
."\n\n"
."[i]".$summary."[/i]"
."\n\n"
."[img]https://s.gks.gs/img/img/01-2013/httpss.gks.gs.img.img.01-2013.Infos_sur_le_Film_silver_CiRAR_.png[/img]"
."\n\n"
."[b][u]Origine du media[/u] : [/b][i] ".$origin."[/i]\n"
."[b][u]Réalisateur[/u] : [/b][i] ".$director."[/i]\n"
."[b][u]Scenariste[/u] : [/b][i] ".implode(', ', $scenarists)."[/i]\n"
."[b][u]Acteurs[/u] : [/b] [/center][/size][spoil]".$actors_str."[/spoil][size=4][center]\n"
."[b][u]Genre[/u] : [/b][i] ".$kind."[/i]\n"
."[b][u]Durée[/u] : [/b][i] ".$duration."[/i]\n"
."[b][u]Date de sortie[/u] : [/b][i] ".$date."[/i]\n"
."[b][u]Titre Original[/u] : [/b][i] ".$title_vo."[/i]\n"
."[b][u]Critiques spectateurs[/u] : [/b][i] ".$note."[/i]\n"
."[b][u]Liens IMDB[/u] : [/b] [i][url]http://www.imdb.fr/title/tt".$imdb."[/url][/i]\n"
.(isset($allocine)?"[b][u]Liens Allociné[/u] : [/b] [i][url]http://www.allocine.fr/film/fichefilm_gen_cfilm=".$allocine.".html[/url][/i]\n":"")
.($gks_thumbs!=null?"\n[img]https://s.gks.gs/img/img/01-2013/httpss.gks.gs.img.img.01-2013.Thumbnails_silver_CiRAR.png[/img]\n"
."\n[url=".$gks_thumbs."][img]".$gks_thumbs."[/img][/url]\n":"")
."\n[img]https://s.gks.gs/img/img/01-2013/httpss.gks.gs.img.img.01-2013.Infos_sur_ll_Upload_silver_CiRAR.png[/img]\n"

.(isset($rlz_src)?"[b][u]Source[/u]: [/b][color=green][i] ".$rlz_src."[/i][/color]\n":"")
."[b][u]Codec vidéo[/u]: [/b][i] ".$vcodec."[/i]\n"
."[b][u]Codec audio[/u]: [/b][i] ".$acodec."[/i]\n"
."[b][u]Bitrate global[/u]: [/b][b][i][color=#ff0000]".$bt."[/color][/i][/b]\n"
."\n[b][u]Langue[/u]: [/b][i] ".$a_langs."[/i]"
."\n[b][u]Sous-titre[/u]: [/b][i] ".($s_langs!=""?$s_langs:"Aucun")."[/i]\n"
.(preg_match('#SUBFORCED#iUs', $filename)?"[b][u]Sous-titres forcés[/u][/b] (durant les passages étrangers) : [i] French[/i]\n":"")
."\n[/size][size=5][color=red]Taille :[b] ".$size."[/b][/color][/size]\n"
."\n[img]https://s.gks.gs/img/img/01-2013/httpss.gks.gs.img.img.01-2013.Restez_en_Seed_silver_CiRAR.png[/img][/center]";
$gks_prez=html_entity_decode($gks_prez);
$gks_prez_file="/home/bot/".basename($torrent, '.torrent').".gks.prez";
$f=fopen($gks_prez_file, "w+");
fwrite($f, $gks_prez);
fclose($f);
