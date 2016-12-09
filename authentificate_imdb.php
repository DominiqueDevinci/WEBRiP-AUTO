<?php
// IMDB a cru ingénieux de sécurisé son sytème de connexion
// mais rien n'est impossible :lol:

/*
 * Marche à suivre (23 Avril 2013)
 * 
 * 1) Requête simple vers la page de login, imdb génère des champs hidden + des cookies.
 *    Les deux sont indispensables pour éviter les sécurités.
 * 
 * 2) Envoi d'une requête avec les données de connexion + les champs hidden et les cookies précédement récupéré.
 * 
 * 3) Il suffit d'utilisé les cookies précédement générées pour être identifié ...
 * 
 * */

// 1)

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://secure.imdb.com/register-imdb/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent=Mozilla/5.0 (X11; Linux x86_64; rv:20.0) Gecko/20100101 Firefox/20.0" );
curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/www/thal/ogmrip/tmp/cookies/imdb_cookies.txt");
curl_setopt($ch, CURLOPT_VERBOSE, false);
$rs=curl_exec($ch);
curl_close($ch);
$tabs=preg_split('#<h3>Sign in with IMDb</h3>\s*<input type\="hidden" name\="(.+)" value="(.+)" />#iUs', $rs, -1, PREG_SPLIT_DELIM_CAPTURE);
$token_key=$tabs[1];
$token_value=$tabs[2];

echo "Token key = <b>".$token_key."</b><br />Token value = <b>".$token_value."</b><br />";


// 2)
$datas=array();
$datas[$token_key]=$token_value;
$datas['login']='Thales0796';
$datas['password']=$imdb__pass;

print_r($datas);

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://secure.imdb.com/register-imdb/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/www/thal/ogmrip/tmp/cookies/imdb_cookies2.txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/www/thal/ogmrip/tmp/cookies/imdb_cookies.txt");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent=Mozilla/5.0 (X11; Linux x86_64; rv:20.0) Gecko/20100101 Firefox/20.0" );
curl_setopt($ch, CURLOPT_VERBOSE, false);
$rs=curl_exec($ch);

echo "Connecté à imdb ...";
// echo $rs;
