<?php

/**********************************************************************
 * 
 * 					Powered by CiRAR Team
 * 
 * 			Produced by Thales0796 for CiRAR team
 * 
 * 						v1.4 (using ffmpeg)
 * 
 **********************************************************************/


ignore_user_abort(true);
set_time_limit(0);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> CiRAR Team Encoding Bot v1.4</title>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" ></script>
<link rel="stylesheet" type="text/css" media="all" href="./modalbox/css/jquery.modalbox.css" />
<script type="text/javascript" src="./modalbox/js/jquery_modalbox_js_min.js"></script>

</head>
<body>
	
<?php

include('/home/www/thal/ogmrip/functions.php');
include('/home/www/thal/ogmrip/rip_vars.php');
include('/home/www/thal/ogmrip/rip_functions.php');

if(isset($_POST['get'])){
	switch($_POST['get']){
	case 'add':
		$token=md5(uniqid(rand(), true));
		$f=fopen('/home/www/thal/ogmrip/queue.txt', 'a+');
		fwrite($f, "&[waiting][$token]".urlencode($_POST['cmd']." > /home/www/thal/ogmrip/logs/$token.log"));
		fclose($f);
		$f=fopen('/home/www/thal/ogmrip/metas/'.$token.'.cirar', 'a+');
		fwrite($f, $_POST['metas']);
		fclose($f);
		echo 'ok';
		return;
		break;
	case 'form':
		if(isset($_POST['input'])){
			$input=urldecode("/home/thal/disk2/dl/".$_POST['input']);
			if(!is_readable($input)){
				echo "<b style='color:red;' >#r0008, cannot read input file ... </b><i>$input</i><br />";
				exit(0);
			}
			echo "<center id='loading' ><img src='./loading.gif' /><br />
			<b style='color:red;' id='strload' > Loading ... </b></center>
			<div style='width:150px;position:absolute;top:0;right:170px;display:none;z-index:-10;' id='img'></div>
			<a style='color:red;' class='openmodalbox large' href='javascript:void(0);'> View input mediainfo <input type='hidden' name='ajaxhref' value='./mediainfo.php?file=".urlencode($input)."' ></a><br />";
			echo "<label for='output' >Output file : </label><input id=output required size=90 placeholder='Target file path ...' type='text' name='output' value='".get_suggest_output(urldecode($_POST['input']))."' /><br />
	<label for='rlz_src' >Release source : </label><input id='rlz_src' name='rlz_src' size=50 placeholder='Source release (quality and/or team)' value='".get_rlz_src(urldecode($_POST['input']))."' /><br />
	<label for='type' >Type : </label><select id='type' name='type' >
		<option value='dvdrip_ac3' > AC3 (1.37 Go ou 2.05 Go) </option>
		<option value='dvdrip_mp3' > MP3 (700 Mo ou 1.37 Go) </option>
	</select>
	<br />
	<label for='imdb' >IMDB : </label><input id='imdb' name='imdb' placeholder='Searching ...' type='text' /> <a href='#' target='_blank' id='link_imdb' ></a>
	<b id='title_fr' style='display:none;' ><br /></b><br />
	<label for='allocine' >Allociné : </label><input id='allocine' name='allocine' placeholder='Searching ...' type='text' /> <a href='#' target='_blank' id='link_allocine' ></a><br />
	<div id='summary' style='width:70%;background-color:beige;display:none;border:2px outset blue;font-style:italic;font-weight:bold;' ></div>
	<label for='poster' >Customize poster : </label><input type='text' id='poster' name='poster' placeholder='Not required, potser will be search automaticly' /><br />
	<br />";
	?>
	<script type="text/javascript" >
	$(document).ready(function(){
		var str=$('#strload');
		str.html(" Searching on imdb ... ");
		$.ajax({
			type: "GET",
			url: "suggest.php",
			data: "type=imdb&title=<?php echo urlencode(basename($input)); ?>",
			success: function(msg){
				$('#imdb').val(msg);
				$('#link_imdb').attr('href', 'http://www.imdb.fr/title/tt'+msg+'/');
				$('#link_imdb').html(' View link ');	
				load_imdb_infos(msg);
			}
		});
		
		$('#imdb').change(function(){
			load_imdb_infos($('#imdb').val());
		});
		function load_imdb_infos(id){
			var img=$('#img');
			var title=$('#title_fr');
			var summary=$('#summary');
			img.html('');
			summary.html('');
			summary.fadeOut(0);
			title.html('');
			title.fadeOut(0);
			$('#loading').fadeIn(500);
			str.html(" Parsing imdb infos ... ");
			$.ajax({
				type: "GET",
				url: "get_imdb_infos.php",
				data: "id="+id+"&title=<?php echo urlencode(basename($input)); ?>",
				success: function(msg){
					try{
						msg=jQuery.parseJSON(msg);
					}catch(err){
						str.html("Error for parse imdb infos :(");
						alert(msg);
						// erreur interceptée ...
					}
					img.html('<img onMouseOver="$(this).css(\'opacity\', 1);" onMouseOut="$(this).css(\'opacity\', 0.2);" style="max-width:300px;opacity:0.2" id="img_poster" src="https://s.gks.gs/cache/cache.php?c='+msg['poster']+'" />');
					img.fadeIn(500);
					title.html('<br />Title : <b style="color:red;" >'+msg['title_fr']+" ("+msg['title_vo']+") </b><br />Duration = <b style='color:blue;' >"+msg['duration']+"</b><br />Date = <b style='color:green;' >"+msg['date']+"</b>");
					title.fadeIn(500);
					$('#allocine').val(msg['allocine']);
					$('#link_allocine').attr('href', 'http://www.allocine.fr/film/fichefilm_gen_cfilm='+msg['allocine']+'.html');
					$('#link_allocine').html(' View link ');
					str.html(" Searching on allocine ... ");
					$.ajax({
						type: "GET",
						url: "get_summary.php",
						data: "id_imdb="+id+"&id_allocine="+msg['allocine']+"&quiet=on",
						success: function(msg2){
							summary.html(msg2);
							summary.fadeIn(500);
							$('#loading').fadeOut(500);
						}
					});
				}
			});
			
		}
	});
	
	</script>
	<?php
			$rs=shell_exec('hb -t 0 --scan -i "'.$input.'" 2>&1');
			$tabs=split("\n", $rs);
			$titles=get_hb_infos($tabs);
			$selected=$titles['selected'];
			unset($titles['selected']);
			//print_r($titles);
				
			foreach($titles as $title => $infos){
				// display video option
				echo "<input type='radio' id='c_$title' name='title' value='$title' onClick=\"$('#title_$title').slideToggle();\" /><label for='c_$title' style='font-size:18px;font-weight:bold;color:red;' > Title #$title (".$titles[$title]['video']['human_duration'].") </label><br /><div style='display:none;' id='title_$title' >";
				echo "<form type='post' id='form_$title' name='form_$title' >";
				//video
				echo '<fieldset><legend> Vidéo </legend>';
				$video=$infos['video'];
				echo "<i class='i' >Duration:</i>".$video['human_duration'].'<br />';
				echo "<i class='i' >Resolution:</i>".$video['reso'].'<br />';
				echo "<i class='i' >Crop:</i> T=".$video['crop_top']." B=".$video['crop_bottom']." L=".$video['crop_left']." R=".$video['crop_right'].'<br />';
					$rtmp=explode('x', $video['reso']);
					$width=intval($rtmp[0]);
					$height=intval($rtmp[1]);  
					$width=$width-intval($video['crop_left'])-intval($video['crop_right']);
					$height=$height-intval($video['crop_top'])-intval($video['crop_bottom']);
					$ar=round_ar($width/$height);				
			
				echo "<i class='i' >AR:</i>".$ar.'<br />';
				echo "<i class='i' >Aspect:</i>".$video['aspect'].'<br />';
				echo "<i class='i' >FPS:</i>".$video['fps'].' fps<br />';
				echo '</fieldset>';
				
				//audio
				echo '<fieldset><legend> Audio tracks </legend>';
				foreach($infos['audio'] as $track_audio => $audio){
					echo "<input type='checkbox' id='$title-audio_$track_audio' name='t_$title-audio_$track_audio' onClick=\"$('#$title-daudio_$track_audio').slideToggle();\" /><label for='$title-audio_$track_audio' style='font-size:18px;font-weight:bold;color:green;' > Audio #$track_audio (".get_human_lang($audio['lang']).", ".$audio['codec'].", ".($audio['bps']/1000)." Kbps) </label><br />
					<div id='$title-daudio_$track_audio' style='display:none;' >";
						echo "<i class='i' >Langage:</i>".get_human_lang($audio['lang']).'<br />';
						echo "<i class='i' >Codec:</i>".$audio['codec'].'<br />';
						echo "<i class='i' >Bitrate:</i>".($audio['bps']/1000).' Kbps<br />';
						echo "<i class='i' >Channels:</i>".$audio['ch'].'<br />';
						echo "<i class='i' >Sampling:</i>".($audio['hz']/1000).' Khz<br />';	
					echo "</div>";
				}
				echo '</fieldset>';
				
				// subs
				echo '<fieldset><legend> Subtitles </legend>';
				foreach($infos['subs'] as $track_sub => $sub){
					echo "<input type='checkbox' id='$title-sub_$track_sub' name='t_$title-sub_$track_sub' onClick=\"$('#$title-dsub_$track_sub').slideToggle();\" /><label for='$title-sub_$track_sub' style='font-size:18px;font-weight:bold;color:green;' > Subtitle #$track_sub (".get_human_lang($sub['lang']).", ".$sub['codec'].") </label><br />
					<div id='$title-dsub_$track_sub' style='display:none;' >";
						echo "<input type='checkbox' id='forced_$track_sub' name='forced_$track_sub' /><label for='forced_$track_sub' style='font-size:18px;font-weight:bold;color:green;' > Forced </label><br />";
						echo "<i class='i' >Langage:</i>".get_human_lang($sub['lang']).'<br />';
						echo "<i class='i' >Codec:</i>".$sub['codec'].'<br />';
						echo "<i class='i' >Format:</i>".$sub['format'].'<br />';
						echo "<i class='i' >Encoding:</i>".$sub['encoding'].'<br />';
					echo "</div>";
				}
				echo '</fieldset>';
				
				echo "
				<input type='hidden' name='input' value='".urlencode($input)."' />
				<input type='hidden' name='title' value='$title' />								
				<center><input class='submit' type='submit' value=' Valider --> ' onclick='encode($(\"#form_$title\").serialize());return false;' /></center>
				</form></div>
				";
			}
			?>
			<script type="text/javascript" >
				$('#c_<?php echo $selected; ?>').trigger('click');
			</script>
			<?php
		}else{
			echo "An input file is required !";
		}	
		return;	
		break;
	case 'cli':
		if(isset($_POST['input'])&&isset($_POST['output'])&&isset($_POST['title'])&&isset($_POST['type'])){
			$input=urldecode(urldecode($_POST['input']));
			$output=urldecode(urldecode($_POST['output']));
			if(trim($output)==""){
				echo "You must inform a valid output filename !<br />";
				exit(0);
			}
			$output='/home/thal/disk2/dl/'.urldecode(urldecode($_POST['output']));
			$ats=array(); // audio_tracks
			$t_ats=array(); // target_audio_tracks
			$t_subs=array(); // target_subs
			$subforced=array();
			$id=$_POST['title'];
			$type=$_POST['type'];
			if(is_readable($input)){
				$metas="autopost=0\ntk=gks,ftdb,t411,rz\nimdb=".$_POST['imdb']."\nallocine=".$_POST['allocine']."\nposter=".urlencode($_POST['poster'])."\nsrc=".urlencode($_POST['src'])."\nfile=".urlencode($output);
				$rs=shell_exec('hb -t 0 --scan -i "'.$input.'" 2>&1');
				$tabs=split("\n", $rs);
				$titles=get_hb_infos($tabs);
				unset($tabs);
				$title=$titles[$_POST['title']];
				if(count($title)>0){
					
					/*************************************
					 * 
					 * 			Checking audio tracks ...
					 * 
					 * **************************************/
					 
					foreach($_POST as $k => $v){
						if(preg_match("#^t_$id\-audio_([0-9]{1,2})$#iUs", $k)){
							$tmp=preg_replace("#^t_$id\-audio_([0-9]{1,2})$#iUs", "$1", $k);
							$ats[$tmp]=$title['audio'][$tmp];
						}else if(preg_match("#^t_$id\-sub_([0-9]{1,2})$#iUs", $k)){
							$tmp=preg_replace("#^t_$id\-sub_([0-9]{1,2})$#iUs", "$1", $k);
							$t_subs[$tmp]=$title['subs'][$tmp];
						}else if(preg_match("#^forced_([0-9]{1,2})$#iUs", $k)){
							$tmp=preg_replace("#^forced_([0-9]{1,2})$#iUs", "$1", $k);
							$subforced[]=$tmp; 
						}
					}
					$subforced_cmd="";
					if(count($subforced)>0){
						$tab=split("\n", shell_exec("mkvmerge -i \"$input\""));
						$tmp_video=0;
						$tmp_audio=0;
						$mkv=array();
						$mkv['audio_type']=array();
						$mkv['audio']=array();
						$mkv['subs']=array();
						$mkv['video']=array();
						$tmp_subs=0;
						$tmp=array();
						foreach($tab as $v){
							if(preg_match("#^\s*track\s*id\s*([0-9]{1,2})\s*:\s*subtitles\s*\(s_text/utf8\)\s*$#iUs", $v)){
								$mkv['subs'][++$tmp_subs]=preg_replace("#^\s*track\s*id\s*([0-9]{1,2})\s*:\s*subtitles\s*\(s_text/utf8\)\s*$#iUs", "$1", $v);
							}else if(preg_match("#^\s*track\s*id\s*([0-9]{1,2})\s*:\s*audio\s*\((.+)\)\s*$#iUs", $v)){
								$mkv['audio'][++$tmp_audio]=preg_replace("#^\s*track\s*id\s*([0-9]{1,2})\s*:\s*audio\s*\((.+)\)\s*$#iUs", "$1", $v);
								$mkv['audio_type'][$tmp_audio]=preg_replace("#^\s*track\s*id\s*([0-9]{1,2})\s*:\s*audio\s*\((.+)\)\s*$#iUs", "$2", $v);
							}else if(preg_match("#^\s*track\s*id\s*([0-9]{1,2})\s*:\s*video\s*\((.+)\)\s*$#iUs", $v)){
								$mkv['video'][0]=preg_replace("#^\s*track\s*id\s*([0-9]{1,2})\s*:\s*video\s*\((.+)\)\s*$#iUs", "$1", $v);
							}
						}
						$subforced_cmd.="mkvextract tracks \"$input\"";
						$tmp_cmd="";
						foreach($subforced as $v){
							$subforced_cmd.=" ".$mkv['subs'][$v].":/home/thal/disk2/dl/".$v.".srt  > /home/www/thal/ogmrip/extract.logs && mv /home/www/thal/ogmrip/extract.logs /home/www/thal/ogmrip/_extract.logs";
							$tmp_cmd.=" && /usr/bin/_ffmpeg -y -i /home/thal/disk2/dl/".$v.".srt /home/thal/disk2/dl/".$v.".ass";
							$tmp_cmd.=" && sed -i 's/Style: Default,Arial,16,\&Hffffff,\&Hffffff,\&H0,\&H0,0,0,0,1,1,0,2,10,10,10,0,0/Style: Default,Verdana,20,\&H00FFFFFF,\&H00000000,\&H00000000,\&H00000000,-1,0,0,0,100,100,0,0,1,2,2,2,15,15,15,0/g' /home/thal/disk2/dl/$v.ass && sed -i 's/Format: Name, Fontname, Fontsize, PrimaryColour, SecondaryColour, OutlineColour, BackColour, Bold, Italic, Underline, BorderStyle, Outline, Shadow, Alignment, MarginL, MarginR, MarginV, AlphaLevel, Encoding/Format: Name, Fontname, Fontsize, PrimaryColour, SecondaryColour, OutlineColour, BackColour, Bold, Italic,  Underline, StrikeOut, ScaleX, ScaleY, Spacing, Angle, BorderStyle, Outline, Shadow, Alignment, MarginL, MarginR, MarginV, Encoding/g' /home/thal/disk2/dl/$v.ass";
						}
						$subforced_cmd.=$tmp_cmd;
						$order='';
						$subforced_cmd.=' && mkvmerge -o "/home/thal/disk2/dl/tmp.mkv"  "--language" "'.$mkv['video'][0].':eng" "--track-name" "0:Video encoded by CiRAR team bot v1.3 for subforced movies." "--default-track" "'.$mkv['video'][0].':yes" "--forced-track" "'.$mkv['video'][0].':no"';
						$order.='0:'.$mkv['video'][0];
						foreach($mkv['audio'] as $k => $v){
							$subforced_cmd.=' "--language" "'.$v.':'.convert_lang_mkv($title['audio'][$k]['lang']).'" "--default-track" "'.$v.':no" "--forced-track" "'.$v.':no"'; 
							$order.=',0:'.$v;
						}
						$it=0;
						$tmp_sub=array();
						
						foreach($mkv['subs'] as $k => $v){
							if(!in_array($k, $subforced)){
								$tmp_sub[]=$v;
								$subforced_cmd.=' "--language" "'.$v.':'.convert_lang_mkv($title['subs'][$k]['lang']).'" "--track-name" "'.$v.':sous titres complets" "--default-track" "'.$v.':no" "--forced-track" "'.$v.':no"'; 
								$order.=',0:'.$v;
							}else{
								$order.=','.++$it.':0';
							}
							
						}

						$subforced_cmd.=' "-a" "'.implode(',', $mkv['audio']).'" "-d" "'.$mkv['video'][0].'" '.(count($tmp_sub)>0?'"-s" "'.implode(',', $tmp_sub).'"':'"-S"').' "-T" "--no-global-tags" "(" "'.$input.'" ")"';
						$it=0;
						foreach($subforced as $f){
							$i=0;
							$subforced_cmd.=' "--language" "'.$i.':'.convert_lang_mkv($title['subs'][$f]['lang']).'" "--track-name" "'.$i.':subforced" "--default-track" "'.$i.':no" "--forced-track" "'.$i.':no" "-s" "'.$i.'" "-D" "-A" "-T" "--no-global-tags" "--no-chapters" "(" "/home/thal/disk2/dl/'.$f.'.ass" ")"';
						}
						
						$subforced_cmd.=' "--track-order" "'.$order.'" > /home/www/thal/ogmrip/merge.logs && mv /home/www/thal/ogmrip/merge.logs /home/www/thal/ogmrip/_merge.logs';
						
					}
					if(count($ats)<=0){
						echo "Warning, you must select one audio track at last<br />";
						exit(0);
					}
					
					 /*************************************
					  * 
					  * 		Calculate reso and ar ...
					  *  
					  * **************************************/
					$rtmp=explode('x', $title['video']['reso']);
					$width=intval($rtmp[0]);
					$height=intval($rtmp[1]);  
					$width=$width-intval($title['video']['crop_left'])-intval($title['video']['crop_right']);
					$height=$height-intval($title['video']['crop_top'])-intval($title['video']['crop_bottom']);
					$ar=round_ar($width/$height);				
					if(isset($ars[$ar])){
						$reso=$ars[$ar];
					}else{
						$reso="000x000";
						echo "<center><b style='color:red;' >Warning, original ar not in standard ar, please calculate resolution manually !!!</b></center>";
					}
						echo "<i class='i' >Orgininal ar : </i><b class='b' >$ar</b><br />";
						echo "<i class='i' >Resolution : </i><b class='b' >$reso</b><br />";
						echo "<i class='i' >Duration : </i><b class='b' >".$title['video']['human_duration']."</b><br />";
						
						/************************************
						 *
						 * 			Calculate size and bitrate ... 
						 * 
						 **************************************/
						 
						$duration=$title['video']['duration'];
						$human_duration=$title['video']['human_duration'];
						$size=0;
						
						if($type=='dvdrip_ac3'){
							if($duration<=convert_to_time(1, 59, 59)){
								$size=1405;
							}else{
								$size=2100;
							}
							if(isset($_POST['target_size'])&&intval($_POST['target_size'])>10){
								$size=intval($_POST['target_size']);
							}
							echo "<i class='i' >Target size : </i><b class='b' >".($size/1024)." Go</b><br />";	
							$min_br=1249000;
							$max_br=1850000;
							$abs=array(448000, 384000, 320000, 256000, 224000, 192000);
							foreach($ats as $aid => $ainfos){
								$t_ats[$aid]['terminated']=false;
								$t_ats[$aid]['copy']=false;
							}
							foreach($abs as $ab){
								$total=0;
								foreach($ats as $aid => $ainfos){
									if($t_ats[$aid]['terminated']==false){
										$t_ats[$aid]['ab']=$ab;
										$total+=$ab;
									}
								}
								$tvb=get_vb($human_duration, $total, $size);
								if($tvb>=$min_br){
									// ok, verify if bitrate source is equal or higher source
									foreach($t_ats as $aid => $ac1){
										if($ats[$aid]['bps']>$ac1['ab']){
											//bitrate plus grand, ok
											$t_ats[$aid]['terminated']=true;
											$t_ats[$aid]['copy']=false;
										}else if($ats[$aid]['bps']==$ac1['ab']){
											// source bitrate is equals with target bitrate
											$t_ats[$aid]['terminated']=true;
											if(strtolower($ats[$aid]['codec'])=='ac3'){
												$t_ats[$aid]['copy']=true;
											}else{
												$t_ats[$aid]['copy']=false;
											}
										}else{
											// the source bitrate is lower than the targte bitrate
											// next loop ...
										}
									}
								}else{
									// next ...
								}
							}
							// audio codec
							$acodec="ffac3"; // ffac3
							$tacodec="ac3";
							// audio channels
							$atmp=0;
							foreach($ats as $aid => $ainfos){ 
								$sch=$ainfos['ch'];
								$atmp+=$t_ats[$aid]['ab'];
								if($t_ats[$aid]['copy']==true){
									$ch="auto";
								}else{
									if($sch=="5.1 ch"){
										if($t_ats[$aid]['ab']>=384){
											$ch="6ch";
										}else{
											$ch="dpl2";
										}
									}else{
										$ch="dpl2";
									}
								}
								$t_ats[$aid]['ch']=$ch;
							}
							$vb=get_vb($human_duration, $atmp, $size);
						}else if($type=='dvdrip_mp3'){
							if($duration<=convert_to_time(1, 59, 59)){
								$size=702;
							}else{
								$size=1400;
							}
							if(isset($_POST['target_size'])&&intval($_POST['target_size'])>10){
								$size=intval($_POST['target_size']);
							}
							echo "<i class='i' >Target size : </i><b class='b' >".($size/1024)." Mo</b><br />";	
							$min_br=780000;
							$max_br=1300000;
							$abs=array(128000);
							foreach($ats as $aid => $ainfos){
								$t_ats[$aid]['terminated']=false;
								$t_ats[$aid]['copy']=false;
							}
							foreach($abs as $ab){
								$total=0;
								foreach($ats as $aid => $ainfos){
									if($t_ats[$aid]['terminated']==false){
										$t_ats[$aid]['ab']=$ab;
										$total+=$ab;
									}
								}
								$tvb=get_vb($human_duration, $total, $size);
								if($tvb>=$min_br){
									// ok, verify if bitrate source is equal or higher source
									foreach($t_ats as $aid => $ac1){
										if($ats[$aid]['bps']>$ac1['ab']){
											//bitrate plus grand, ok
											$t_ats[$aid]['terminated']=true;
											$t_ats[$aid]['copy']=false;
										}else if($ats[$aid]['bps']==$ac1['ab']){
											// source bitrate is equals with target bitrate
											$t_ats[$aid]['terminated']=true;
											if(strtolower($ats[$aid]['codec'])=='mp3'){
												$t_ats[$aid]['copy']=true;
											}else{
												$t_ats[$aid]['copy']=false;
											}
										}else{
											// the source bitrate is lower than the targte bitrate
											// next loop ...
										}
									}
								}else{
									// next ...
								}
							}
							// audio codec
							$acodec="mp3";
							$tacodec="mp3";
							// audio channels
							$atmp=0;
							foreach($ats as $aid => $ainfos){ 
								$sch=$ainfos['ch'];
								if($t_ats[$aid]['copy']==true){
									$ch="auto";
								}else{
									$ch="stereo";
								}
								$t_ats[$aid]['ch']=$ch;
								$atmp+=$t_ats[$aid]['ab'];
							}
							$vb=get_vb($human_duration, $atmp, $size);
						}else{
							echo "<b style='color:red;' >Target type not valid ...</b><br />";
							exit(0);
						}
						$tmp=0;
						
						$tmp_audio_id=" -a ";
						$tmp_audio_lang=" -A ";
						$tmp_audio_codec=" -E ";
						$tmp_audio_bitrate=" -B ";
						$tmp_audio_ch=" -6 ";
						
						if(count($t_subs)>0){
							$part_subs=" -s ";
							foreach($t_subs as $sid => $sinfos){
								$part_subs.=$sid.",";
							}
							$part_subs=substr($part_subs, 0, -1);
						}else{
							$part_subs="";
						}
						echo '<br /><b> Audio configuration ... </b><br />';
						
						foreach($t_ats as $aid => $ainfos){
							$tmp_audio_id.=$aid.",";
							$tmp_audio_lang.=get_human_lang($ats[$aid]['lang']).",";
							$tmp_audio_codec.=($ainfos['copy']==true?"copy:$tacodec":$acodec).",";
							$tmp_audio_bitrate.=($ainfos['copy']==true?"auto":($ainfos['ab']/1000)).",";
							$tmp_audio_ch.=$ainfos['ch'].",";
							$tmp+=$ainfos['ab'];
							echo "Track $aid, use codec <b class='b' >$acodec</b> with bitrate <b class='b' >".($ainfos['ab']/1000)." Kbps</b> (original = <b style='color:blue' >".($ats[$aid]['bps']/1000)." Kbps</b>) and ch = ".$ainfos['ch']."</b><br />";
						}
						
						$tmp_audio_id=substr($tmp_audio_id, 0, -1);
						$tmp_audio_lang=substr($tmp_audio_lang, 0, -1);
						$tmp_audio_codec=substr($tmp_audio_codec, 0, -1);
						$tmp_audio_bitrate=substr($tmp_audio_bitrate, 0, -1);
						$tmp_audio_ch=substr($tmp_audio_ch, 0, -1);
						$part_audio=$tmp_audio_id.$tmp_audio_lang.$tmp_audio_codec.$tmp_audio_bitrate.$tmp_audio_ch;
						
						
						
						echo '<br /><b> Video configuration ... </b><br />';
						echo 'Use video bitrate <b class="b" >'.round(($vb/1000), 2).' Kbps</b><br />';
						
						echo '<br /><b> Subtitle configuration ... </b><br />';
						foreach($t_subs as $sid => $sinfos){
							echo 'Use subtitle track <b class="b" >'.$sid.' ('.get_human_lang($sinfos['lang']).', '.$sinfos['codec'].')</b><br />';
						}
						$tmp1=explode('x', $reso);
						$width=$tmp1[0];
						$height=$tmp1[1];
						$vb=round($vb/1000, 2);
						if($vb<1450){
							$width=640;
						}else{
							$width=720;
						}
						$end="";
						if(count($subforced)>0){
							$part_subs.=" --subtitle-burn ".implode(',', $subforced);
							$t_input="/home/thal/disk2/dl/tmp.mkv";
						}else{
							$t_input=$input;
						}
$funky="cabac=1:ref=5:deblock=1,-1,-1:analyse=0x3:0x113:mbtree=1:direct=3:me=umh:subme=8:psy=1:rc_lookahead=50:deadzone=21.11:qcomp=0.60:qmin=0:qstep=4:qmax=69:me_range=16:fast_skip=1:decimate=1:ratetol=1.0:trellis=1:mixed_ref=1:chroma_me=1";
$cmd="hb -i \"$t_input\" -t $id -e x264 --vb $vb --width $width --keep-display-aspect --modulus 16 $part_audio $part_subs -x \"$funky\" --two-pass --turbo -o \"$output\"";
						$cmd=str_replace('  ', ' ', $cmd);
						if(isset($subforced_cmd)&&trim($subforced_cmd)!=""){
							$subforced_cmd.=" && ";
						}else{
							$subforced_cmd="";
						}
						echo "<br />Command line : <textarea name='hb_cli' id='hb_cli' style='height:100px;' >".$subforced_cmd.$cmd."</textarea>";
						echo "<br />Metadatas : <textarea name='_metas' id='_metas' style='height:110px;width:95%;' >$metas</textarea>";
						echo "<center><button class='submit' onclick='add_to_queue($(\"#hb_cli\").val(), $(\"#_metas\").val());' > Add to queue --> </button></center>";
					
				}else{
					echo 'Internal error #hb0001<br />';
				}
			}else{
				echo 'Input file not found or not readeable.<br />';
			}
		}else{
			echo 'Essentials arguments arent required.<br />';
		}
		return;
		break;
	default:
		echo 'A valid argument is required<br />';
		return;
	}
	exit(0);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> CiRAR Team Encoding Bot</title>
<script type="text/javascript" src="./jquery.min.js" ></script>
<style>
.i
{
	font-weight: bold;
	color:blue;
	margin-right: 15px;
}
.b
{
	color:red;
}
.submit
{
	background-color:yellow;
	color:red;
	font-weight:bold;
	font-size:20px;
	border: 2px outset blue;
	border-radius: 5px;
}
.submit:hover
{
	border: 2px inset red;
}
#hb_cli
{
	width: 95%;
	height: 30px;
	
}
fieldset
{
	width:60%;
}
</style>
</head>
<body>
	
<?php
echo '<div style="text-align:center;position:absolute;margin-bottom:10px;font-weight:bold;" ><a style="color:blue;" href="./view_queue.php" title="View queue" > View queue </a> | <a style="color:red;" onClick=\'if(confirm("Do you want really clean this queue ?")){return true;}else{return false;}\'  a href="empty_queue.php" > Empty queue </a> | <a href="./dvdrip.php" title="New rip" style="color:green;"> New encode </a> | <a href="check_queue.log" style="color:gray" >View daemon logs</a></div><br /><br /><br />';



if(isset($_POST['input'])&&isset($_POST['output'])&&isset($_POST['lang'])&&isset($_POST['type'])){
	
	print_r($_POST);
	
	$audio_copy=false;
	$target_type=$_POST['type'];
	$target_lang=$_POST['lang'];
	$input='/home/thal/disk2/dl/'.$_POST['input'];
	$output='/home/thal/disk2/dl/'.$_POST['output'];
	
	
	/**********************************************************************
	 * 
	 * 				Preparing handbrake command line ...
	 * 
	 **********************************************************************/
	// selecting tracks ...

	
	$target_subs="";
	if(count($subs)>0&&$target_lang!="fr"){
		foreach($subs as $id => $i){
			if(strtoupper($i['format'])=='TEXT'||strtoupper($i['format'])=='SSA'){
				$target_subs.=$id.",";
			}
		}
	}
	if($target_subs==""){
		echo "<b style='color:color;' >Warning, no subtitles ...</b><br />";
		$target_subs=null;
	}else{
		$target_subs=substr($target_subs, 0, -1);
		echo "<b style='color:green' >Using all text/ssa subtitles tracks ...</b><br />";
	}
	echo "<b style='color:green' >Using video track no <u style='color:red;' >$selected_title</u></b><br />";
	echo "<b style='color:green' >Duration <u style='color:red;' >".strftime("%Hh %M:%S", $duration-(60*60))."</u></b><br />";

	foreach($audio as $id => $i){
		if($i['lang']==$target_lang){
			$target_audio=$id;
			echo "<b style='color:green' >Using video track no <u style='color:red;' >$id</u> (<u style='color:red;' >".$i['lang']."</u>) </b><br />";
			break;
		}
	}
	if(!isset($target_audio)||intval($target_audio)<=0){
		echo "<b style='color:color;' >Error, no audio track found for langage \"$target_lang\" ...</b><br />";
		exit(0);
	}

	
	foreach($abs as $b){
		$ab=$b;
		$vb=get_vb($human_duration, $ab, $target_size);
		if($vb<=$min_br){
			if($b<=$abs[count($abs)-1]){
				// br too low
				echo "<b style='color:red' >Warning, problem for found a good bitrate, $vb bps of video bitrate (audio = $b) seems too low quality ...</b><br />";
				exit(0);
			}
			// trying test next br ...
		}else if($vb>$max_br){
			echo "<b style='color:red' >Warning, problem for found a good bitrate, $vb bps of video bitrate (audio = $b) seems too high quality ...</b><br />";
			exit(0);
		}else{
			
			if(intval($audio[$target_audio]['bps'])>intval($ab)){
				// ok, br accepted
				echo "<b style='color:green' >Use <b style='color:red;' >".round($vb/1000, 2)."</b> Kbps as video bitrate and <b style='color:red;' >".round($ab/1000, 2)."</b> Kbps as audio bitrate ...</b><br />";
				break;
			}else if($audio[$target_audio]['bps']==$ab){
				// ok, mode audio_copy
				$audio_copy=true;
				echo "<b style='color:green' >Use <b style='color:red;' >".round($vb/1000, 2)."</b> Kbps as video bitrate and active mode <b style='color:red;' >audio_copy</b> ...</b><br />";
				break;
			}else{
				if($ab==$abs[count($abs)-1]){
					// br too low
					echo "<b style='color:red' >#r0006, Warning: problem for found a good audio bitrate (last selected is $ab), because source is ".$audio[$target_audio]['bps']." ...</b><br />";
					exit(0);
				}
			}
		}
	}
	$ar=round_ar($ar);
	if($ars[$ar]!=null&&trim($ars[$ar])!=""){
		$selected_reso=$ars[$ar];
		echo "<b style='color:green' >The resolution <u style='color:red' >$selected_reso</u> has been automaticly selected (original ar = <u style='color:red' >$ar</u>) ...</b><br />\n";
		$tmp=split('x', $selected_reso);
		$width=$tmp[0];
		$height=$tmp[1];
	}else{
		echo "<b style='color:red' >Warning, the ar '$ar' is not in standards ar !</b><br />\n";
	}

	
	$vb=round($vb/1000, 2);
	$ab=round($ab/1000, 2);
	
	$cmd="hb -t $selected_title -i \"$input\" -e x264 --x264-preset veryslow --vb $vb --width $width --height $height -a $target_audio -A ".get_human_lang($audio[$target_audio]['lang'])." -B ".($audio_copy?'auto':$ab)." -E ".($audio_copy?"copy:ac3":$acodec)." -R ".($audio_copy?'auto':"48")." -6 $ch ".($target_subs!=null?"-s $target_subs ":"")." -o \"$output\"";
	echo "<br /><b style='color:blue;' >$cmd</b><br />";
	echo '<form method="post" >
	<input type="hidden" value="'.urlencode($cmd).'" name="cmd" /><br />
	<input type="submit" style="color:red;background-color:yellow;font-weight:bold;" value=" Add to queue --> " />
	</form>';
}else{
	if(isset($_POST['cmd'])){
		$token=md5(uniqid(rand(), true));
		$f=fopen('/home/www/thal/ogmrip/queue.txt', 'a+');
		fwrite($f, "&[waiting][$token]".$_POST['cmd'].urlencode(" > /home/www/thal/ogmrip/logs/$token.log"));
		fclose($f);
		echo '<center style="color:green;font-weight:bold;" > Encode add to queue successfully - The queue will be automaticly started in 60 sec about ... </center>';
	}
	?>
	<script type="text/javascript" >
	$(document).ready(function(){
		$('#step1').click(function(){
			var input=encodeURIComponent($("#input").val());
			$('#main').html('<center><img src="./loading.gif" /></center>');
			$.ajax({
				url: "./dvdrip.php",
				type: "POST",
				data: "get=form&input="+input,
				success: function(msg){
					$('#main').html(msg);
				}
			});
			return false;
		});
	});
	function encode(datas){
		var tmp = "get=cli&src="+encodeURIComponent($('#rlz_src').val())+"&imdb="+encodeURIComponent($('#imdb').val())+"&allocine="+encodeURIComponent($('#allocine').val())+"&poster="+encodeURIComponent($('#poster').val())+"&output="+encodeURIComponent($('#output').val())+"&type="+$('#type option:selected').val()+'&'+datas;
		$('#main').html('<center><img src="./loading.gif" /></center>');
		$.ajax({
			url: "./dvdrip.php",
			type: "POST",
			data: tmp,
			success: function(msg){
				$('#main').html(msg);
			}
		});
		return false;
	}
	function add_to_queue(datas, metas){
		$('#main').html('<center><img src="./loading.gif" /></center>');
		$.ajax({
			url: "./dvdrip.php",
			type: "POST",
			data: "get=add&cmd="+encodeURIComponent(datas)+"&metas="+encodeURIComponent(metas),
			success: function(msg){
				window.location='./view_queue.php';
			}
		});
		return false;
	}
	</script>
	<div id="main" >
	<form method='post' >
	<input required size=150 placeholder='Source file path ...' type='text' id="input" name='input' /><br />
	<input type='submit' value='Valider' id='step1'/>
	</form>
	</div>
	<?php
}
?>
</body>
</html>
