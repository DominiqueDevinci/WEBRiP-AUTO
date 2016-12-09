<?php
$tmp=$html1->find('#tn15lhs a[name="poster"]', 0)->href;
$tmp2=file_get_contents('http://www.imdb.com'.$tmp);
if($tmp2!=false&&$tmp!=null){
	$html2=new simple_html_dom();
	$html2->load($tmp2);
	unset($tmp2);
	$poster=$html2->find('#primary-img', 0)->src;
}else{
	$poster='http://i.media-imdb.com/images/SFc8478396c56922f834968dac94a405f7/intl/fr/title_addposter.jpg';
}
unset($tmp);
