<?php
$tmp=$html1->find('.cast', 0);
$i=0;
if($tmp!=null){
	foreach($tmp->find('.odd') as $item){
		$actors[trim($item->find('.nm a', 0)->plaintext)]=trim($item->find('.char', 0)->plaintext);
		if(++$i>=20){
			break;
		}
	}
}
print_r($actors);
