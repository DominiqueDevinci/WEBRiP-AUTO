<?php
/*$tmp=nl2br(shell_exec("mediainfo ".(urldecode(urldecode($_GET['file'])))));
echo '<h2 style="color:red;font-weight:bold;" >'.urldecode(urldecode($_GET['file'])).'</h2>
<div id="mediainfo" style="overflow:scroll; >'.$tmp.'</div>';
*/
echo '<div id="mediainfo" style="overflow:scroll;" >'.nl2br(shell_exec("mediainfo \"".urldecode(urldecode($_GET['file']))."\"")).'</div>';
