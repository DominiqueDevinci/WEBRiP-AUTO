<?php
include_once('/vars.php');
function getline( $fp, $delim )
{
    $result = "";
    while( !feof( $fp ) )
    {
        $tmp = fgetc( $fp );
        if( $tmp == $delim )
            return $result;
        $result .= $tmp;
    }
    return $result;
}
function get_duration($s){
	// retourne en centième de seconde
	$tmp=preg_split("#([0-9]{2}):([0-9]{2}):([0-9]{2}).([0-9]{2})#iUs", $s, -1, PREG_SPLIT_DELIM_CAPTURE);
	if(count($tmp)>1){
		return intval(($tmp[1]*60*60*60)+($tmp[2]*60*60)+($tmp[3]*60)+($tmp[4]));
	}
}
if(is_readable($dir_logs.$_GET['id'].'.log')){
	
	$f2=fopen($dir_logs.$_GET['id'].'.log', 'r+');
	$prec=null;
	while($l=fgets($f2)){
		$prec=$l;
	}
	$tmp=preg_split("#.+task\s*([0-9])\s*of\s*[0-9].+\(([0-9]{1,2}\.[0-9]{1,2})\sfps.+\s([0-9]{1,2}.[0-9]{2}) %.+ETA ([0-9]{2}h[0-9]{2}m[0-9]{2}s)\)$#", $prec, -1, PREG_SPLIT_DELIM_CAPTURE);
	$p=floatval($tmp[3]);
	echo $p."&".str_replace('h', 'h ', substr($tmp[4], 0, -3)).'&'.intval($tmp[1]).'&'.$tmp[2];
	return;
}else if(is_readable($dir_logs.$_GET['id'].'.pass2b.logs')){
	$duration=preg_split("#duration:\s*([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2})#iUs", file_get_contents($dir_logs.$_GET['id'].'.pass2b.logs'), -1, PREG_SPLIT_DELIM_CAPTURE);
	$duration=get_duration($duration[1]);
	$fp = fopen($dir_logs.$_GET['id'].'.pass2b.logs', 'r');
	$prec=null;
	$str=null;
	while( !feof($fp) )
	{
		$prec=$str;
		$str = getline($fp, "\r"); // because logs's end lines are in LF format (MAC)
	}
	fclose($fp);
	$tmp=preg_split("#fps=\s*([0-9]{1,3}(\.[0-9]{1,2})?)\s.+time=([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}).+bitrate=.+#i", $prec, -1, PREG_SPLIT_DELIM_CAPTURE);
	//$p=floatval($tmp[3]);

	echo round(((intval(get_duration($tmp[3]))/intval($duration))*100), 2)."& &2&".$tmp[1];
}else if(is_readable($dir_logs.$_GET['id'].'.pass1b.logs')){
	$duration=preg_split("#duration:\s*([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2})#iUs", file_get_contents($dir_logs.$_GET['id'].'.pass1b.logs'), -1, PREG_SPLIT_DELIM_CAPTURE);
	$duration=get_duration($duration[1]);
	$fp = fopen($dir_logs.$_GET['id'].'.pass1b.logs', 'r');
	$prec=null;
	$str=null;
	while( !feof($fp) )
	{
		$prec=$str;
		$str = getline($fp, "\r"); // because logs's end lines are in LF format (MAC)
	}
	fclose($fp);
	$tmp=preg_split("#fps=\s*([0-9]{1,3}(\.[0-9]{1,2})?)\s.+time=([0-9]{2}:[0-9]{2}:[0-9]{2}.[0-9]{2}).+bitrate=.+#iUs", $prec, -1, PREG_SPLIT_DELIM_CAPTURE);
	//$p=floatval($tmp[3]);
	echo round(((intval(get_duration($tmp[3]))/intval($duration))*100), 2)."& &1&".$tmp[1];
}else if(is_readable('extract.logs')){
		// extracting ...
		$fp = fopen("extract.logs", 'r');
		$prec=null;
		$str=null;
		while( !feof($fp) )
		{
			$prec=$str;
			$str = getline($fp, "\r"); // because logs's end lines are in LF format (MAC)
		}
		fclose($fp);
		$tab=preg_split("#progress: ([0-9]{1,3})%#iUs", $prec, -1, PREG_SPLIT_DELIM_CAPTURE);
		echo "extract&".$tab[1];
		return;
}else if(is_readable('merge.logs')){
		// merging ...
		$fp = fopen("merge.logs", 'r');
		$prec=null;
		$str=null;
		while( !feof($fp) )
		{
			$prec=$str;
			$str = getline($fp, "\r"); // because logs's end lines are in LF format (MAC)
		}
		fclose($fp);
		$tab=preg_split("#progress: ([0-9]{1,3})%#iUs", $prec, -1, PREG_SPLIT_DELIM_CAPTURE);
		echo "merge&".$tab[1];
		return;
}else{
	echo 0;
}
