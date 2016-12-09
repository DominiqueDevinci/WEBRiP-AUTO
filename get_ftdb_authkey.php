<?php
$FTDB_apikey="API_000CC38544B5DD9022";
$datas=array();
$datas['api_key']=$FTDB_apikey;
$datas['torrent']="@".'/home/bot/The.Kingdom.2007.VOSTFR.BRRiP.x264.AC3-CiRAR.torrent';
$ch = curl_init('http://www.frenchtorrentdb.com/?section=SERVICES&module=mod_api_ftdbup');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datas); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_HEADER, 1);
preg_match('/^Set-Cookie: (.*?);/m', curl_exec($ch), $m);
print_r($m);
