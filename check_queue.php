<?php
include('/vars.php');
$f1=fopen($dir_this.'check_queue.log', 'a+');
fwrite($f1, strftime("%Y %B %d - %H:%M:%S", time())."\n");
fclose($f1);

$q=file_get_contents($dir_this.'queue.txt');
if(strpos($q, "&")!==false){
	$tab=split('&', $q);
}else{
	$tab=array(1 => $q);
}
unset($tab[0]);
$run=false;
foreach($tab as $cmd){
	$run=false;
	$state=strtoupper(substr($cmd, 1,7));
	if($state=="RUNNING"){
		$run=true;
		break;
	}
}
if($run){
	//print("An encode is running yet ...\n");
}else{
	//print("Nothing encode is running and ".count($tab)." encodes are waiting...\n");
	foreach($tab as $k => $cmd){
		$state=strtoupper(substr($cmd, 1,7));
		$logs=substr($cmd, 10,32);
		
		if($state=="WAITING"){
			$q=str_replace('[waiting]['.$logs.']', '[running]['.$logs.']', $q);
			$f=fopen($dir_this.'queue.txt', 'w+');
			fwrite($f, $q);
			fclose($f);
			
			$f=fopen($dir_this.'run_hb.sh', 'w+');
			fwrite($f, "#!/bin/bash\n\n".urldecode(substr($cmd, 43))."\nphp -f ".$dir_this."terminate_encode.php \"$logs\"");
			fclose($f);
			
			echo "go";
			return;
			break;
		}
	}
}
