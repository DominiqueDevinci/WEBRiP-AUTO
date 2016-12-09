<?php
include_once('/vars.php');
require_once('dom.php');
$_GET['metas']=$dir_metas.$_GET['metas'].'.cirar';
if(isset($_GET['metas'])&&is_readable($_GET['metas'])){
	$metas=file_get_contents($_GET['metas']);
	$metas=explode("\n", $metas);
	$tab=array();
	foreach($metas as $tmp){
		$tmp2=explode("=", $tmp);
		if(isset($tmp2[1])){
			$tab[trim($tmp2[0])]=trim(urldecode($tmp2[1]));
		}
	}
	if(isset($tab['autopost'])&&$tab['autopost']==1){
		echo '<span style="color:red;" >Warning, autopost is not supported yet !</span><br />';
	}
	if(isset($tab['file'])&&is_readable($tab['file'])){
		echo 'mtn "'.$tab['file'].'" -f /usr/share/fonts/truetype/ttf-dejavu/DejaVuSerif.ttf -T "Encoded and proudly presents by CiRAR" -c 1 -r 5 -D 12 -j 96 -k FFFFFF -F 161616:10 -O '.$dir_public;
		shell_exec('mtn "'.$tab['file'].'" -f /usr/share/fonts/truetype/ttf-dejavu/DejaVuSerif.ttf -T "Encoded and proudly presents by CiRAR" -c 1 -r 5 -D 12 -j 96 -k FFFFFF -F 161616:10 -O '.$dir_public.' > '.$dir_logs.'mtn.log');
		$tmp=explode('.', $tab['file']);
		$thumbs=$http_public.basename($tab['file'], '.'.$tmp[count($tmp)-1]).'_s.jpg';
		echo '<h2> Mediainfo : </h2><div id="mediainfo" style="height:400px;overflow:scroll;" >'.nl2br(shell_exec('mediainfo "'.$tab['file'].'"')).'</div>';
		echo '<br /><a href="javascript:$(\'#thumbs\').slideToggle();"  > View thumbs ... </a><img onMouseOver="$(this).css(\'opacity\', 1);" onMouseOut="$(this).css(\'opacity\', 0.2);" id="thumbs" style="display:none;width:50%;float:right;opacity:0.2;" src="'.$thumbs.'" />';								
		echo '<br />IMDB : <a href="http://www.imdb.fr/title/tt'.$tab['imdb'].'/" target="_blank" >'.$tab['imdb'].'</a> (<input type="text" id="imdb" value="'.$tab['imdb'].'" />)<br />';
		echo 'Allociné : <a href="http://www.allocine.fr/film/fichefilm_gen_cfilm='.$tab['allocine'].'.html" target="_blank" >'.$tab['allocine'].'</a> (<input type="text" id="allocine" value="'.$tab['allocine'].'" />)<br />';
		echo ($tab['poster']!="0"?'Customize poster = <a href="'.$tab['poster'].'" target="_blank" >'.$tab['poster'].'</a><br />':'Default poster ...<br />');
		echo 'Trackers : <b><input id="tks" type="text" value="gks,ftdb,pdn,bt,ut,rz,t411" /></b><br />';
		echo 'Source : <b><input id="rlz_src" type="text" value="'.$tab['src'].'" /></b><br />';
		echo '<label>Publier avec le profil de : </label><select id="profil" >
		<option value="thales0796" >Thales0796</option>
		</select><br />
		<input id="submit" type="button" value=" Publier " /><br />
		<div id="infos" ></div>
		';
		?>
		<script type="text/javascript" >
		$(document).ready(function(){
			$('#submit').click(function(){
				var end = false;
				$('#submit').attr('disabled', 'disabled');
				var cmd = '<?php echo $dir_this; ?>bot.sh "<?php echo addslashes($tab['file']); ?>" '+$('#imdb').val()+' '+$('#allocine').val()+' '+$('#tks').val()+' "'+$('#rlz_src').val()+'" "<?php echo urlencode($tab['poster']); ?>" '+$('#profil option:selected').attr('value')+"> <?php echo $dir_logs; ?>up.logs 2><?php echo $dir_logs; ?>up.logs2 3><?php echo $dir_logs; ?>up.log3";
				$('#infos').html('<center style="font-weight:bold;color:green;" >'+cmd+'</center>');
				$('#infos').append('<center>Uploading ...<br /><img src="./loading.gif" alt="loading" /></center>');
				$('#submit').before('<div style="width:100%;background-color:white;margin:0px;padding:0px;"><div id="bar_create" style="margin: -7px 0px 0px; padding: 0px; color: white; font-weight: bold; z-index: 150; background-color: white; background-image: url(./progress.png); width: 0%; text-align: center; height: 25px; opacity: 1;"><span style="position:absolute;color:blue;font-weight:bold" id="str_create"></span></div></div>');
				$.ajax({
					type: "POST",
					url: "execute.php",
					data: "command="+encodeURIComponent(cmd),
					success: function(msg){
						end=true;
						alert('Upload terminé ! -> '+msg);
					}
				});
				var bar=$('#bar_create');
				var str=$('#str_create');
				refresh_create_bar();
				function refresh_create_bar(){
					$.ajax({
						type: "GET",
						url: "buildtorrent_progress.php",
						success: function(msg){
							bar.css('width', msg+'%');
							str.html('<b style="color:red;" >'+msg+'%</b>');
							if(end==false){
								setTimeout(function(){refresh_create_bar();}, 500);
							}
						}
					});
				}
			});
			
		});
		</script>
		<?php
	}else{
		echo '<span style="color:red;" >Warning, cannot found or read the file "'.$tab['file'].'" !</span><br />';
	}
}else{
	echo "Cannot read metas ... arg specified = '".$_GET['metas']."'<br />";
}
?>

