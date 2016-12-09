<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> CiRAR Team Encoding Bot</title>

<script type="text/javascript" src="./jquery.min.js" ></script>
<link rel="stylesheet" type="text/css" media="all" href="./modalbox/css/jquery.modalbox.css" />
<script type="text/javascript" src="./modalbox/js/jquery_modalbox_js_min.js"></script>

<style type="text/css">
<!--
@import url("view_queue.css");
-->
</style>

</head>
<body>
	
<?php
include_once('vars.php');
echo '<div style="text-align:center;position:absolute;margin-bottom:10px;font-weight:bold;" ><a style="color:blue;" href="./view_queue.php" title="View queue" > View queue </a> | <a style="color:red;" onClick=\'if(confirm("Do you want really clean this queue ?")){return true;}else{return false;}\'  a href="empty_queue.php" > Empty queue </a> | <a href="./rip.php" title="New rip" style="color:green;"> New encode </a> | <a href="check_queue.log" style="color:gray" >View daemon logs</a></div><br /><br /><br />';

$q=file_get_contents('queue.txt');
if(strpos($q, "&")!==false){
	$tab=explode('&', $q);
}else{
	$tab=array(1 => $q);
}

echo "<table id='hor-minimalist-b' ><thead><tr><th> State </th><th style='text-align:center;'> File </th><th style='width:12%;' > Logs </th><th>  Next </th><th> - </th></tr></thead><tbody>";

foreach($tab as $cmd){
	if(trim($cmd)!=""){
		$run=false;
		$state=strtoupper(substr($cmd, 1,7));
		if($state!="_ERASED"){
			if($state=="WAITING"){
				$state="<span style='color:gray;' >Waiting</span>";
			}else if($state=="RUNNING"){
				$state="<span style='color:red;font-weight:bold;' >Running</span>";
				$run=true;
			}else{
				$state="<span style='color:green;' >Finish</span>";
			}
			$logs=substr($cmd, 10,32);
			$cmd=urldecode(urldecode(substr($cmd, 43)));
			if(preg_match("#hb[0-9]?(_core[0-9]{3})?#iUs", $cmd)){
				$name=preg_replace("#^(.+)(\"|\-\-turbo|\-\-two-pass) -o \"(.+)\" >(.+)?$#iUs", "$3", $cmd);
				$name=str_replace($dir_target, '', $name);
			}else if(preg_match("#ffmpeg \-y \-vstats_file#iUs", $cmd)){
				$name=str_replace("\"", "", preg_replace("#^(.+)\-pass\s*2\s*\-passlogfile\s*pass1\.log\s*(.+)?\s*>(.+)?$#iUs", "$2", $cmd));
				$name=str_replace($dir_target, '', $name);
			}else if(preg_match("#ffmpeg(.+)\-crf#iUs", $cmd)){
				$name=str_replace("\"", "", preg_replace("#^(.+)\-threads [0-9]{1,2}\s*\"(.+)\"(.+)$#iUs", "$2", $cmd));
				$name=str_replace($dir_target, '', $name);
			}else{
				$name=$cmd;
			}
			if($run){
				if(is_readable($dir_logs.$logs.'.log')){
					$f2=fopen($dir_logs.$logs.'.log', 'r');
					$prec=null;
					while($l=fgets($f2)){
						$prec=$l;
					}
					$tmp=preg_split("#.+([0-9]{1,2}.[0-9]{2}) %.+#iUs", $prec, -1, PREG_SPLIT_DELIM_CAPTURE);
					$p=floatval($tmp[count($tmp)-2]);
				}
			}
			echo "<tr><td ".($run==true?"style='border-bottom:none;' ":"")." >$state</td><td style='".($run==true?"border-bottom:none;":"")."text-align:center;width:78%;' >$name</td>
			<td ".($run==true?"style='border-bottom:none;' ":"")." >".(is_readable($dir_logs.$logs.'.log')?'<a class="openmodalbox large" href="javascript:void(0);"> View logs	<input type="hidden" name="ajaxhref" value="./logs/'.$logs.'.log" /></a>':'No logs ...')."</td>";
			echo "<td style='width:100px;' ".($run==true?"style='border-bottom:none;' ":"")." >".(is_readable($dir_metas.$logs.'.cirar')?'<a style="color:red;" class="openmodalbox large" href="javascript:void(0);"> Next <input type="hidden" name="ajaxhref" value="./prepare_publish.php?metas='.$logs.'" /></a>':'No data')."</td>";
			echo "<td><a style='font-weight:bold;text-decoration:none;' href='remove_from_queue.php?id=$logs' > - </a></td>";
			if($run){
				?>
				<script type="text/javascript" >
					$(document).ready(function(){
						window.location.href='#last';
						$str=$('#str_<?php echo $logs; ?>');
						$bar=$('#bar_<?php echo $logs; ?>');
						refresh();
						function refresh(){
							$.ajax({
								url:'refresh_bar_queue.php',
								type:'GET',
								data:"id=<?php echo $logs; ?>",
								success: function(msg){
									if(msg==0){
										$str.html("Warning #w0009 - Waiting for logs ...");
										$bar.css('width', parseFloat(100)+"%");
										$bar.css('opacity', 0.5);
									}else{
										$bar.css('opacity', 1);
										tmp=msg.split('&');
										if(tmp[0]==0){
	/*
											location.reload(true);
	*/
										}else if(tmp[0]=='extract'){
											$str.html('Extracting : <b style="color:red;" >'+tmp[1]+' % </b>');
											$bar.css('width', parseFloat(tmp[1])+"%");
										}else if(tmp[0]=='merge'){
											$str.html('Merging : <b style="color:red;" >'+tmp[1]+' % </b>');
											$bar.css('width', parseFloat(tmp[1])+"%");
										}else{
											$str.html('<b style="color:red;" >Pass '+tmp[2]+'</b> - '+tmp[0]+' % - '+tmp[1]+' - <b style="color:red;" >'+tmp[3]+' FPS</b>');
											$bar.css('width', parseFloat(tmp[0])+"%");
										}
									}
									setTimeout(function(){refresh();}, 1000);
								}
							});
						}
					});
	</script>
				<?php
				echo "<tr id='last' style='height:25px;margin:0px;padding:0px;' ><td colspan=5 style='width:100%;background-color:white;margin:0px;padding:0px;' ><div id='bar_$logs' style='margin:0px;margin-top:-7px;padding:0px;color:white;font-weight:bold;z-index:150;background-color:white;background-image:url(progress.png);width:0%;text-align:center;height:25px;' ><span style='position:absolute;color:blue;font-weight:bold' id='str_$logs' >0%</span></div></td></tr>";
				$run=false;
			}
			echo "</tr>";
		}
	}
}
echo "</tbody></table>";


echo "</body></html>";
