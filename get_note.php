<?php
$tmp=trim($html1->find('.starbar-meta a', 0)->plaintext);
if($tmp!=""){
	$note=trim($html1->find('.starbar-meta b', 0)->plaintext).' sur '.$tmp;
}else{
	$note='Nothing imdb note ...';
}
wr($note);
