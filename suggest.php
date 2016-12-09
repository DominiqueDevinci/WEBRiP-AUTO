<?php
include('./functions.php');
if(isset($_GET['type'])){
	switch($_GET['type']){
		case 'imdb':
			echo get_id_imdb(urldecode($_GET['title']), false);
			break;
		case 'allocine':
			echo get_id_allocine(urldecode($_GET['title']), true);	
			break;
		default:
			echo "The argument 'type' is not valid !";
			exit(0);
			break;
	}
}else{
	echo "Argument 'type' required ...";
	exit(0);
}
