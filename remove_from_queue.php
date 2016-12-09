<?php
if(isset($_GET['id'])){
	$q=file_get_contents('queue.txt');
	$q=str_replace('[running]['.$_GET['id'].']', '[_erased]['.$_GET['id'].']', $q);
	$q=str_replace('[waiting]['.$_GET['id'].']', '[_erased]['.$_GET['id'].']', $q);
	$q=str_replace('[termina]['.$_GET['id'].']', '[_erased]['.$_GET['id'].']', $q);
	$f=fopen('queue.txt', 'w+');
	fwrite($f, $q);
	fclose($f);
	header('Location: view_queue.php');
}else{
	echo "Sorry, you must specified all params.";
}
