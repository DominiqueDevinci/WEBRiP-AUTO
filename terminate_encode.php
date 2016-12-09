<?php
include('/vars.php');
if(isset($argv[1])){
	$q=file_get_contents($dir_this.'queue.txt');
	$q=str_replace('[running]['.$argv[1].']', '[termina]['.$argv[1].']', $q);
	$f=fopen($dir_this.'/queue.txt', 'w+');
	fwrite($f, $q);
	fclose($f);
}
