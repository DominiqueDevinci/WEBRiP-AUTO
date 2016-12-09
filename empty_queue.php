<?php
$f=fopen('/home/www/thal/ogmrip/queue.txt', 'w+');
fclose($f);
shell_exec("killall hb");
shell_exec("killall hb");
header('Location: rip.php');
