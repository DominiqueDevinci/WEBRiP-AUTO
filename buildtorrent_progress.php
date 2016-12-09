<?php
include('/vars.php');
$tmp=file_get_contents($dir_logs.'up.logs2');
$tab=preg_split("#(\[.{50}\])#iUs", $tmp, -1, PREG_SPLIT_DELIM_CAPTURE);
echo (count($tab)-3);
