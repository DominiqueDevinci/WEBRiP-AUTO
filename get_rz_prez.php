<?php
	echo 'execute : rar -m0 -v750M a '.$file.'.rar '.urldecode($file)."\n";
	if(is_readable($file.".rar")){
		echo "rm -f ".$file.".rar";
		shell_exec("rm -f ".$file.".rar");
	}
	for($i=1;$i<100;$i++){
		if(is_readable($file.'.part'.$i.'.rar')){
			shell_exec("rm -f ".$file.'.part'.$i.'.rar');
			echo "rm -f ".$file.'.part'.$i.'.rar'."\n";
		}else{
			break;
		}	
	}
	$r=shell_exec('rar -m0 -v750M a '.$file.'.rar '.urldecode($file).'  2>&1');
	$f=fopen('rar_logs.txt', 'a+');
	fwrite($f, $r."\n\n");
	fclose($f);
	$tab=array();
	for($i=1;$i<100;$i++){
		echo 'is_readeable : '.urldecode($file).'.part'.$i.'.rar'."\n";
		if(is_readable(urldecode($file).'.part'.$i.'.rar')){
			$tab[]=urldecode($file).'.part'.$i.'.rar';
		}else{
			break;
		}
	}
	$links_jheberg=array();
	if(count($tab)>0){
		foreach($tab as $file){
			$tmp=uploadOnJHeberg($file);
			if($tmp!=false){
				$links_jheberg[]=$tmp;
			}else{
				echo 'Error : #e002 (echec pour uploader sur jheberg).';
				exit(0);
			}
		}
	}else{
		echo "Error : #e001\n";
		exit(0);
	}

wr("Generating RZ prez ...");
wr("Uploading rlz on JHeberg ...");
wr("Sumarry encodage = ".mb_detect_encoding($summary)." & imdb encoodage = ".mb_detect_encoding($summary)." & local encoding = ".mb_detect_encoding("essai"));


$rz_prez="";
if(isset($thumbs)){
	if(!isset($public_thumbs)||$public_thumbs==null){
		wr('Uploading thumbs on casimage ...');
		$public_thumbs=get_public_thumbs($thumbs);
	}
}
if(isset($t411_poster)){
	$rz_poster=$t411_poster;
}else{
	$rz_poster=move_to_casimage($poster);
}
$actors_str="";
foreach($actors as $k => $v){
	$actors_str.="[b]".$k."[/b]".(trim(strval($v))!=""?"(".$v.")":"").", ";
}
$actors_str=substr($actors_str, 0, -2);
$rz_prez .= "[center][b] ".utf8_encode($title_fr)." [/b]\n";
$rz_prez .= "\n";
$rz_prez .= "[img]".$rz_poster."[/img]\n";
$rz_prez .= "\n";
$rz_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033613927553.png[/img]\n";
$rz_prez .= "\n";
$rz_prez .= "[b]Titre:[/b] ".utf8_encode($title_fr)."\n";
$rz_prez .= "[b]Titre Original:[/b] ".utf8_encode($title_vo)."\n";
$rz_prez .= "[b]Genre:[/b] ".$kind."\n";
$rz_prez .= "[b]Durée:[/b] ".$duration."\n";
$rz_prez .= "[b]Date De Sortie:[/b] ".$date."\n";
$rz_prez .= "[b]Réalisateur:[/b] ".utf8_encode($director)."\n";
$rz_prez .= "[b]Acteurs:[/b] ".$actors_str."\n";
$rz_prez .= "[b]Origine Du Film:[/b] ".$origin."\n";
$rz_prez .= "[b]Note Des Spectateurs:[/b] ".$note."\n";
$rz_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033400942421.png[/img]\n";
$rz_prez .= "\n[i]";
$rz_prez .= $summary;
wr("RZ ummary = $summary");
$rz_prez .= "[/i]\n\n";
$rz_prez .= "[quote][b]Liens IMDB[/b] : [url]http://www.imdb.fr/title/tt".$imdb."[/url][/quote]\n";
$rz_prez .= (isset($allocine)?"[quote][b]Liens Allociné:[/b] [url]http://www.allocine.fr/film/fichefilm_gen_cfilm=".$allocine.".html[/url][/quote]\n":"");
$rz_prez .= "\n";
if(isset($public_thumbs)&&$public_thumbs!=null){
	$rz_prez .= "[img]http://nsa30.casimages.com/img/2013/01/09/13010903331487553.png[/img]\n";
	$rz_prez .= "\n";
	$rz_prez .= "[url=$public_thumbs][img]".$public_thumbs."[/img][/url]\n";
	$rz_prez .= "\n";
}

$rz_prez .= "[img]http://nsa29.casimages.com/img/2013/01/09/130109033236877857.png[/img]\n";

$rz_prez .= "\n";
$rz_prez .= (isset($rlz_src)?"[b]Source:[/b] [color=green]".$rlz_src."[/color]":"")
."\n[b]Codec vidéo: [/b][i] ".$vcodec."[/i]\n"
."[b]Codec audio: [/b][i] ".$acodec."[/i]\n"
."[b]Bitrate global: [/b][b][i][color=#ff0000]".$bt."[/color][/i][/b]\n"
."\n[b]Langue: [/b][i] ".$a_langs."[/i]"
."\n[b]Sous-titre: [/b][i] ".($s_langs!=""?$s_langs:"Aucun")."[/i]\n"
.(preg_match('#SUBFORCED#iUs', $filename)?"[b][u]Sous-titres forcés[/u][/b] (durant les passages étrangers) : [i] French[/i]\n":"")
."\n[color=red]Taille :[b] ".$size."[/b][/color]\n";

if(count($links_jheberg)>0){
	$i=0;
	foreach($links_jheberg as $l){
		$rz_prez .="[quote][url=".$l."] -- Télécharger sur JHeberg (part ".(++$i)." ) --[/url][/quote]\n";
	}
}else{
	echo "array $links_jheberg vide";
	exit(0);
}

$rz_prez .= "[img=http://uppix.net/3/a/b/f8e904e6a5dafd19e628db0d7f3f2.png]http://uppix.net/3/a/b/f8e904e6a5dafd19e628db0d7f3f2.png[/img] [img=http://uppix.net/e/b/2/f7e4217d6f70bff2c9cb67227e773.png]http://uppix.net/e/b/2/f7e4217d6f70bff2c9cb67227e773.png[/img] ";
$rz_prez .= "\n[code]".utf8_encode(file_get_contents($nfo))."[/code][/center]";
$rz_prez=str_replace('&#x27;', '\'', (html_entity_decode(urldecode(html_entity_decode($rz_prez)))));

